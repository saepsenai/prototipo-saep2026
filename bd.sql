DROP DATABASE IF EXISTS empresa;
CREATE DATABASE empresa CHARACTER SET utf8 COLLATE utf8_danish_ci;
USE empresa;

-- DROP TABLES
DROP TABLE IF EXISTS movimentacao CASCADE;
DROP TABLE IF EXISTS produto CASCADE;

CREATE TABLE produto (
    id_produto INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    quantidade_estoque INTEGER NOT NULL,
    valor_unitario NUMERIC(10,2) NOT NULL,
    data_cadastro DATE NOT NULL DEFAULT CURRENT_DATE
);

CREATE TABLE movimentacao (
    id_movimentacao INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    tipo VARCHAR(10) NOT NULL CHECK (tipo IN ('ENTRADA', 'SAIDA')),
    quantidade INTEGER NOT NULL,
    id_produto INTEGER NOT NULL REFERENCES produto(id_produto),
    datetime_movimentacao DATETIME,
    id_user INTEGER NOT NULL REFERENCES user(id_user)
);

INSERT INTO produto (nome, quantidade_estoque, valor_unitario) VALUES
('Amaciante de roupas', 100, 0.50),
('Detergente', 25, 18.90),
('Sabão em pó', 40, 22.50);


INSERT INTO movimentacao (tipo, quantidade, id_produto, datetime_movimentacao, id_user) VALUES
('ENTRADA', 50, 1),
('SAIDA', 10, 1),
('SAIDA', 5, 2);

select * from produto;