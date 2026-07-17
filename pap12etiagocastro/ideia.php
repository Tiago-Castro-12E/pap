<?php

require_once __DIR__ . "/includes/autenticacao.php";
require_once __DIR__ . "/includes/ligaBD.php";

$idIdeia = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT) ?: 0;

if ($idIdeia <= 0) {
    http_response_code(404);
    $ideia = null;
} else {
    $sql = "SELECT i.id_ideia, i.titulo, i.descricao, i.estado, i.data_submissao,
                   i.data_atualizacao, i.id_utilizador, u.nome AS autor, c.nome_categoria,
                   (SELECT COUNT(*) FROM voto v WHERE v.id_ideia = i.id_ideia) AS total_votos,
                   (SELECT COUNT(*) FROM comentario co WHERE co.id_ideia = i.id_ideia) AS total_comentarios
            FROM ideia i
            JOIN utilizador u ON u.id_utilizador = i.id_utilizador
            JOIN categoria c ON c.id_categoria = i.id_categoria
            WHERE i.id_ideia = ?
            LIMIT 1";
    $stmt = mysqli_prepare($ligacao, $sql);
    $ideia = null;

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $idIdeia);
        mysqli_stmt_execute($stmt);
        $ideia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
        mysqli_stmt_close($stmt);
    } else {
        error_log("Erro ao carregar detalhe da ideia: " . mysqli_error($ligacao));
    }

    $publica = $ideia && in_array($ideia["estado"], ["Aprovada", "Implementada"], true);
    $propria = $ideia && utilizadorAutenticado()
        && (int) $_SESSION["id_utilizador"] === (int) $ideia["id_utilizador"];

    if (!$ideia || (!$publica && !$propria && !utilizadorAdministrador())) {
        http_response_code(404);
        $ideia = null;
    }
}

$tituloPagina = $ideia ? $ideia["titulo"] . " | Banco de Ideias" : "Ideia não encontrada";
include __DIR__ . "/includes/menu.php";
?>

<main class="page">
    <div class="container content-narrow">
        <?php if (!$ideia): ?>
            <div class="empty-state">
                <h1>Ideia não encontrada</h1>
                <p>A ideia não existe ou não está disponível para consulta.</p>
                <a href="<?php echo $baseUrl; ?>/ideias.php" class="btn">Voltar às ideias</a>
            </div>
        <?php else: ?>
            <article class="idea-detail">
                <div class="idea-card-topline">
                    <span class="badge"><?php echo escapar($ideia["nome_categoria"]); ?></span>
                    <span class="badge <?php echo $ideia["estado"] === "Implementada" ? "badge-success" : ($ideia["estado"] === "Aprovada" ? "badge-info" : "badge-warning"); ?>">
                        <?php echo escapar($ideia["estado"]); ?>
                    </span>
                </div>

                <h1><?php echo escapar($ideia["titulo"]); ?></h1>
                <div class="idea-meta">
                    <span>Proposta por <strong><?php echo escapar($ideia["autor"]); ?></strong></span>
                    <time datetime="<?php echo escapar($ideia["data_submissao"]); ?>">
                        <?php echo escapar(date("d/m/Y", strtotime($ideia["data_submissao"]))); ?>
                    </time>
                </div>

                <?php if (!in_array($ideia["estado"], ["Aprovada", "Implementada"], true)): ?>
                    <div class="aviso" role="status">Esta ideia ainda não é pública. Consegues vê-la por seres o autor ou administrador.</div>
                <?php endif; ?>

                <div class="idea-description">
                    <?php echo nl2br(escapar($ideia["descricao"])); ?>
                </div>

                <div class="idea-stats detail-stats" aria-label="Participação na ideia">
                    <span>&#128077; <strong><?php echo (int) $ideia["total_votos"]; ?></strong> votos</span>
                    <span>&#128172; <strong><?php echo (int) $ideia["total_comentarios"]; ?></strong> comentários</span>
                </div>

                <a href="<?php echo $baseUrl; ?>/ideias.php" class="back-link">&larr; Voltar às ideias</a>
            </article>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
