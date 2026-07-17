<?php

require_once __DIR__ . "/includes/autenticacao.php";
require_once __DIR__ . "/includes/ligaBD.php";

$idIdeia = filter_var($_GET["id"] ?? $_POST["id_ideia"] ?? null, FILTER_VALIDATE_INT) ?: 0;
$erroAcao = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    exigirLogin();

    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) {
        $erroAcao = "O formulário expirou. Atualiza a página e tenta novamente.";
    } elseif ($idIdeia <= 0) {
        $erroAcao = "Ideia inválida.";
    } else {
        $acao = $_POST["acao"] ?? "";
        $idUtilizador = (int) $_SESSION["id_utilizador"];

        if ($acao === "votar" || $acao === "comentar") {
            $stmt = mysqli_prepare(
                $ligacao,
                "SELECT id_ideia FROM ideia WHERE id_ideia = ? AND estado IN ('Aprovada', 'Implementada') LIMIT 1"
            );
            mysqli_stmt_bind_param($stmt, "i", $idIdeia);
            mysqli_stmt_execute($stmt);
            $ideiaPublica = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) !== null;
            mysqli_stmt_close($stmt);

            if (!$ideiaPublica) {
                $erroAcao = "Esta ideia não está disponível para participação.";
            }
        }

        if ($acao === "votar" && $erroAcao === "") {
            mysqli_begin_transaction($ligacao);
            try {
                $stmt = mysqli_prepare(
                    $ligacao,
                    "SELECT id_voto FROM voto WHERE id_utilizador = ? AND id_ideia = ? FOR UPDATE"
                );
                mysqli_stmt_bind_param($stmt, "ii", $idUtilizador, $idIdeia);
                mysqli_stmt_execute($stmt);
                $voto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);

                if ($voto) {
                    $stmt = mysqli_prepare($ligacao, "DELETE FROM voto WHERE id_voto = ?");
                    $idVoto = (int) $voto["id_voto"];
                    mysqli_stmt_bind_param($stmt, "i", $idVoto);
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new RuntimeException("Falha ao remover voto.");
                    }
                    $estadoAcao = "voto-removido";
                } else {
                    $stmt = mysqli_prepare($ligacao, "INSERT INTO voto (id_utilizador, id_ideia) VALUES (?, ?)");
                    mysqli_stmt_bind_param($stmt, "ii", $idUtilizador, $idIdeia);
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new RuntimeException("Falha ao adicionar voto.");
                    }
                    $estadoAcao = "voto-adicionado";
                }
                mysqli_stmt_close($stmt);
                mysqli_commit($ligacao);
                redirecionar("ideia.php?id=" . $idIdeia . "&estado=" . $estadoAcao);
            } catch (Throwable $erro) {
                mysqli_rollback($ligacao);
                error_log("Erro ao alternar voto: " . $erro->getMessage());
                $erroAcao = "Não foi possível atualizar o voto.";
            }
        } elseif ($acao === "comentar" && $erroAcao === "") {
            $texto = trim($_POST["texto"] ?? "");
            $tamanho = function_exists("mb_strlen") ? mb_strlen($texto) : strlen($texto);

            if ($tamanho < 2 || $tamanho > 2000) {
                $erroAcao = "O comentário deve ter entre 2 e 2000 caracteres.";
            } else {
                $stmt = mysqli_prepare(
                    $ligacao,
                    "INSERT INTO comentario (texto, id_utilizador, id_ideia) VALUES (?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmt, "sii", $texto, $idUtilizador, $idIdeia);

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    redirecionar("ideia.php?id=" . $idIdeia . "&estado=comentario-adicionado#comentarios");
                }
                error_log("Erro ao adicionar comentário: " . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
                $erroAcao = "Não foi possível adicionar o comentário.";
            }
        } elseif ($acao === "remover_comentario" && $erroAcao === "") {
            $idComentario = filter_var($_POST["id_comentario"] ?? null, FILTER_VALIDATE_INT) ?: 0;

            if (utilizadorAdministrador()) {
                $stmt = mysqli_prepare($ligacao, "DELETE FROM comentario WHERE id_comentario = ? AND id_ideia = ?");
                mysqli_stmt_bind_param($stmt, "ii", $idComentario, $idIdeia);
            } else {
                $stmt = mysqli_prepare(
                    $ligacao,
                    "DELETE FROM comentario WHERE id_comentario = ? AND id_ideia = ? AND id_utilizador = ?"
                );
                mysqli_stmt_bind_param($stmt, "iii", $idComentario, $idIdeia, $idUtilizador);
            }
            mysqli_stmt_execute($stmt);
            $removido = mysqli_stmt_affected_rows($stmt) === 1;
            mysqli_stmt_close($stmt);

            if ($removido) {
                redirecionar("ideia.php?id=" . $idIdeia . "&estado=comentario-removido#comentarios");
            }
            $erroAcao = "Não tens permissão para remover esse comentário.";
        } elseif (!in_array($acao, ["votar", "comentar", "remover_comentario"], true)) {
            $erroAcao = "Ação inválida.";
        }
    }
}

$ideia = null;
if ($idIdeia > 0) {
    $sql = "SELECT i.id_ideia, i.titulo, i.descricao, i.estado, i.data_submissao,
                   i.data_atualizacao, i.id_utilizador, u.nome AS autor, c.nome_categoria,
                   (SELECT COUNT(*) FROM voto v WHERE v.id_ideia = i.id_ideia) AS total_votos,
                   (SELECT COUNT(*) FROM comentario co WHERE co.id_ideia = i.id_ideia) AS total_comentarios
            FROM ideia i JOIN utilizador u ON u.id_utilizador = i.id_utilizador
            JOIN categoria c ON c.id_categoria = i.id_categoria WHERE i.id_ideia = ? LIMIT 1";
    $stmt = mysqli_prepare($ligacao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idIdeia);
    mysqli_stmt_execute($stmt);
    $ideia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
    mysqli_stmt_close($stmt);

    $publica = $ideia && in_array($ideia["estado"], ["Aprovada", "Implementada"], true);
    $propria = $ideia && utilizadorAutenticado() && (int) $_SESSION["id_utilizador"] === (int) $ideia["id_utilizador"];
    if (!$ideia || (!$publica && !$propria && !utilizadorAdministrador())) {
        $ideia = null;
    }
}

if (!$ideia) {
    http_response_code(404);
}

$comentarios = [];
$utilizadorVotou = false;
if ($ideia) {
    $stmt = mysqli_prepare(
        $ligacao,
        "SELECT co.id_comentario, co.texto, co.data_comentario, co.id_utilizador, u.nome AS autor
         FROM comentario co JOIN utilizador u ON u.id_utilizador = co.id_utilizador
         WHERE co.id_ideia = ? ORDER BY co.data_comentario ASC"
    );
    mysqli_stmt_bind_param($stmt, "i", $idIdeia);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    while ($comentario = mysqli_fetch_assoc($resultado)) {
        $comentarios[] = $comentario;
    }
    mysqli_stmt_close($stmt);

    if (utilizadorAutenticado()) {
        $idUtilizador = (int) $_SESSION["id_utilizador"];
        $stmt = mysqli_prepare($ligacao, "SELECT id_voto FROM voto WHERE id_utilizador = ? AND id_ideia = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ii", $idUtilizador, $idIdeia);
        mysqli_stmt_execute($stmt);
        $utilizadorVotou = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) !== null;
        mysqli_stmt_close($stmt);
    }
}

$mensagensEstado = [
    "voto-adicionado" => "Voto registado.", "voto-removido" => "Voto removido.",
    "comentario-adicionado" => "Comentário publicado.", "comentario-removido" => "Comentário removido.",
];
$mensagemEstado = $mensagensEstado[$_GET["estado"] ?? ""] ?? "";
$tituloPagina = $ideia ? $ideia["titulo"] . " | Banco de Ideias" : "Ideia não encontrada";
include __DIR__ . "/includes/menu.php";
?>

<main class="page"><div class="container content-narrow">
<?php if (!$ideia): ?>
    <div class="empty-state"><h1>Ideia não encontrada</h1><p>A ideia não existe ou não está disponível para consulta.</p><a href="<?php echo $baseUrl; ?>/ideias.php" class="btn">Voltar às ideias</a></div>
<?php else: ?>
    <?php if ($mensagemEstado): ?><div class="sucesso" role="status"><?php echo escapar($mensagemEstado); ?></div><?php endif; ?>
    <?php if ($erroAcao): ?><div class="erro" role="alert"><?php echo escapar($erroAcao); ?></div><?php endif; ?>
    <article class="idea-detail">
        <div class="idea-card-topline"><span class="badge"><?php echo escapar($ideia["nome_categoria"]); ?></span><span class="badge badge-info"><?php echo escapar($ideia["estado"]); ?></span></div>
        <h1><?php echo escapar($ideia["titulo"]); ?></h1>
        <div class="idea-meta"><span>Proposta por <strong><?php echo escapar($ideia["autor"]); ?></strong></span><time datetime="<?php echo escapar($ideia["data_submissao"]); ?>"><?php echo date("d/m/Y", strtotime($ideia["data_submissao"])); ?></time></div>
        <?php if (!$publica): ?><div class="aviso">Esta ideia ainda não é pública.</div><?php endif; ?>
        <div class="idea-description"><?php echo nl2br(escapar($ideia["descricao"])); ?></div>
        <div class="idea-stats detail-stats"><span>&#128077; <strong><?php echo (int) $ideia["total_votos"]; ?></strong> votos</span><span>&#128172; <strong><?php echo count($comentarios); ?></strong> comentários</span></div>
        <?php if ($publica && utilizadorAutenticado()): ?>
            <form method="post" class="participation-action"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>"><input type="hidden" name="id_ideia" value="<?php echo $idIdeia; ?>"><input type="hidden" name="acao" value="votar"><button type="submit"><?php echo $utilizadorVotou ? "Retirar voto" : "Apoiar esta ideia"; ?></button></form>
        <?php elseif ($publica): ?><p class="login-prompt"><a href="<?php echo $baseUrl; ?>/login.php">Inicia sessão</a> para votar e comentar.</p><?php endif; ?>
    </article>

    <section id="comentarios" class="comments-section"><h2>Comentários</h2>
        <?php if ($publica && utilizadorAutenticado()): ?><form method="post" class="form-card comment-form"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>"><input type="hidden" name="id_ideia" value="<?php echo $idIdeia; ?>"><input type="hidden" name="acao" value="comentar"><label for="texto">Adicionar comentário</label><textarea id="texto" name="texto" minlength="2" maxlength="2000" required></textarea><button type="submit">Publicar comentário</button></form><?php endif; ?>
        <div class="comments-list">
        <?php if (!$comentarios): ?><p class="empty-inline">Ainda não existem comentários.</p><?php endif; ?>
        <?php foreach ($comentarios as $comentario): ?><article class="comment"><header><strong><?php echo escapar($comentario["autor"]); ?></strong><time datetime="<?php echo escapar($comentario["data_comentario"]); ?>"><?php echo date("d/m/Y H:i", strtotime($comentario["data_comentario"])); ?></time></header><p><?php echo nl2br(escapar($comentario["texto"])); ?></p>
            <?php if (utilizadorAutenticado() && ((int) $_SESSION["id_utilizador"] === (int) $comentario["id_utilizador"] || utilizadorAdministrador())): ?><form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>"><input type="hidden" name="id_ideia" value="<?php echo $idIdeia; ?>"><input type="hidden" name="id_comentario" value="<?php echo (int) $comentario["id_comentario"]; ?>"><input type="hidden" name="acao" value="remover_comentario"><button type="submit" class="btn-small" onclick="return confirm('Remover este comentário?')">Remover</button></form><?php endif; ?>
        </article><?php endforeach; ?></div>
    </section><a href="<?php echo $baseUrl; ?>/ideias.php" class="back-link">&larr; Voltar às ideias</a>
<?php endif; ?>
</div></main>
<?php include __DIR__ . "/includes/footer.php"; ?>
