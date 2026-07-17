<?php

require_once __DIR__ . "/includes/autenticacao.php";
require_once __DIR__ . "/includes/ligaBD.php";

$pesquisa = trim($_GET["q"] ?? "");
if ((function_exists("mb_strlen") ? mb_strlen($pesquisa) : strlen($pesquisa)) > 100) {
    $pesquisa = function_exists("mb_substr") ? mb_substr($pesquisa, 0, 100) : substr($pesquisa, 0, 100);
}

$idCategoria = filter_var($_GET["categoria"] ?? null, FILTER_VALIDATE_INT) ?: 0;
$ordenacao = $_GET["ordem"] ?? "recentes";
$ordenacoes = [
    "recentes" => "i.data_submissao DESC",
    "votadas" => "total_votos DESC, i.data_submissao DESC",
];
if (!isset($ordenacoes[$ordenacao])) {
    $ordenacao = "recentes";
}

$pagina = filter_var($_GET["pagina"] ?? 1, FILTER_VALIDATE_INT) ?: 1;
$pagina = max(1, $pagina);
$porPagina = 9;
$offset = ($pagina - 1) * $porPagina;

$filtroSql = "i.estado IN ('Aprovada', 'Implementada')
              AND (? = '' OR i.titulo LIKE CONCAT('%', ?, '%') OR i.descricao LIKE CONCAT('%', ?, '%'))
              AND (? = 0 OR i.id_categoria = ?)";

$stmtTotal = mysqli_prepare($ligacao, "SELECT COUNT(*) AS total FROM ideia i WHERE " . $filtroSql);
$totalIdeias = 0;
if ($stmtTotal) {
    mysqli_stmt_bind_param($stmtTotal, "sssii", $pesquisa, $pesquisa, $pesquisa, $idCategoria, $idCategoria);
    mysqli_stmt_execute($stmtTotal);
    $totalIdeias = (int) (mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTotal))["total"] ?? 0);
    mysqli_stmt_close($stmtTotal);
} else {
    error_log("Erro ao contar ideias: " . mysqli_error($ligacao));
}

$totalPaginas = max(1, (int) ceil($totalIdeias / $porPagina));
if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
    $offset = ($pagina - 1) * $porPagina;
}

$sql = "SELECT i.id_ideia, i.titulo, i.descricao, i.estado, i.data_submissao,
               u.nome AS autor, c.nome_categoria,
               (SELECT COUNT(*) FROM voto v WHERE v.id_ideia = i.id_ideia) AS total_votos,
               (SELECT COUNT(*) FROM comentario co WHERE co.id_ideia = i.id_ideia) AS total_comentarios
        FROM ideia i
        JOIN utilizador u ON u.id_utilizador = i.id_utilizador
        JOIN categoria c ON c.id_categoria = i.id_categoria
        WHERE " . $filtroSql . "
        ORDER BY " . $ordenacoes[$ordenacao] . "
        LIMIT ? OFFSET ?";

$ideias = [];
$erroCarregamento = "";
$stmt = mysqli_prepare($ligacao, $sql);
if ($stmt) {
    mysqli_stmt_bind_param(
        $stmt,
        "sssiiii",
        $pesquisa,
        $pesquisa,
        $pesquisa,
        $idCategoria,
        $idCategoria,
        $porPagina,
        $offset
    );
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    while ($ideia = mysqli_fetch_assoc($resultado)) {
        $ideias[] = $ideia;
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Erro ao listar ideias: " . mysqli_error($ligacao));
    $erroCarregamento = "Não foi possível carregar as ideias.";
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
}

function resumoIdeia(string $texto, int $limite = 180): string
{
    if ((function_exists("mb_strlen") ? mb_strlen($texto) : strlen($texto)) <= $limite) {
        return $texto;
    }

    $resumo = function_exists("mb_substr") ? mb_substr($texto, 0, $limite) : substr($texto, 0, $limite);
    return rtrim($resumo) . "…";
}

function urlPaginaIdeias(int $numero, string $pesquisa, int $categoria, string $ordem): string
{
    $parametros = array_filter([
        "q" => $pesquisa,
        "categoria" => $categoria > 0 ? $categoria : null,
        "ordem" => $ordem !== "recentes" ? $ordem : null,
        "pagina" => $numero > 1 ? $numero : null,
    ], static fn($valor) => $valor !== null && $valor !== "");

    return "ideias.php" . ($parametros ? "?" . http_build_query($parametros) : "");
}

$tituloPagina = "Explorar ideias | Banco de Ideias";
include __DIR__ . "/includes/menu.php";
?>

<main class="page ideas-page">
    <div class="container">
        <div class="page-heading heading-actions">
            <div>
                <span class="eyebrow">Comunidade</span>
                <h1>Explorar ideias</h1>
                <p>Descobre propostas aprovadas e soluções já implementadas.</p>
            </div>
            <?php if (utilizadorAutenticado()): ?>
                <a href="<?php echo $baseUrl; ?>/submeter.php" class="btn">Submeter ideia</a>
            <?php endif; ?>
        </div>

        <form method="get" action="" class="filters-card">
            <div class="field-group search-field">
                <label for="q">Pesquisar</label>
                <input type="search" id="q" name="q" value="<?php echo escapar($pesquisa); ?>"
                       maxlength="100" placeholder="Título ou descrição">
            </div>
            <div class="field-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo (int) $categoria["id_categoria"]; ?>"
                            <?php echo $idCategoria === (int) $categoria["id_categoria"] ? "selected" : ""; ?>>
                            <?php echo escapar($categoria["nome_categoria"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field-group">
                <label for="ordem">Ordenar</label>
                <select id="ordem" name="ordem">
                    <option value="recentes" <?php echo $ordenacao === "recentes" ? "selected" : ""; ?>>Mais recentes</option>
                    <option value="votadas" <?php echo $ordenacao === "votadas" ? "selected" : ""; ?>>Mais votadas</option>
                </select>
            </div>
            <button type="submit">Aplicar filtros</button>
        </form>

        <?php if ($erroCarregamento !== ""): ?>
            <div class="erro" role="alert"><?php echo escapar($erroCarregamento); ?></div>
        <?php else: ?>
            <div class="results-heading">
                <p><strong><?php echo $totalIdeias; ?></strong> <?php echo $totalIdeias === 1 ? "ideia encontrada" : "ideias encontradas"; ?></p>
            </div>

            <?php if (!$ideias): ?>
                <div class="empty-state">
                    <h2>Nenhuma ideia encontrada</h2>
                    <p>Experimenta alterar a pesquisa ou os filtros selecionados.</p>
                </div>
            <?php else: ?>
                <div class="ideas-grid">
                    <?php foreach ($ideias as $ideia): ?>
                        <article class="idea-card">
                            <div class="idea-card-topline">
                                <span class="badge"><?php echo escapar($ideia["nome_categoria"]); ?></span>
                                <span class="badge <?php echo $ideia["estado"] === "Implementada" ? "badge-success" : "badge-info"; ?>">
                                    <?php echo escapar($ideia["estado"]); ?>
                                </span>
                            </div>
                            <h2>
                                <a href="<?php echo $baseUrl; ?>/ideia.php?id=<?php echo (int) $ideia["id_ideia"]; ?>">
                                    <?php echo escapar($ideia["titulo"]); ?>
                                </a>
                            </h2>
                            <p><?php echo escapar(resumoIdeia($ideia["descricao"])); ?></p>
                            <div class="idea-meta">
                                <span>Por <?php echo escapar($ideia["autor"]); ?></span>
                                <time datetime="<?php echo escapar($ideia["data_submissao"]); ?>">
                                    <?php echo escapar(date("d/m/Y", strtotime($ideia["data_submissao"]))); ?>
                                </time>
                            </div>
                            <div class="idea-stats" aria-label="Participação na ideia">
                                <span>&#128077; <?php echo (int) $ideia["total_votos"]; ?> votos</span>
                                <span>&#128172; <?php echo (int) $ideia["total_comentarios"]; ?> comentários</span>
                            </div>
                            <a class="card-link" href="<?php echo $baseUrl; ?>/ideia.php?id=<?php echo (int) $ideia["id_ideia"]; ?>">Ver ideia</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($totalPaginas > 1): ?>
                <nav class="pagination" aria-label="Paginação das ideias">
                    <?php if ($pagina > 1): ?>
                        <a href="<?php echo escapar(urlPaginaIdeias($pagina - 1, $pesquisa, $idCategoria, $ordenacao)); ?>">Anterior</a>
                    <?php endif; ?>
                    <span>Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?></span>
                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="<?php echo escapar(urlPaginaIdeias($pagina + 1, $pesquisa, $idCategoria, $ordenacao)); ?>">Seguinte</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
