<?php
$tituloPagina = "Início | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>

<main>

<section class="hero">

<div class="container hero-container">

<div class="hero-text">

<span class="tag">
Banco de Ideias da Comunidade
</span>

<h1>

Transforma as tuas ideias em melhorias reais.

</h1>

<p>

Uma plataforma onde alunos, professores e comunidade podem apresentar
propostas para melhorar a escola e a freguesia.

</p>

<div class="hero-buttons">

<a href="<?php echo $baseUrl; ?>/ideias.php" class="btn btn-primary">

Explorar Ideias

</a>

<a href="<?php echo $baseUrl; ?>/submeter.php" class="btn btn-outline">

Submeter Ideia

</a>

</div>

</div>

</div>

</section>

<section class="features">

<div class="container">

<div class="feature">

<div class="icon" aria-hidden="true">&#128161;</div>

<h3>

Partilha

</h3>

<p>

Submete ideias para melhorar a escola e a comunidade.

</p>

</div>

<div class="feature">

<div class="icon" aria-hidden="true">&#128077;</div>

<h3>

Vota

</h3>

<p>

Apoia as melhores ideias apresentadas pelos utilizadores.

</p>

</div>

<div class="feature">

<div class="icon" aria-hidden="true">&#128172;</div>

<h3>

Comenta

</h3>

<p>

Participa na discussão e ajuda a melhorar cada proposta.

</p>

</div>

</div>

</section>

<section class="frase">

<div class="container">

<h2>

"As melhores mudanças começam sempre por uma boa ideia."

</h2>

</div>

</section>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
