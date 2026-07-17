<?php
require_once __DIR__ . "/../includes/autenticacao.php";
exigirAdministrador();
require_once __DIR__ . "/../includes/ligaBD.php";

$estadosPermitidos = ["Pendente","Aprovada","Rejeitada","Implementada"];
$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_var($_POST["id_ideia"] ?? null, FILTER_VALIDATE_INT) ?: 0;
    $novoEstado = $_POST["estado"] ?? "";
    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) $erro = "O formulário expirou.";
    elseif ($id <= 0 || !in_array($novoEstado, $estadosPermitidos, true)) $erro = "Pedido inválido.";
    else {
        $stmt = mysqli_prepare($ligacao, "UPDATE ideia SET estado=? WHERE id_ideia=?");
        mysqli_stmt_bind_param($stmt, "si", $novoEstado, $id);
        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) >= 0) { mysqli_stmt_close($stmt); redirecionar("admin/ideias.php?estado=alterada"); }
        $erro = "Não foi possível alterar a ideia."; mysqli_stmt_close($stmt);
    }
}
$filtro = $_GET["filtro"] ?? "";
if (!in_array($filtro, $estadosPermitidos, true)) $filtro = "";
$sql = "SELECT i.id_ideia,i.titulo,i.estado,i.data_submissao,u.nome autor,c.nome_categoria FROM ideia i JOIN utilizador u ON u.id_utilizador=i.id_utilizador JOIN categoria c ON c.id_categoria=i.id_categoria WHERE (?='' OR i.estado=?) ORDER BY i.data_submissao DESC";
$stmt=mysqli_prepare($ligacao,$sql); mysqli_stmt_bind_param($stmt,"ss",$filtro,$filtro); mysqli_stmt_execute($stmt); $resultado=mysqli_stmt_get_result($stmt); $ideias=[]; while($i=mysqli_fetch_assoc($resultado))$ideias[]=$i; mysqli_stmt_close($stmt);
$tituloPagina="Moderar ideias | Administração"; include __DIR__."/../includes/menu.php";
?>
<main class="page"><div class="container"><div class="page-heading heading-actions"><div><span class="eyebrow">Administração</span><h1>Moderar ideias</h1></div><a class="btn" href="<?php echo $baseUrl; ?>/admin/dashboard.php">Dashboard</a></div>
<?php if(($_GET["estado"]??"")==="alterada"):?><div class="sucesso">Estado atualizado.</div><?php endif;?><?php if($erro):?><div class="erro"><?php echo escapar($erro);?></div><?php endif;?>
<form method="get" class="filters-card admin-filter"><div class="field-group"><label for="filtro">Estado</label><select id="filtro" name="filtro"><option value="">Todos</option><?php foreach($estadosPermitidos as $e):?><option value="<?php echo escapar($e);?>" <?php echo $filtro===$e?"selected":"";?>><?php echo escapar($e);?></option><?php endforeach;?></select></div><button type="submit">Filtrar</button></form>
<div class="table-responsive"><table><thead><tr><th>Ideia</th><th>Autor</th><th>Categoria</th><th>Estado</th><th>Ações</th></tr></thead><tbody><?php foreach($ideias as $i):?><tr><td><a href="<?php echo $baseUrl;?>/ideia.php?id=<?php echo (int)$i["id_ideia"];?>"><?php echo escapar($i["titulo"]);?></a></td><td><?php echo escapar($i["autor"]);?></td><td><?php echo escapar($i["nome_categoria"]);?></td><td><?php echo escapar($i["estado"]);?></td><td><form method="post" class="inline-form" onsubmit="return confirm('Confirmar alteração do estado?')"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf());?>"><input type="hidden" name="id_ideia" value="<?php echo (int)$i["id_ideia"];?>"><select name="estado" aria-label="Novo estado"><?php foreach($estadosPermitidos as $e):?><option value="<?php echo escapar($e);?>" <?php echo $i["estado"]===$e?"selected":"";?>><?php echo escapar($e);?></option><?php endforeach;?></select><button class="btn-small" type="submit">Guardar</button></form></td></tr><?php endforeach;?></tbody></table></div>
</div></main><?php include __DIR__."/../includes/footer.php";?>
