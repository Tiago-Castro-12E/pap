-- Executar apenas numa base banco_ideias já criada antes desta funcionalidade.
-- Numa instalação nova, basta executar criar_tabelas.sql.

USE banco_ideias;

ALTER TABLE utilizador
    ADD COLUMN forcar_troca_senha TINYINT(1) NOT NULL DEFAULT 0 AFTER ativo;

CREATE TABLE pedido_recuperacao (
    id_pedido INT UNSIGNED AUTO_INCREMENT,
    id_utilizador INT UNSIGNED NOT NULL,
    estado ENUM('Pendente', 'Resolvido', 'Recusado') NOT NULL DEFAULT 'Pendente',
    data_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_resolucao TIMESTAMP NULL DEFAULT NULL,
    id_admin_responsavel INT UNSIGNED NULL,
    CONSTRAINT pk_pedido_recuperacao PRIMARY KEY (id_pedido),
    CONSTRAINT fk_recuperacao_utilizador FOREIGN KEY (id_utilizador)
        REFERENCES utilizador (id_utilizador)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_recuperacao_admin FOREIGN KEY (id_admin_responsavel)
        REFERENCES utilizador (id_utilizador)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_recuperacao_estado_data (estado, data_pedido),
    INDEX idx_recuperacao_utilizador (id_utilizador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
