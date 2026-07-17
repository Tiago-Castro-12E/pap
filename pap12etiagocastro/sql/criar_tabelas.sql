CREATE TABLE utilizador (
    id_utilizador INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('aluno','professor','admin') NOT NULL
);

CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome_categoria VARCHAR(100) NOT NULL
);

CREATE TABLE ideia (
    id_ideia INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    data_submissao DATE NOT NULL,
    estado ENUM('Pendente','Aprovada','Rejeitada') DEFAULT 'Pendente',
    id_utilizador INT NOT NULL,
    id_categoria INT NOT NULL,
    FOREIGN KEY (id_utilizador) REFERENCES utilizador(id_utilizador),
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);

CREATE TABLE comentario (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    data_comentario DATE NOT NULL,
    id_utilizador INT NOT NULL,
    id_ideia INT NOT NULL,
    FOREIGN KEY (id_utilizador) REFERENCES utilizador(id_utilizador),
    FOREIGN KEY (id_ideia) REFERENCES ideia(id_ideia)
);

CREATE TABLE voto (
    id_voto INT AUTO_INCREMENT PRIMARY KEY,
    data_voto DATE NOT NULL,
    id_utilizador INT NOT NULL,
    id_ideia INT NOT NULL,
    UNIQUE(id_utilizador,id_ideia),
    FOREIGN KEY (id_utilizador) REFERENCES utilizador(id_utilizador),
    FOREIGN KEY (id_ideia) REFERENCES ideia(id_ideia)
);