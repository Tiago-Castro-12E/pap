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

$tituloPagina = "O meu perfil | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>

<main class="page">
    <div class="container">
        <h1>O meu perfil</h1>

        <div class="perfil-card">
            <p><strong>Nome:</strong> <?php echo escapar($utilizador["nome"]); ?></p>
            <p><strong>Email:</strong> <?php echo escapar($utilizador["email"]); ?></p>
            <p><strong>Tipo:</strong> <?php echo escapar($tipoApresentado); ?></p>

            <form method="post" action="<?php echo $baseUrl; ?>/logout.php">
                <input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">
                <button type="submit" class="btn">Terminar sessão</button>
            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
