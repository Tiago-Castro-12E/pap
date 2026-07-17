<?php
require_once __DIR__ . "/../includes/autenticacao.php";
exigirAdministrador();
require_once __DIR__ . "/../includes/ligaBD.php";

$totais = mysqli_fetch_assoc(mysqli_query($ligacao, "SELECT
 (SELECT COUNT(*) FROM utilizador WHERE ativo=1) utilizadores,
 (SELECT COUNT(*) FROM categoria WHERE ativa=1) categorias,
 (SELECT COUNT(*) FROM ideia) ideias,
 (SELECT COUNT(*) FROM voto) votos,
 (SELECT COUNT(*) FROM comentario) comentarios,
 (SELECT COUNT(*) FROM pedido_recuperacao WHERE estado='Pendente') recuperacoes"));
$estados = [];
$resultado = mysqli_query($ligacao, "SELECT estado, COUNT(*) total FROM ideia GROUP BY estado");
while ($linha = mysqli_fetch_assoc($resultado)) { $estados[$linha["estado"]] = (int) $linha["total"]; }

$tituloPagina = "Dashboard | Administração";
include __DIR__ . "/../includes/menu.php";
?>
<main class="page"><div class="container">
<div class="page-heading"><span class="eyebrow">Administração</span><h1>Dashboard</h1><p>Visão geral da participação na plataforma.</p></div>
<div class="dashboard-grid">
<?php foreach (["Utilizadores ativos"=>$totais["utilizadores"],"Categorias ativas"=>$totais["categorias"],"Ideias"=>$totais["ideias"],"Votos"=>$totais["votos"],"Comentários"=>$totais["comentarios"],"Recuperações pendentes"=>$totais["recuperacoes"]] as $rotulo=>$total): ?><article class="stat-card"><strong><?php echo (int)$total; ?></strong><span><?php echo escapar($rotulo); ?></span></article><?php endforeach; ?>
</div>
<section class="admin-section"><h2>Ideias por estado</h2><div class="dashboard-grid"><?php foreach (["Pendente","Aprovada","Implementada","Rejeitada"] as $estado): ?><article class="stat-card compact"><strong><?php echo $estados[$estado] ?? 0; ?></strong><span><?php echo escapar($estado); ?></span></article><?php endforeach; ?></div></section>
<nav class="admin-links" aria-label="Gestão"><a class="card" href="<?php echo $baseUrl; ?>/admin/ideias.php">Moderar ideias</a><a class="card" href="<?php echo $baseUrl; ?>/admin/categorias.php">Gerir categorias</a><a class="card" href="<?php echo $baseUrl; ?>/admin/utilizadores.php">Gerir utilizadores</a><a class="card" href="<?php echo $baseUrl; ?>/admin/recuperacoes.php">Recuperar passwords</a><a class="card" href="<?php echo $baseUrl; ?>/admin/mensagens.php">Mensagens</a></nav>
</div></main>
<?php include __DIR__ . "/../includes/footer.php"; ?>
