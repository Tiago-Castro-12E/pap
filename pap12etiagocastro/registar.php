<?php

session_start();
include "includes/ligaBD.php";

$mensagem = "";
$erro = "";

if(isset($_POST["registar"])){

    $nome = mysqli_real_escape_string($ligacao,$_POST["nome"]);
    $email = mysqli_real_escape_string($ligacao,$_POST["email"]);
    $senha = mysqli_real_escape_string($ligacao,$_POST["senha"]);
    $tipo = "aluno";

    $verificar = mysqli_query($ligacao,"SELECT * FROM utilizador WHERE email='$email'");

    if(mysqli_num_rows($verificar)>0){

        $erro = "Este email já se encontra registado.";

    }else{

        $sql="INSERT INTO utilizador(nome,email,senha,tipo)
        VALUES('$nome','$email','$senha','$tipo')";

        if(mysqli_query($ligacao,$sql)){

            $mensagem="Conta criada com sucesso! Já pode iniciar sessão.";

        }else{

            $erro="Ocorreu um erro ao criar a conta.";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="pt">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Registar</title>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include "includes/menu.php"; ?>

<section class="page">

<div class="login-box">

<h1>Criar Conta</h1>

<?php

if($erro!=""){

echo "<div class='erro'>$erro</div>";

}

if($mensagem!=""){

echo "<div class='sucesso'>$mensagem</div>";

}

?>

<form method="POST">

<label>Nome</label>

<input
type="text"
name="nome"
required>

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
name="registar">

Registar

</button>

</form>

<p>

Já tens conta?

<a href="login.php">

Iniciar Sessão

</a>

</p>

</div>

</section>

<?php include "includes/footer.php"; ?>

</body>

</html>