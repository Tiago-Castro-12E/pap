<?php

$servidor = "localhost";
$utilizador = "root";
$password = "";
$baseDados = "banco_ideias";

$ligacao = mysqli_connect($servidor, $utilizador, $password, $baseDados);

if (!$ligacao) {
    error_log("Erro ao ligar à base de dados: " . mysqli_connect_error());
    exit("Não foi possível ligar à base de dados. Tenta novamente mais tarde.");
}

mysqli_set_charset($ligacao, "utf8mb4");

?>
