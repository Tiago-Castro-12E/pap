<?php

$servidor = getenv("DB_HOST") ?: "localhost";
$utilizador = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASSWORD") !== false ? getenv("DB_PASSWORD") : "";
$baseDados = getenv("DB_NAME") ?: "banco_ideias";

$ligacao = mysqli_connect($servidor, $utilizador, $password, $baseDados);

if (!$ligacao) {
    error_log("Erro ao ligar à base de dados: " . mysqli_connect_error());
    exit("Não foi possível ligar à base de dados. Tenta novamente mais tarde.");
}

mysqli_set_charset($ligacao, "utf8mb4");

?>
