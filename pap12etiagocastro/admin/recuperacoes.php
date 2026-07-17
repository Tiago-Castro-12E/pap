<?php

require_once __DIR__ . "/../includes/autenticacao.php";
exigirAdministrador();
require_once __DIR__ . "/../includes/ligaBD.php";

$erro = "";
$passwordTemporaria = $_SESSION["password_temporaria"] ?? null;
unset($_SESSION["password_temporaria"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idPedido = filter_var($_POST["id_pedido"] ?? null, FILTER_VALIDATE_INT) ?: 0;
    $acao = $_POST["acao"] ?? "";

    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) {
        $erro = "O formulário expirou.";
    } elseif ($idPedido <= 0 || !in_array($acao, ["resolver", "recusar"], true)) {
        $erro = "Pedido inválido.";
    } else {
        mysqli_begin_transaction($ligacao);
        try {
            $stmt = mysqli_prepare(
                $ligacao,
                "SELECT p.id_pedido, p.id_utilizador, u.email
                 FROM pedido_recuperacao p
                 JOIN utilizador u ON u.id_utilizador = p.id_utilizador
                 WHERE p.id_pedido = ? AND p.estado = 'Pendente' AND u.ativo = 1
                 FOR UPDATE"
            );
            mysqli_stmt_bind_param($stmt, "i", $idPedido);
            mysqli_stmt_execute($stmt);
            $pedido = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if (!$pedido) {
                throw new RuntimeException("Pedido inexistente ou já tratado.");
            }

            $idAdmin = (int) $_SESSION["id_utilizador"];
            if ($acao === "resolver") {
                $temporaria = strtoupper(bin2hex(random_bytes(6)));
                $hash = password_hash($temporaria, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($ligacao, "UPDATE utilizador SET senha = ?, forcar_troca_senha = 1 WHERE id_utilizador = ?");
                mysqli_stmt_bind_param($stmt, "si", $hash, $pedido["id_utilizador"]);
                if (!mysqli_stmt_execute($stmt)) throw new RuntimeException("Falha ao criar password temporária.");
                mysqli_stmt_close($stmt);
                $novoEstado = "Resolvido";
            } else {
                $novoEstado = "Recusado";
            }

            $stmt = mysqli_prepare($ligacao, "UPDATE pedido_recuperacao SET estado = ?, data_resolucao = CURRENT_TIMESTAMP, id_admin_responsavel = ? WHERE id_pedido = ?");
            mysqli_stmt_bind_param($stmt, "sii", $novoEstado, $idAdmin, $idPedido);
            if (!mysqli_stmt_execute($stmt)) throw new RuntimeException("Falha ao concluir pedido.");
            mysqli_stmt_close($stmt);
            mysqli_commit($ligacao);

            if ($acao === "resolver") {
                $_SESSION["password_temporaria"] = ["email" => $pedido["email"], "password" => $temporaria];
            }
            redirecionar("admin/recuperacoes.php?estado=" . ($acao === "resolver" ? "resolvido" : "recusado"));
        } catch (Throwable $e) {
            mysqli_rollback($ligacao);
            error_log("Erro na recuperação administrativa: " . $e->getMessage());
            $erro = "Não foi possível tratar o pedido.";
        }
    }
}

$pedidos = [];
$resultado = mysqli_query($ligacao, "SELECT p.id_pedido, p.estado, p.data_pedido, p.data_resolucao, u.nome, u.email FROM pedido_recuperacao p JOIN utilizador u ON u.id_utilizador = p.id_utilizador ORDER BY FIELD(p.estado, 'Pendente', 'Resolvido', 'Recusado'), p.data_pedido DESC");
while ($pedido = mysqli_fetch_assoc($resultado)) $pedidos[] = $pedido;

$tituloPagina = "Recuperações | Administração";
include __DIR__ . "/../includes/menu.php";
?>
<main class="page"><div class="container">
<div class="page-heading heading-actions"><div><span class="eyebrow">Administração</span><h1>Recuperação de passwords</h1><p>Confirma presencialmente a identidade antes de gerar uma password temporária.</p></div><a class="btn" href="<?php echo $baseUrl; ?>/admin/dashboard.php">Dashboard</a></div>
<?php if ($passwordTemporaria): ?><div class="temporary-password" role="alert"><h2>Password temporária</h2><p>Conta: <strong><?php echo escapar($passwordTemporaria["email"]); ?></strong></p><code><?php echo escapar($passwordTemporaria["password"]); ?></code><p>Copia-a agora. Não voltará a ser apresentada.</p></div><?php endif; ?>
<?php if (($_GET["estado"] ?? "") === "recusado"): ?><div class="sucesso">Pedido recusado.</div><?php endif; ?>
<?php if ($erro): ?><div class="erro" role="alert"><?php echo escapar($erro); ?></div><?php endif; ?>
<div class="table-responsive"><table><thead><tr><th>Utilizador</th><th>Pedido</th><th>Estado</th><th>Ações</th></tr></thead><tbody>
<?php if (!$pedidos): ?><tr><td colspan="4">Não existem pedidos.</td></tr><?php endif; ?>
<?php foreach ($pedidos as $pedido): ?><tr><td><strong><?php echo escapar($pedido["nome"]); ?></strong><br><?php echo escapar($pedido["email"]); ?></td><td><?php echo date("d/m/Y H:i", strtotime($pedido["data_pedido"])); ?></td><td><span class="badge <?php echo $pedido["estado"] === "Pendente" ? "badge-warning" : "badge-muted"; ?>"><?php echo escapar($pedido["estado"]); ?></span></td><td>
<?php if ($pedido["estado"] === "Pendente"): ?><div class="admin-actions"><form method="post" class="inline-form" onsubmit="return confirm('Confirmaste presencialmente a identidade deste utilizador?')"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>"><input type="hidden" name="id_pedido" value="<?php echo (int) $pedido["id_pedido"]; ?>"><input type="hidden" name="acao" value="resolver"><button class="btn-small" type="submit">Gerar password</button></form><form method="post" class="inline-form" onsubmit="return confirm('Recusar este pedido?')"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>"><input type="hidden" name="id_pedido" value="<?php echo (int) $pedido["id_pedido"]; ?>"><input type="hidden" name="acao" value="recusar"><button class="btn-small" type="submit">Recusar</button></form></div><?php else: ?>—<?php endif; ?>
</td></tr><?php endforeach; ?></tbody></table></div>
</div></main><?php include __DIR__ . "/../includes/footer.php"; ?>
