-- Consultas de referência para desenvolvimento.
-- Os valores @... simulam parâmetros que no PHP deverão ser enviados por prepared statements.

USE banco_ideias;

-- 1. Ideias públicas com autor, categoria e totais, sem duplicações por JOIN.
SELECT
    i.id_ideia,
    i.titulo,
    i.descricao,
    i.estado,
    i.data_submissao,
    u.nome AS autor,
    c.nome_categoria,
    COUNT(DISTINCT v.id_voto) AS total_votos,
    COUNT(DISTINCT co.id_comentario) AS total_comentarios
FROM ideia i
JOIN utilizador u ON u.id_utilizador = i.id_utilizador
JOIN categoria c ON c.id_categoria = i.id_categoria
LEFT JOIN voto v ON v.id_ideia = i.id_ideia
LEFT JOIN comentario co ON co.id_ideia = i.id_ideia
WHERE i.estado IN ('Aprovada', 'Implementada')
GROUP BY i.id_ideia, i.titulo, i.descricao, i.estado, i.data_submissao,
         u.nome, c.nome_categoria
ORDER BY i.data_submissao DESC;

-- 2. Pesquisa e filtro de ideias públicas.
SET @pesquisa = 'reciclagem';
SET @id_categoria = NULL;

SELECT
    i.id_ideia,
    i.titulo,
    i.data_submissao,
    c.nome_categoria,
    COUNT(v.id_voto) AS total_votos
FROM ideia i
JOIN categoria c ON c.id_categoria = i.id_categoria
LEFT JOIN voto v ON v.id_ideia = i.id_ideia
WHERE i.estado IN ('Aprovada', 'Implementada')
  AND (i.titulo LIKE CONCAT('%', @pesquisa, '%')
       OR i.descricao LIKE CONCAT('%', @pesquisa, '%'))
  AND (@id_categoria IS NULL OR i.id_categoria = @id_categoria)
GROUP BY i.id_ideia, i.titulo, i.data_submissao, c.nome_categoria
ORDER BY total_votos DESC, i.data_submissao DESC;

-- 3. Detalhe de uma ideia. Trocar o valor pelo identificador pretendido.
SET @id_ideia = 1;

SELECT
    i.*,
    u.nome AS autor,
    c.nome_categoria,
    COUNT(DISTINCT v.id_voto) AS total_votos,
    COUNT(DISTINCT co.id_comentario) AS total_comentarios
FROM ideia i
JOIN utilizador u ON u.id_utilizador = i.id_utilizador
JOIN categoria c ON c.id_categoria = i.id_categoria
LEFT JOIN voto v ON v.id_ideia = i.id_ideia
LEFT JOIN comentario co ON co.id_ideia = i.id_ideia
WHERE i.id_ideia = @id_ideia
GROUP BY i.id_ideia, u.nome, c.nome_categoria;

-- 4. Comentários de uma ideia por ordem cronológica.
SELECT co.id_comentario, co.texto, co.data_comentario, u.nome AS autor
FROM comentario co
JOIN utilizador u ON u.id_utilizador = co.id_utilizador
WHERE co.id_ideia = @id_ideia
ORDER BY co.data_comentario ASC;

-- 5. Ideias submetidas por um utilizador.
SET @id_utilizador = 2;

SELECT i.id_ideia, i.titulo, i.estado, i.data_submissao, c.nome_categoria
FROM ideia i
JOIN categoria c ON c.id_categoria = i.id_categoria
WHERE i.id_utilizador = @id_utilizador
ORDER BY i.data_submissao DESC;

-- 6. Categorias ativas para formulários e filtros.
SELECT id_categoria, nome_categoria
FROM categoria
WHERE ativa = 1
ORDER BY nome_categoria ASC;

-- 7. Totais gerais para o dashboard administrativo.
SELECT
    (SELECT COUNT(*) FROM utilizador WHERE ativo = 1) AS utilizadores_ativos,
    (SELECT COUNT(*) FROM categoria WHERE ativa = 1) AS categorias_ativas,
    (SELECT COUNT(*) FROM ideia) AS total_ideias,
    (SELECT COUNT(*) FROM comentario) AS total_comentarios,
    (SELECT COUNT(*) FROM voto) AS total_votos;

-- 8. Número de ideias por estado para o dashboard.
SELECT estado, COUNT(*) AS total
FROM ideia
GROUP BY estado
ORDER BY FIELD(estado, 'Pendente', 'Aprovada', 'Implementada', 'Rejeitada');

-- 9. Categorias com mais ideias e votos.
SELECT
    c.id_categoria,
    c.nome_categoria,
    COUNT(DISTINCT i.id_ideia) AS total_ideias,
    COUNT(DISTINCT v.id_voto) AS total_votos
FROM categoria c
LEFT JOIN ideia i ON i.id_categoria = c.id_categoria
LEFT JOIN voto v ON v.id_ideia = i.id_ideia
GROUP BY c.id_categoria, c.nome_categoria
ORDER BY total_ideias DESC, total_votos DESC;

-- 10. Verificar se um utilizador já votou numa ideia.
SELECT EXISTS (
    SELECT 1
    FROM voto
    WHERE id_utilizador = @id_utilizador
      AND id_ideia = @id_ideia
) AS ja_votou;

