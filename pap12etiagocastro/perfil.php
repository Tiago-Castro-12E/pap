<?php

include "includes/verificarLogin.php";
include "includes/ligaBD.php";
include "includes/menu.php";

$id = $_SESSION["id_utilizador"];

$sql = "SELECT * FROM utilizador WHERE id_utilizador='$id'";

$resultado = mysqli_query($ligacao,$sql);

$utilizador = mysqli_fetch_assoc($resultado);

?>

<section class="page">

<div class="container">

<h1>O Meu Perfil</h1>

<div class="perfil-card">

<p><strong>Nome:</strong> <?php echo $utilizador['nome']; ?></p>

<p><strong>Email:</strong> <?php echo $utilizador['email']; ?></p>

<p><strong>Tipo:</strong> <?php echo ucfirst($utilizador['tipo']); ?></p>

<a href="logout.php" class="btn">Terminar Sessão</a>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>