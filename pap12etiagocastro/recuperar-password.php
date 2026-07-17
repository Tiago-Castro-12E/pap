<?php

require_once __DIR__ . "/includes/autenticacao.php";
require_once __DIR__ . "/includes/ligaBD.php";

if (utilizadorAutenticado()) {
    redirecionar("perfil.php");
}

$erro = "";
$enviado = ($_GET["estado"] ?? "") === "enviado";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");

    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) {
        $erro = "O formulário expirou. Atualiza a página e tenta novamente.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191) {
        $erro = "Introduz um endereço de email válido.";
    } else {
        $sql = "INSERT INTO pedido_recuperacao (id_utilizador)
                SELECT u.id_utilizador
                FROM utilizador u
                WHERE u.email = ? AND u.ativo = 1
                  AND NOT EXISTS (
                      SELECT 1 FROM pedido_recuperacao p
                      WHERE p.id_utilizador = u.id_utilizador AND p.estado = 'Pendente'
                  )";
        $stmt = mysqli_prepare($ligacao, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            if (!mysqli_stmt_execute($stmt)) {
                error_log("Erro ao criar pedido de recuperação: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Erro ao preparar pedido de recuperação: " . mysqli_error($ligacao));
        }

        unset($_SESSION["csrf_token"]);
        redirecionar("recuperar-password.php?estado=enviado");
    }
}

$tituloPagina = "Recuperar palavra-passe | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>
<main class="page"><div class="login-box">
    <h1>Recuperar palavra-passe</h1>
    <p class="form-intro">Envia o pedido e confirma depois a tua identidade junto de um administrador.</p>
    <?php if ($enviado): ?><div class="sucesso" role="status">Se existir uma conta ativa com esse email, o pedido foi enviado ao administrador.</div><?php endif; ?>
    <?php if ($erro): ?><div class="erro" role="alert"><?php echo escapar($erro); ?></div><?php endif; ?>
    <form method="post"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">
        <label for="email">Email da conta</label>
        <input type="email" id="email" name="email" maxlength="191" autocomplete="email" required>
        <button type="submit">Enviar pedido</button>
    </form>
    <p><a href="<?php echo $baseUrl; ?>/login.php">Voltar ao login</a></p>
</div></main>
<?php include __DIR__ . "/includes/footer.php"; ?>
