<?php

require_once __DIR__ . "/../includes/autenticacao.php";
exigirAdministrador();
require_once __DIR__ . "/../includes/ligaBD.php";

$erros = [];
$nomeCategoria = "";
$estado = $_GET["estado"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) {
        $erros[] = "O formulário expirou. Atualiza a página e tenta novamente.";
    } else {
        $acao = $_POST["acao"] ?? "";

        if ($acao === "criar") {
            $nomeCategoria = trim($_POST["nome_categoria"] ?? "");
            $tamanho = function_exists("mb_strlen") ? mb_strlen($nomeCategoria) : strlen($nomeCategoria);

            if ($tamanho < 2 || $tamanho > 100) {
                $erros[] = "O nome da categoria deve ter entre 2 e 100 caracteres.";
            } else {
                $stmt = mysqli_prepare($ligacao, "INSERT INTO categoria (nome_categoria) VALUES (?)");

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $nomeCategoria);

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        redirecionar("admin/categorias.php?estado=criada");
                    }

                    if (mysqli_stmt_errno($stmt) === 1062) {
                        $erros[] = "Já existe uma categoria com esse nome.";
                    } else {
                        error_log("Erro ao criar categoria: " . mysqli_stmt_error($stmt));
                        $erros[] = "Não foi possível criar a categoria.";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    error_log("Erro ao preparar criação de categoria: " . mysqli_error($ligacao));
                    $erros[] = "Não foi possível criar a categoria.";
                }
            }
        } elseif ($acao === "alternar") {
            $idCategoria = filter_var($_POST["id_categoria"] ?? null, FILTER_VALIDATE_INT) ?: 0;

            if ($idCategoria <= 0) {
                $erros[] = "Categoria inválida.";
            } else {
                $stmt = mysqli_prepare(
                    $ligacao,
                    "UPDATE categoria SET ativa = IF(ativa = 1, 0, 1) WHERE id_categoria = ?"
                );

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "i", $idCategoria);
                    mysqli_stmt_execute($stmt);
                    $alterada = mysqli_stmt_affected_rows($stmt) === 1;
                    mysqli_stmt_close($stmt);

                    if ($alterada) {
                        redirecionar("admin/categorias.php?estado=alterada");
                    }
                }

                $erros[] = "Não foi possível alterar a categoria.";
            }
        } else {
            $erros[] = "Ação inválida.";
        }
    }
}

$categorias = [];
$sql = "SELECT c.id_categoria, c.nome_categoria, c.ativa, c.data_criacao,
               COUNT(i.id_ideia) AS total_ideias
        FROM categoria c
        LEFT JOIN ideia i ON i.id_categoria = c.id_categoria
        GROUP BY c.id_categoria, c.nome_categoria, c.ativa, c.data_criacao
        ORDER BY c.nome_categoria";
$resultado = mysqli_query($ligacao, $sql);

if ($resultado) {
    while ($categoria = mysqli_fetch_assoc($resultado)) {
        $categorias[] = $categoria;
    }
} else {
    error_log("Erro ao listar categorias: " . mysqli_error($ligacao));
    $erros[] = "Não foi possível carregar as categorias.";
}

$tituloPagina = "Gerir categorias | Administração";
include __DIR__ . "/../includes/menu.php";
?>

<main class="page">
    <div class="container">
        <div class="page-heading heading-actions">
            <div>
                <span class="eyebrow">Administração</span>
                <h1>Categorias</h1>
                <p>Cria categorias e controla quais aparecem nos formulários públicos.</p>
            </div>
            <a href="<?php echo $baseUrl; ?>/admin/dashboard.php" class="btn">Voltar ao dashboard</a>
        </div>

        <?php if ($estado === "criada"): ?>
            <div class="sucesso" role="status">Categoria criada com sucesso.</div>
        <?php elseif ($estado === "alterada"): ?>
            <div class="sucesso" role="status">Estado da categoria alterado.</div>
        <?php endif; ?>

        <?php if ($erros): ?>
            <div class="erro" role="alert">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo escapar($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="admin-layout">
            <form method="post" action="" class="form-card admin-form">
                <input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">
                <input type="hidden" name="acao" value="criar">

                <h2>Nova categoria</h2>
                <label for="nome_categoria">Nome</label>
                <input type="text" id="nome_categoria" name="nome_categoria"
                       value="<?php echo escapar($nomeCategoria); ?>" minlength="2" maxlength="100" required>
                <button type="submit">Criar categoria</button>
            </form>

            <div>
                <div class="aviso">Categorias com ideias não são eliminadas. Podem ser desativadas sem perder os dados existentes.</div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Categoria</th>
                                <th>Estado</th>
                                <th>Ideias</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$categorias): ?>
                                <tr><td colspan="4">Ainda não existem categorias.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td><?php echo escapar($categoria["nome_categoria"]); ?></td>
                                    <td>
                                        <span class="badge <?php echo (int) $categoria["ativa"] === 1 ? "badge-success" : "badge-muted"; ?>">
                                            <?php echo (int) $categoria["ativa"] === 1 ? "Ativa" : "Inativa"; ?>
                                        </span>
                                    </td>
                                    <td><?php echo (int) $categoria["total_ideias"]; ?></td>
                                    <td>
                                        <form method="post" action="" class="inline-form">
                                            <input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">
                                            <input type="hidden" name="acao" value="alternar">
                                            <input type="hidden" name="id_categoria" value="<?php echo (int) $categoria["id_categoria"]; ?>">
                                            <button type="submit" class="btn-small">
                                                <?php echo (int) $categoria["ativa"] === 1 ? "Desativar" : "Ativar"; ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . "/../includes/footer.php"; ?>
