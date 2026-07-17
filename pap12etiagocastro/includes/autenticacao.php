<?php

function iniciarSessaoSegura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $usaHttps = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";

    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "secure" => $usaHttps,
        "httponly" => true,
        "samesite" => "Lax",
    ]);

    session_start();
}

function urlBase(): string
{
    $diretorio = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? ""));
    $diretorio = preg_replace("#/admin$#", "", $diretorio);

    return ($diretorio === "/" || $diretorio === ".") ? "" : rtrim($diretorio, "/");
}

function redirecionar(string $caminho): void
{
    header("Location: " . urlBase() . "/" . ltrim($caminho, "/"));
    exit();
}

function utilizadorAutenticado(): bool
{
    return isset($_SESSION["id_utilizador"]);
}

function utilizadorAdministrador(): bool
{
    return utilizadorAutenticado() && ($_SESSION["tipo"] ?? "") === "admin";
}

function exigirLogin(): void
{
    if (!utilizadorAutenticado()) {
        redirecionar("login.php");
    }
}

function exigirAdministrador(): void
{
    if (!utilizadorAdministrador()) {
        http_response_code(403);
        exit("Acesso não autorizado.");
    }
}

function tokenCsrf(): string
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function tokenCsrfValido(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION["csrf_token"])
        && hash_equals($_SESSION["csrf_token"], $token);
}

function escapar(?string $valor): string
{
    return htmlspecialchars($valor ?? "", ENT_QUOTES, "UTF-8");
}

iniciarSessaoSegura();

if (!headers_sent()) {
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}

$paginaAutenticacao = basename($_SERVER["SCRIPT_NAME"] ?? "");
if (utilizadorAutenticado()
    && (int) ($_SESSION["forcar_troca_senha"] ?? 0) === 1
    && !in_array($paginaAutenticacao, ["alterar-password.php", "logout.php"], true)) {
    redirecionar("alterar-password.php");
}
