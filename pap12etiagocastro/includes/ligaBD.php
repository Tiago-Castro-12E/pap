<?php

$servidor = "localhost";
$utilizador = "root";
$password = "";
$baseDados = "banco_ideias";

$ligacao = mysqli_connect($servidor, $utilizador, $password, $baseDados);

if (!$ligacao) {
    die("Erro ao ligar à base de dados: " . mysqli_connect_error());
}

mysqli_set_charset($ligacao, "utf8");

?>