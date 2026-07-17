<?php

require_once __DIR__ . "/includes/autenticacao.php";
require_once __DIR__ . "/includes/ligaBD.php";

if (utilizadorAutenticado()) {
    redirecionar("index.php");
}

$erro = "";
$email = "";
$registoConcluido = ($_GET["registo"] ?? "") === "sucesso";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) {
        $erro = "O formulário expirou. Atualiza a página e tenta novamente.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === "") {
        $erro = "Email ou palavra-passe inválidos.";
    } else {
        $sql = "SELECT id_utilizador, nome, email, senha, tipo, ativo
                FROM utilizador
                WHERE email = ?
                LIMIT 1";
        $stmt = mysqli_prepare($ligacao, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $utilizador = mysqli_fetch_assoc($resultado) ?: null;
            mysqli_stmt_close($stmt);

            if ($utilizador
                && (int) $utilizador["ativo"] === 1
                && password_verify($senha, $utilizador["senha"])) {
                session_regenerate_id(true);

                $_SESSION["id_utilizador"] = (int) $utilizador["id_utilizador"];
                $_SESSION["nome"] = $utilizador["nome"];
                $_SESSION["tipo"] = $utilizador["tipo"];
                unset($_SESSION["csrf_token"]);

                redirecionar("index.php");
            }
        } else {
            error_log("Não foi possível preparar a consulta de login: " . mysqli_error($ligacao));
        }

        $erro = "Email ou palavra-passe inválidos.";
    }
}

$tituloPagina = "Iniciar sessão | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>

<main class="page">
    <div class="login-box">
        <h1>Iniciar sessão</h1>

        <?php if ($registoConcluido): ?>
            <div class="sucesso" role="status">Conta criada com sucesso. Já podes iniciar sessão.</div>
        <?php endif; ?>

        <?php if ($erro !== ""): ?>
            <div class="erro" role="alert"><?php echo escapar($erro); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo escapar($email); ?>"
                autocomplete="email"
                maxlength="191"
                required>

            <label for="senha">Palavra-passe</label>
            <input
                type="password"
                id="senha"
                name="senha"
                autocomplete="current-password"
                required>

            <button type="submit">Entrar</button>
        </form>

        <p>Ainda não tens conta? <a href="<?php echo $baseUrl; ?>/registar.php">Regista-te</a></p>
    </div>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
