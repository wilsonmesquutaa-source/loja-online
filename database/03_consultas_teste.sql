USE loja_virtual_db;

SHOW TABLES;

DESCRIBE categorias;
DESCRIBE produtos;
DESCRIBE pedidos;
DESCRIBE pagamentos;


SELECT
    p.id,
    p.nome AS produto,
    c.nome AS categoria,
    p.preco,
    p.estoque,
    p.status
FROM produtos AS p
INNER JOIN categorias AS c
    ON c.id = p.categoria_id
ORDER BY p.nome;


SELECT
    nome,
    estoque
FROM produtos
WHERE estoque <= 10
ORDER BY estoque;


SELECT
    c.nome AS categoria,
    COUNT(p.id) AS quantidade_produtos
FROM categorias AS c
LEFT JOIN produtos AS p
    ON p.categoria_id = c.id
GROUP BY c.id, c.nome
ORDER BY c.nome;
