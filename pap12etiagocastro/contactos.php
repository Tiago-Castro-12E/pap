<?php
require_once __DIR__."/includes/autenticacao.php"; require_once __DIR__."/includes/ligaBD.php";
$erros=[];$nome="";$email="";$assunto="";$mensagem="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $nome=trim($_POST["nome"]??"");$email=trim($_POST["email"]??"");$assunto=trim($_POST["assunto"]??"");$mensagem=trim($_POST["mensagem"]??"");
 $len=static fn($v)=>function_exists("mb_strlen")?mb_strlen($v):strlen($v);
 if(!tokenCsrfValido($_POST["csrf_token"]??null))$erros[]="O formulário expirou.";
 if($len($nome)<2||$len($nome)>100)$erros[]="O nome deve ter entre 2 e 100 caracteres.";
 if(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>191)$erros[]="Introduz um email válido.";
 if($len($assunto)<3||$len($assunto)>150)$erros[]="O assunto deve ter entre 3 e 150 caracteres.";
 if($len($mensagem)<10||$len($mensagem)>5000)$erros[]="A mensagem deve ter entre 10 e 5000 caracteres.";
 if(!$erros){$stmt=mysqli_prepare($ligacao,"INSERT INTO mensagem_contacto(nome,email,assunto,mensagem) VALUES(?,?,?,?)");mysqli_stmt_bind_param($stmt,"ssss",$nome,$email,$assunto,$mensagem);if(mysqli_stmt_execute($stmt)){mysqli_stmt_close($stmt);unset($_SESSION["csrf_token"]);redirecionar("contactos.php?estado=enviada");}error_log("Erro ao guardar contacto: ".mysqli_stmt_error($stmt));mysqli_stmt_close($stmt);$erros[]="Não foi possível enviar a mensagem.";}
}
$tituloPagina="Contactos | Banco de Ideias";include __DIR__."/includes/menu.php";
?>
<main class="page"><div class="container content-narrow"><div class="page-heading"><span class="eyebrow">Contactos</span><h1>Fala connosco</h1><p>Usa este formulário para enviar dúvidas ou sugestões relacionadas com a plataforma.</p></div>
<?php if(($_GET["estado"]??"")==="enviada"):?><div class="sucesso" role="status">Mensagem enviada e guardada para análise.</div><?php endif;?><?php if($erros):?><div class="erro" role="alert"><ul><?php foreach($erros as $e):?><li><?php echo escapar($e);?></li><?php endforeach;?></ul></div><?php endif;?>
<form method="post" class="form-card"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf());?>"><label for="nome">Nome</label><input id="nome" name="nome" value="<?php echo escapar($nome);?>" maxlength="100" required><label for="email">Email</label><input type="email" id="email" name="email" value="<?php echo escapar($email);?>" maxlength="191" required><label for="assunto">Assunto</label><input id="assunto" name="assunto" value="<?php echo escapar($assunto);?>" maxlength="150" required><label for="mensagem">Mensagem</label><textarea id="mensagem" name="mensagem" maxlength="5000" required><?php echo escapar($mensagem);?></textarea><button type="submit">Enviar mensagem</button></form>
</div></main><?php include __DIR__."/includes/footer.php";?>
