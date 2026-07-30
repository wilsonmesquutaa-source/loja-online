-- =====================================================================
-- UC2 - Banco de Dados Relacional
-- Tópico 02: CRUD SQL (DML & DQL)
-- Arquivo de Exercícios para o Aluno (Prática de Fixação)
-- =====================================================================

-- 1. SETUP DE AMBIENTE:
-- Certifique-se de estar com o arquivo 'agenda.db' aberto e a tabela contatos criada.
-- Caso precise recriá-la, rode o comando abaixo:
--
-- CREATE TABLE IF NOT EXISTS contatos (
--     id INTEGER PRIMARY KEY AUTOINCREMENT,
--     nome TEXT NOT NULL,
--     telefone TEXT,
--     email TEXT UNIQUE
-- );

-- =====================================================================
-- SEÇÃO 1: Exemplos Demonstrativos (Acompanhe com o Professor)
-- =====================================================================

-- Exemplo 1: Inserindo dados individuais (DML - INSERT)
INSERT INTO contatos (nome, telefone, email) 
VALUES ('Ana Souza', '85 91111-2222', 'ana.souza@email.com');

-- Exemplo 2: Consulta geral ordenada por ID inverso (DQL - SELECT com ORDER BY)
SELECT * FROM contatos ORDER BY id DESC;

-- Exemplo 3: Alterando um contato específico (DML - UPDATE)
UPDATE contatos 
SET telefone = '85 99999-9999' 
WHERE id = 1;


-- =====================================================================
-- SEÇÃO 2: Exercícios de Fixação (Mão na Massa do Aluno)
-- =====================================================================

-- Tarefa 1: Realize a inserção em lote (Bulk Insert) de 4 contatos diferentes 
-- de uma só vez na tabela 'contatos' (invente nomes, telefones e e-mails).
-- [ESCREVA SUA QUERY ABAIXO]
CREATE TABLE contatos(
ID INTEGER PRIMARY KEY AUTOINCREMENT
nome TEXT NOT NULL,

telefone TEXT,

email TEXT UNIQUE

)
INSERT INTO contatos (nome, telefone, email)
VALUES
('geraldo', '(33)9943-7891', '@geraldin@gmail.com'),

('larissa', '(85)9093-2890', '@lari@gmail.com'),

('leandro', '(33)9753-7034', '@lean23@gmail.com'),

('gustavo', '(95)9943-9894', '@gusta@gmail.com');





-- Tarefa 2: Faça uma consulta (SELECT) que retorne apenas as colunas 
-- 'nome' e 'email' de todos os contatos cadastrados. Aplique um apelido (ALIAS / AS) 
-- para que a coluna 'nome' apareça na listagem como 'Nome do Contato'.
-- [ESCREVA SUA QUERY ABAIXO]
SELECT


-- Tarefa 3: Realize uma busca (SELECT) por todos os contatos cujo nome 
-- contenha a letra "a" (dica: utilize a cláusula WHERE com o operador LIKE e o curinga %).
-- [ESCREVA SUA QUERY ABAIXO]



-- Tarefa 4: Atualize (UPDATE) o e-mail do contato de ID = 2 para 'novo_email@provedor.com'.
-- Lembre-se da REGRA DE OURO de especificar o ID!
-- [ESCREVA SUA QUERY ABAIXO]



-- Tarefa 5: Exclua (DELETE) permanentemente o contato de ID = 4 da tabela.
-- Após rodar o comando, faça um SELECT geral para confirmar a remoção da linha.
-- [ESCREVA SUA QUERY ABAIXO]

