<?php

require_once __DIR__ . "/autenticacao.php";

$tituloPagina = $tituloPagina ?? "Banco de Ideias da Comunidade";
$classeBody = $classeBody ?? "";

$baseUrl = urlBase();
$utilizadorAutenticado = utilizadorAutenticado();
$administrador = utilizadorAdministrador();
$nomeSessao = escapar($_SESSION["nome"] ?? "Utilizador");
$paginaAtual = basename($_SERVER["SCRIPT_NAME"] ?? "");
$paginaNavegacao = $paginaAtual === "ideia.php" ? "ideias.php" : $paginaAtual;

function classeLinkAtivo(string $ficheiro, string $paginaAtual): string
{
    return $ficheiro === $paginaAtual ? ' class="ativo" aria-current="page"' : "";
}

?>
<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Banco de Ideias da Comunidade: apresenta, debate e apoia propostas para melhorar a escola e a comunidade.">
    <title><?php echo escapar($tituloPagina); ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/style.css">
</head>

<body class="<?php echo escapar($classeBody); ?>">

<header>
    <div class="container navbar">
        <div class="logo">
            <a href="<?php echo $baseUrl; ?>/index.php">Banco <span>de Ideias</span></a>
        </div>

        <nav aria-label="Navegação principal">
            <ul class="menu">
                <li><a href="<?php echo $baseUrl; ?>/index.php"<?php echo classeLinkAtivo("index.php", $paginaNavegacao); ?>>Início</a></li>
                <li><a href="<?php echo $baseUrl; ?>/sobre.php"<?php echo classeLinkAtivo("sobre.php", $paginaNavegacao); ?>>Sobre</a></li>
                <li><a href="<?php echo $baseUrl; ?>/ideias.php"<?php echo classeLinkAtivo("ideias.php", $paginaNavegacao); ?>>Ideias</a></li>
                <li><a href="<?php echo $baseUrl; ?>/submeter.php"<?php echo classeLinkAtivo("submeter.php", $paginaNavegacao); ?>>Submeter</a></li>
                <li><a href="<?php echo $baseUrl; ?>/contactos.php"<?php echo classeLinkAtivo("contactos.php", $paginaNavegacao); ?>>Contactos</a></li>
            </ul>
        </nav>

        <div class="navbar-actions">
            <?php if ($utilizadorAutenticado): ?>
                <?php if ($administrador): ?>
                    <a href="<?php echo $baseUrl; ?>/admin/dashboard.php" class="admin-link">Administração</a>
                <?php endif; ?>
                <a href="<?php echo $baseUrl; ?>/perfil.php" class="user-link">Olá, <?php echo $nomeSessao; ?></a>
                <form method="post" action="<?php echo $baseUrl; ?>/logout.php" class="logout-form">
                    <input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">
                    <button type="submit" class="btn-login logout-button">Sair</button>
                </form>
            <?php else: ?>
                <a href="<?php echo $baseUrl; ?>/registar.php" class="register-link">Registar</a>
                <a href="<?php echo $baseUrl; ?>/login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>
