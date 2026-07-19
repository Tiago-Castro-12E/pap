<?php

require_once __DIR__ . "/includes/verificarLogin.php";
require_once __DIR__ . "/includes/ligaBD.php";

$idUtilizador = (int) $_SESSION["id_utilizador"];
$stmt = mysqli_prepare(
    $ligacao,
    "SELECT id_utilizador, nome, email, tipo, ativo, data_criacao
     FROM utilizador
     WHERE id_utilizador = ?
     LIMIT 1"
);

if (!$stmt) {
    error_log("Não foi possível preparar a consulta do perfil: " . mysqli_error($ligacao));
    http_response_code(500);
    exit("Não foi possível carregar o perfil.");
}

mysqli_stmt_bind_param($stmt, "i", $idUtilizador);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$utilizador = mysqli_fetch_assoc($resultado) ?: null;
mysqli_stmt_close($stmt);

if (!$utilizador || (int) $utilizador["ativo"] !== 1) {
    $_SESSION = [];
    session_destroy();
    redirecionar("login.php");
}

$tiposUtilizador = [
    "aluno" => "Aluno",
    "professor" => "Professor",
    "admin" => "Administrador",
];
$tipoApresentado = $tiposUtilizador[$utilizador["tipo"]] ?? "Utilizador";

$minhasIdeias = [];
$stmt = mysqli_prepare(
    $ligacao,
    "SELECT i.id_ideia, i.titulo, i.estado, i.data_submissao, c.nome_categoria,
            (SELECT COUNT(*) FROM voto v WHERE v.id_ideia = i.id_ideia) AS total_votos,
            (SELECT COUNT(*) FROM comentario co WHERE co.id_ideia = i.id_ideia) AS total_comentarios
     FROM ideia i
     JOIN categoria c ON c.id_categoria = i.id_categoria
     WHERE i.id_utilizador = ?
     ORDER BY i.data_submissao DESC"
);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $idUtilizador);
    mysqli_stmt_execute($stmt);
    $resultadoIdeias = mysqli_stmt_get_result($stmt);
    while ($ideia = mysqli_fetch_assoc($resultadoIdeias)) {
        $minhasIdeias[] = $ideia;
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Não foi possível carregar as ideias do perfil: " . mysqli_error($ligacao));
}

$tituloPagina = "O meu perfil | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>

<main class="page">
    <div class="container">
        <h1>O meu perfil</h1>

        <?php if (($_GET["estado"] ?? "") === "password-alterada"): ?>
            <div class="sucesso" role="status">Palavra-passe alterada com sucesso.</div>
        <?php endif; ?>

        <div class="perfil-card">
            <p><strong>Nome:</strong> <?php echo escapar($utilizador["nome"]); ?></p>
            <p><strong>Email:</strong> <?php echo escapar($utilizador["email"]); ?></p>
            <p><strong>Tipo:</strong> <?php echo escapar($tipoApresentado); ?></p>

            <form method="post" action="<?php echo $baseUrl; ?>/logout.php">
                <input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">
                <button type="submit" class="btn">Terminar sessão</button>
            </form>
        </div>

        <section class="profile-ideas" aria-labelledby="minhas-ideias-titulo">
            <div class="section-heading heading-actions">
                <div>
                    <span class="eyebrow">Participação</span>
                    <h2 id="minhas-ideias-titulo">As minhas ideias</h2>
                    <p>Acompanha o estado e a participação nas propostas que submeteste.</p>
                </div>
                <a href="<?php echo $baseUrl; ?>/submeter.php" class="btn">Nova ideia</a>
            </div>

            <?php if (!$minhasIdeias): ?>
                <div class="empty-state">
                    <h2>Ainda não submeteste ideias</h2>
                    <p>Quando apresentares uma proposta, ela aparecerá aqui.</p>
                    <a href="<?php echo $baseUrl; ?>/submeter.php" class="btn">Submeter a primeira ideia</a>
                </div>
            <?php else: ?>
                <div class="ideas-grid profile-ideas-grid">
                    <?php foreach ($minhasIdeias as $ideia): ?>
                        <?php
                        $classeEstado = match ($ideia["estado"]) {
                            "Aprovada" => "badge-info",
                            "Implementada" => "badge-success",
                            "Rejeitada" => "badge-muted",
                            default => "badge-warning",
                        };
                        ?>
                        <article class="idea-card">
                            <div class="idea-card-topline">
                                <span class="badge"><?php echo escapar($ideia["nome_categoria"]); ?></span>
                                <span class="badge <?php echo $classeEstado; ?>"><?php echo escapar($ideia["estado"]); ?></span>
                            </div>
                            <h2><a href="<?php echo $baseUrl; ?>/ideia.php?id=<?php echo (int) $ideia["id_ideia"]; ?>"><?php echo escapar($ideia["titulo"]); ?></a></h2>
                            <div class="idea-meta">
                                <time datetime="<?php echo escapar($ideia["data_submissao"]); ?>">
                                    Submetida em <?php echo date("d/m/Y", strtotime($ideia["data_submissao"])); ?>
                                </time>
                            </div>
                            <div class="idea-stats" aria-label="Participação na ideia">
                                <span>&#128077; <?php echo (int) $ideia["total_votos"]; ?> votos</span>
                                <span>&#128172; <?php echo (int) $ideia["total_comentarios"]; ?> comentários</span>
                            </div>
                            <a class="card-link" href="<?php echo $baseUrl; ?>/ideia.php?id=<?php echo (int) $ideia["id_ideia"]; ?>">Ver detalhes</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
