<?php
require_once __DIR__ . "/../includes/autenticacao.php"; exigirAdministrador();
require_once __DIR__ . "/../includes/ligaBD.php";
$tipos=["aluno","professor","admin"]; $erro="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $id=filter_var($_POST["id_utilizador"]??null,FILTER_VALIDATE_INT)?:0; $tipo=$_POST["tipo"]??""; $ativo=($_POST["ativo"]??"")==="1"?1:0;
 if(!tokenCsrfValido($_POST["csrf_token"]??null))$erro="O formulário expirou.";
 elseif($id<=0||!in_array($tipo,$tipos,true))$erro="Pedido inválido.";
 else {
  $stmt=mysqli_prepare($ligacao,"SELECT tipo,ativo FROM utilizador WHERE id_utilizador=?"); mysqli_stmt_bind_param($stmt,"i",$id); mysqli_stmt_execute($stmt); $alvo=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
  if(!$alvo)$erro="Utilizador inexistente.";
  elseif($alvo["tipo"]==="admin"&&(int)$alvo["ativo"]===1&&($tipo!=="admin"||$ativo===0)){
   $total=(int)mysqli_fetch_assoc(mysqli_query($ligacao,"SELECT COUNT(*) total FROM utilizador WHERE tipo='admin' AND ativo=1"))["total"];
   if($total<=1)$erro="Não é possível desativar ou despromover o último administrador ativo.";
  }
  if(!$erro){$stmt=mysqli_prepare($ligacao,"UPDATE utilizador SET tipo=?,ativo=? WHERE id_utilizador=?");mysqli_stmt_bind_param($stmt,"sii",$tipo,$ativo,$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);if($id===(int)$_SESSION["id_utilizador"]){$_SESSION["tipo"]=$tipo;if(!$ativo){$_SESSION=[];session_destroy();redirecionar("index.php");}}redirecionar("admin/utilizadores.php?estado=alterado");}
 }
}
$utilizadores=[];$r=mysqli_query($ligacao,"SELECT id_utilizador,nome,email,tipo,ativo,data_criacao FROM utilizador ORDER BY data_criacao DESC");while($u=mysqli_fetch_assoc($r))$utilizadores[]=$u;
$tituloPagina="Utilizadores | Administração";include __DIR__."/../includes/menu.php";
?>
<main class="page"><div class="container"><div class="page-heading heading-actions"><div><span class="eyebrow">Administração</span><h1>Utilizadores</h1></div><a class="btn" href="<?php echo $baseUrl;?>/admin/dashboard.php">Dashboard</a></div><?php if(($_GET["estado"]??"")==="alterado"):?><div class="sucesso">Utilizador atualizado.</div><?php endif;?><?php if($erro):?><div class="erro"><?php echo escapar($erro);?></div><?php endif;?>
<div class="table-responsive"><table><thead><tr><th>Nome</th><th>Email</th><th>Tipo</th><th>Estado</th><th>Ação</th></tr></thead><tbody><?php foreach($utilizadores as $u):?><tr><td><?php echo escapar($u["nome"]);?></td><td><?php echo escapar($u["email"]);?></td><td colspan="3"><form method="post" class="inline-form user-form" onsubmit="return confirm('Confirmar alteração deste utilizador?')"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf());?>"><input type="hidden" name="id_utilizador" value="<?php echo (int)$u["id_utilizador"];?>"><select name="tipo" aria-label="Tipo"><?php foreach($tipos as $t):?><option value="<?php echo $t;?>" <?php echo $u["tipo"]===$t?"selected":"";?>><?php echo escapar(ucfirst($t));?></option><?php endforeach;?></select><select name="ativo" aria-label="Estado"><option value="1" <?php echo (int)$u["ativo"]===1?"selected":"";?>>Ativo</option><option value="0" <?php echo (int)$u["ativo"]===0?"selected":"";?>>Inativo</option></select><button class="btn-small" type="submit">Guardar</button></form></td></tr><?php endforeach;?></tbody></table></div>
</div></main><?php include __DIR__."/../includes/footer.php";?>
