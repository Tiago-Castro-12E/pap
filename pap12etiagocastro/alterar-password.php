<?php

require_once __DIR__ . "/includes/autenticacao.php";
exigirLogin();
require_once __DIR__ . "/includes/ligaBD.php";

if ((int) ($_SESSION["forcar_troca_senha"] ?? 0) !== 1) {
    redirecionar("perfil.php");
}

$erros = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $senha = $_POST["senha"] ?? "";
    $confirmacao = $_POST["confirmar_senha"] ?? "";

    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) $erros[] = "O formulário expirou.";
    if (strlen($senha) < 8) $erros[] = "A palavra-passe deve ter pelo menos 8 caracteres.";
    if ($senha !== $confirmacao) $erros[] = "A confirmação não corresponde.";

    if (!$erros) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $id = (int) $_SESSION["id_utilizador"];
        $stmt = mysqli_prepare($ligacao, "UPDATE utilizador SET senha = ?, forcar_troca_senha = 0 WHERE id_utilizador = ? AND ativo = 1");
        mysqli_stmt_bind_param($stmt, "si", $hash, $id);

        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1) {
            mysqli_stmt_close($stmt);
            $_SESSION["forcar_troca_senha"] = 0;
            unset($_SESSION["csrf_token"]);
            session_regenerate_id(true);
            redirecionar("perfil.php?estado=password-alterada");
        }
        error_log("Erro ao alterar palavra-passe obrigatória: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        $erros[] = "Não foi possível alterar a palavra-passe.";
    }
}

$tituloPagina = "Definir nova palavra-passe | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>
<main class="page"><div class="login-box">
    <h1>Define uma nova palavra-passe</h1>
    <div class="aviso">Entraste com uma palavra-passe temporária. Tens de a substituir antes de continuar.</div>
    <?php if ($erros): ?><div class="erro" role="alert"><ul><?php foreach ($erros as $erro): ?><li><?php echo escapar($erro); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">
        <label for="senha">Nova palavra-passe</label><input type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" required>
        <label for="confirmar_senha">Confirmar nova palavra-passe</label><input type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" autocomplete="new-password" required>
        <button type="submit">Guardar nova palavra-passe</button>
    </form>
</div></main>
<?php include __DIR__ . "/includes/footer.php"; ?>
