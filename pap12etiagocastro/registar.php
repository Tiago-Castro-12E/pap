<?php

require_once __DIR__ . "/includes/autenticacao.php";
require_once __DIR__ . "/includes/ligaBD.php";

if (utilizadorAutenticado()) {
    redirecionar("index.php");
}

$erros = [];
$nome = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $confirmarSenha = $_POST["confirmar_senha"] ?? "";

    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) {
        $erros[] = "O formulário expirou. Atualiza a página e tenta novamente.";
    }

    $tamanhoNome = function_exists("mb_strlen") ? mb_strlen($nome) : strlen($nome);
    if ($tamanhoNome < 2 || $tamanhoNome > 100) {
        $erros[] = "O nome deve ter entre 2 e 100 caracteres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191) {
        $erros[] = "Introduz um endereço de email válido.";
    }

    if (strlen($senha) < 8) {
        $erros[] = "A palavra-passe deve ter pelo menos 8 caracteres.";
    }

    if ($senha !== $confirmarSenha) {
        $erros[] = "A confirmação da palavra-passe não corresponde.";
    }

    if (!$erros) {
        $stmt = mysqli_prepare($ligacao, "SELECT id_utilizador FROM utilizador WHERE email = ? LIMIT 1");

        if (!$stmt) {
            error_log("Não foi possível preparar a verificação do email: " . mysqli_error($ligacao));
            $erros[] = "Não foi possível criar a conta. Tenta novamente mais tarde.";
        } else {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $emailExiste = mysqli_fetch_assoc($resultado) !== null;
            mysqli_stmt_close($stmt);

            if ($emailExiste) {
                $erros[] = "Este email já se encontra registado.";
            }
        }
    }

    if (!$erros) {
        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
        $tipo = "aluno";
        $stmt = mysqli_prepare(
            $ligacao,
            "INSERT INTO utilizador (nome, email, senha, tipo) VALUES (?, ?, ?, ?)"
        );

        if (!$stmt || $hashSenha === false) {
            error_log("Não foi possível preparar o registo do utilizador: " . mysqli_error($ligacao));
            $erros[] = "Não foi possível criar a conta. Tenta novamente mais tarde.";
        } else {
            mysqli_stmt_bind_param($stmt, "ssss", $nome, $email, $hashSenha, $tipo);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                unset($_SESSION["csrf_token"]);
                redirecionar("login.php?registo=sucesso");
            }

            error_log("Não foi possível registar o utilizador: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            $erros[] = "Não foi possível criar a conta. Tenta novamente mais tarde.";
        }
    }
}

$tituloPagina = "Criar conta | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>

<main class="page">
    <div class="login-box">
        <h1>Criar conta</h1>

        <?php if ($erros): ?>
            <div class="erro" role="alert">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo escapar($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">

            <label for="nome">Nome</label>
            <input
                type="text"
                id="nome"
                name="nome"
                value="<?php echo escapar($nome); ?>"
                autocomplete="name"
                minlength="2"
                maxlength="100"
                required>

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
                autocomplete="new-password"
                minlength="8"
                required>

            <label for="confirmar_senha">Confirmar palavra-passe</label>
            <input
                type="password"
                id="confirmar_senha"
                name="confirmar_senha"
                autocomplete="new-password"
                minlength="8"
                required>

            <button type="submit">Registar</button>
        </form>

        <p>Já tens conta? <a href="<?php echo $baseUrl; ?>/login.php">Iniciar sessão</a></p>
    </div>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
