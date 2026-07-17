<?php

session_start();
include "includes/ligaBD.php";

$erro = "";

if(isset($_POST["login"])){

    $email = mysqli_real_escape_string($ligacao, $_POST["email"]);
    $senha = mysqli_real_escape_string($ligacao, $_POST["senha"]);

    $sql = "SELECT * FROM utilizador WHERE email='$email'";

    $resultado = mysqli_query($ligacao, $sql);

    if(mysqli_num_rows($resultado) == 1){

        $utilizador = mysqli_fetch_assoc($resultado);

        if($senha == $utilizador["senha"]){

            $_SESSION["id_utilizador"] = $utilizador["id_utilizador"];
            $_SESSION["nome"] = $utilizador["nome"];
            $_SESSION["tipo"] = $utilizador["tipo"];

            header("Location: index.php");
            exit();

        }else{

            $erro = "Palavra-passe incorreta.";

        }

    }else{

        $erro = "O utilizador não existe.";

    }

}

?>

<!DOCTYPE html>

<html lang="pt">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login</title>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include "includes/menu.php"; ?>

<section class="page">

<div class="login-box">

<h1>Iniciar Sessão</h1>

<?php

if($erro!=""){

echo "<div class='erro'>$erro</div>";

}

?>

<form method="POST">

<label>Email</label>

<input
type="email"
name="email"
required>

<label>Palavra-passe</label>

<input
type="password"
name="senha"
required>

<button
type="submit"
name="login">

Entrar

</button>

</form>

<p>

Ainda não tens conta?

<a href="registar.php">

Regista-te

</a>

</p>

</div>

</section>

<?php include "includes/footer.php"; ?>

</body>

</html>