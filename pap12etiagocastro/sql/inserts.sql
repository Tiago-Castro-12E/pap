-- Dados exclusivamente para desenvolvimento e demonstração.
-- Executar depois de criar_tabelas.sql.

USE banco_ideias;

-- A hash bcrypt abaixo corresponde à palavra-passe: password
-- Foi produzida pelo equivalente a password_hash('password', PASSWORD_BCRYPT).
-- Estas credenciais são fracas e nunca devem ser usadas em produção.
INSERT INTO utilizador (nome, email, senha, tipo, ativo) VALUES
    ('Administrador Demo', 'admin@bancodeideias.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'admin', 1),
    ('Ana Aluna', 'ana@bancodeideias.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'aluno', 1),
    ('Professor Manuel', 'manuel@bancodeideias.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'professor', 1)
ON DUPLICATE KEY UPDATE
    nome = VALUES(nome),
    tipo = VALUES(tipo),
    ativo = VALUES(ativo);

INSERT INTO categoria (nome_categoria, ativa) VALUES
    ('Ambiente', 1),
    ('Cultura', 1),
    ('Desporto', 1),
    ('Espaços escolares', 1),
    ('Tecnologia', 1),
    ('Transportes', 1)
ON DUPLICATE KEY UPDATE ativa = VALUES(ativa);

INSERT INTO ideia (titulo, descricao, estado, data_submissao, id_utilizador, id_categoria)
SELECT
    'Instalar mais pontos de reciclagem',
    'Colocar ecopontos identificados nos corredores e junto ao bar para facilitar a separação de resíduos.',
    'Aprovada',
    '2026-06-10 10:00:00',
    u.id_utilizador,
    c.id_categoria
FROM utilizador u
JOIN categoria c ON c.nome_categoria = 'Ambiente'
WHERE u.email = 'ana@bancodeideias.test'
  AND NOT EXISTS (
      SELECT 1 FROM ideia i
      WHERE i.titulo = 'Instalar mais pontos de reciclagem'
        AND i.id_utilizador = u.id_utilizador
  );

INSERT INTO ideia (titulo, descricao, estado, data_submissao, id_utilizador, id_categoria)
SELECT
    'Clube semanal de programação',
    'Criar um encontro semanal aberto a alunos de todos os cursos para desenvolver pequenos projetos tecnológicos.',
    'Pendente',
    '2026-06-18 15:30:00',
    u.id_utilizador,
    c.id_categoria
FROM utilizador u
JOIN categoria c ON c.nome_categoria = 'Tecnologia'
WHERE u.email = 'manuel@bancodeideias.test'
  AND NOT EXISTS (
      SELECT 1 FROM ideia i
      WHERE i.titulo = 'Clube semanal de programação'
        AND i.id_utilizador = u.id_utilizador
  );

INSERT INTO ideia (titulo, descricao, estado, data_submissao, id_utilizador, id_categoria)
SELECT
    'Zona exterior com sombra',
    'Melhorar o espaço exterior com bancos e estruturas de sombra para os intervalos e atividades ao ar livre.',
    'Implementada',
    '2026-05-22 09:15:00',
    u.id_utilizador,
    c.id_categoria
FROM utilizador u
JOIN categoria c ON c.nome_categoria = 'Espaços escolares'
WHERE u.email = 'ana@bancodeideias.test'
  AND NOT EXISTS (
      SELECT 1 FROM ideia i
      WHERE i.titulo = 'Zona exterior com sombra'
        AND i.id_utilizador = u.id_utilizador
  );

INSERT INTO comentario (texto, data_comentario, id_utilizador, id_ideia)
SELECT
    'Podemos começar por identificar os locais onde existe maior produção de resíduos.',
    '2026-06-11 11:20:00',
    u.id_utilizador,
    i.id_ideia
FROM utilizador u
JOIN ideia i ON i.titulo = 'Instalar mais pontos de reciclagem'
WHERE u.email = 'manuel@bancodeideias.test'
  AND NOT EXISTS (
      SELECT 1 FROM comentario co
      WHERE co.id_utilizador = u.id_utilizador
        AND co.id_ideia = i.id_ideia
        AND co.texto = 'Podemos começar por identificar os locais onde existe maior produção de resíduos.'
  );

INSERT INTO comentario (texto, data_comentario, id_utilizador, id_ideia)
SELECT
    'A iniciativa também podia incluir sessões para iniciantes.',
    '2026-06-19 12:05:00',
    u.id_utilizador,
    i.id_ideia
FROM utilizador u
JOIN ideia i ON i.titulo = 'Clube semanal de programação'
WHERE u.email = 'ana@bancodeideias.test'
  AND NOT EXISTS (
      SELECT 1 FROM comentario co
      WHERE co.id_utilizador = u.id_utilizador
        AND co.id_ideia = i.id_ideia
        AND co.texto = 'A iniciativa também podia incluir sessões para iniciantes.'
  );

INSERT IGNORE INTO voto (id_utilizador, id_ideia)
SELECT u.id_utilizador, i.id_ideia
FROM utilizador u
JOIN ideia i ON i.titulo = 'Instalar mais pontos de reciclagem'
WHERE u.email IN ('ana@bancodeideias.test', 'manuel@bancodeideias.test');

INSERT IGNORE INTO voto (id_utilizador, id_ideia)
SELECT u.id_utilizador, i.id_ideia
FROM utilizador u
JOIN ideia i ON i.titulo = 'Zona exterior com sombra'
WHERE u.email = 'manuel@bancodeideias.test';

