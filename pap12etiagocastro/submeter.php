<?php

require_once __DIR__ . "/includes/verificarLogin.php";
require_once __DIR__ . "/includes/ligaBD.php";

$erros = [];
$titulo = "";
$descricao = "";
$idCategoria = 0;
$sucesso = ($_GET["estado"] ?? "") === "criada";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $idCategoria = filter_var($_POST["id_categoria"] ?? null, FILTER_VALIDATE_INT) ?: 0;

    if (!tokenCsrfValido($_POST["csrf_token"] ?? null)) {
        $erros[] = "O formulário expirou. Atualiza a página e tenta novamente.";
    }

    $tamanhoTitulo = function_exists("mb_strlen") ? mb_strlen($titulo) : strlen($titulo);
    $tamanhoDescricao = function_exists("mb_strlen") ? mb_strlen($descricao) : strlen($descricao);

    if ($tamanhoTitulo < 5 || $tamanhoTitulo > 150) {
        $erros[] = "O título deve ter entre 5 e 150 caracteres.";
    }

    if ($tamanhoDescricao < 20 || $tamanhoDescricao > 5000) {
        $erros[] = "A descrição deve ter entre 20 e 5000 caracteres.";
    }

    if ($idCategoria <= 0) {
        $erros[] = "Seleciona uma categoria válida.";
    }

    if (!$erros) {
        $stmt = mysqli_prepare(
            $ligacao,
            "SELECT id_categoria FROM categoria WHERE id_categoria = ? AND ativa = 1 LIMIT 1"
        );

        if (!$stmt) {
            error_log("Erro ao preparar a validação da categoria: " . mysqli_error($ligacao));
            $erros[] = "Não foi possível validar a categoria.";
        } else {
            mysqli_stmt_bind_param($stmt, "i", $idCategoria);
            mysqli_stmt_execute($stmt);
            $categoriaValida = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) !== null;
            mysqli_stmt_close($stmt);

            if (!$categoriaValida) {
                $erros[] = "A categoria selecionada não está disponível.";
            }
        }
    }

    if (!$erros) {
        $idUtilizador = (int) $_SESSION["id_utilizador"];
        $stmt = mysqli_prepare(
            $ligacao,
            "INSERT INTO ideia (titulo, descricao, id_utilizador, id_categoria)
             VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Erro ao preparar a submissão da ideia: " . mysqli_error($ligacao));
            $erros[] = "Não foi possível submeter a ideia. Tenta novamente.";
        } else {
            mysqli_stmt_bind_param($stmt, "ssii", $titulo, $descricao, $idUtilizador, $idCategoria);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                unset($_SESSION["csrf_token"]);
                redirecionar("submeter.php?estado=criada");
            }

            error_log("Erro ao submeter a ideia: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            $erros[] = "Não foi possível submeter a ideia. Tenta novamente.";
        }
    }
}

$categorias = [];
$resultadoCategorias = mysqli_query(
    $ligacao,
    "SELECT id_categoria, nome_categoria FROM categoria WHERE ativa = 1 ORDER BY nome_categoria"
);

if ($resultadoCategorias) {
    while ($categoria = mysqli_fetch_assoc($resultadoCategorias)) {
        $categorias[] = $categoria;
    }
} else {
    error_log("Erro ao carregar categorias: " . mysqli_error($ligacao));
    $erros[] = "Não foi possível carregar as categorias.";
}

$tituloPagina = "Submeter ideia | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>

<main class="page">
    <div class="container content-narrow">
        <div class="page-heading">
            <div>
                <span class="eyebrow">Participação</span>
                <h1>Submeter uma ideia</h1>
                <p>Apresenta uma proposta concreta para melhorar a escola ou a comunidade.</p>
            </div>
        </div>

        <?php if ($sucesso): ?>
            <div class="sucesso" role="status">A ideia foi submetida e ficará pendente até ser analisada.</div>
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

        <?php if (!$categorias): ?>
            <div class="aviso" role="status">Ainda não existem categorias disponíveis. Contacta um administrador.</div>
        <?php else: ?>
            <form method="post" action="" class="form-card">
                <input type="hidden" name="csrf_token" value="<?php echo escapar(tokenCsrf()); ?>">

                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo escapar($titulo); ?>"
                       minlength="5" maxlength="150" required>
                <small>Resume a proposta de forma clara.</small>

                <label for="id_categoria">Categoria</label>
                <select id="id_categoria" name="id_categoria" required>
                    <option value="">Seleciona uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo (int) $categoria["id_categoria"]; ?>"
                            <?php echo $idCategoria === (int) $categoria["id_categoria"] ? "selected" : ""; ?>>
                            <?php echo escapar($categoria["nome_categoria"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" minlength="20" maxlength="5000" required><?php echo escapar($descricao); ?></textarea>
                <small>Explica o problema, a solução proposta e os possíveis benefícios.</small>

                <button type="submit">Submeter ideia</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
