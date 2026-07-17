-- Banco de Ideias da Comunidade
-- Compatível com MySQL 8 e versões recentes de MariaDB.

CREATE DATABASE IF NOT EXISTS banco_ideias
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE banco_ideias;

CREATE TABLE IF NOT EXISTS utilizador (
    id_utilizador INT UNSIGNED AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('aluno', 'professor', 'admin') NOT NULL DEFAULT 'aluno',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    forcar_troca_senha TINYINT(1) NOT NULL DEFAULT 0,
    data_criacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_utilizador PRIMARY KEY (id_utilizador),
    CONSTRAINT uq_utilizador_email UNIQUE (email),
    INDEX idx_utilizador_tipo_ativo (tipo, ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categoria (
    id_categoria INT UNSIGNED AUTO_INCREMENT,
    nome_categoria VARCHAR(100) NOT NULL,
    ativa TINYINT(1) NOT NULL DEFAULT 1,
    data_criacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_categoria PRIMARY KEY (id_categoria),
    CONSTRAINT uq_categoria_nome UNIQUE (nome_categoria),
    INDEX idx_categoria_ativa (ativa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ideia (
    id_ideia INT UNSIGNED AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    estado ENUM('Pendente', 'Aprovada', 'Rejeitada', 'Implementada') NOT NULL DEFAULT 'Pendente',
    data_submissao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_utilizador INT UNSIGNED NOT NULL,
    id_categoria INT UNSIGNED NOT NULL,
    CONSTRAINT pk_ideia PRIMARY KEY (id_ideia),
    CONSTRAINT fk_ideia_utilizador FOREIGN KEY (id_utilizador)
        REFERENCES utilizador (id_utilizador)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_ideia_categoria FOREIGN KEY (id_categoria)
        REFERENCES categoria (id_categoria)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_ideia_estado_data (estado, data_submissao),
    INDEX idx_ideia_categoria (id_categoria),
    INDEX idx_ideia_utilizador (id_utilizador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comentario (
    id_comentario INT UNSIGNED AUTO_INCREMENT,
    texto TEXT NOT NULL,
    data_comentario TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_utilizador INT UNSIGNED NOT NULL,
    id_ideia INT UNSIGNED NOT NULL,
    CONSTRAINT pk_comentario PRIMARY KEY (id_comentario),
    CONSTRAINT fk_comentario_utilizador FOREIGN KEY (id_utilizador)
        REFERENCES utilizador (id_utilizador)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_comentario_ideia FOREIGN KEY (id_ideia)
        REFERENCES ideia (id_ideia)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_comentario_ideia_data (id_ideia, data_comentario),
    INDEX idx_comentario_utilizador (id_utilizador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS voto (
    id_voto INT UNSIGNED AUTO_INCREMENT,
    data_voto TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_utilizador INT UNSIGNED NOT NULL,
    id_ideia INT UNSIGNED NOT NULL,
    CONSTRAINT pk_voto PRIMARY KEY (id_voto),
    CONSTRAINT uq_voto_utilizador_ideia UNIQUE (id_utilizador, id_ideia),
    CONSTRAINT fk_voto_utilizador FOREIGN KEY (id_utilizador)
        REFERENCES utilizador (id_utilizador)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_voto_ideia FOREIGN KEY (id_ideia)
        REFERENCES ideia (id_ideia)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_voto_ideia (id_ideia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mensagem_contacto (
    id_mensagem INT UNSIGNED AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL,
    assunto VARCHAR(150) NOT NULL,
    mensagem TEXT NOT NULL,
    estado ENUM('Nova', 'Lida') NOT NULL DEFAULT 'Nova',
    data_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_mensagem_contacto PRIMARY KEY (id_mensagem),
    INDEX idx_mensagem_estado_data (estado, data_envio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pedido_recuperacao (
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
