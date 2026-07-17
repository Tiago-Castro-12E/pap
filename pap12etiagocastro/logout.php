<?php

require_once __DIR__ . "/includes/autenticacao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    exit("Método não permitido.");
}

if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) {
    http_response_code(403);
    exit("Pedido inválido ou expirado.");
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $parametros = session_get_cookie_params();
    setcookie(
        session_name(),
        "",
        time() - 42000,
        $parametros["path"],
        $parametros["domain"],
        $parametros["secure"],
        $parametros["httponly"]
    );
}

session_destroy();
redirecionar("index.php");

