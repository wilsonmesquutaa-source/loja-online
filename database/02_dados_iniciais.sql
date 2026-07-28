USE loja_virtual_db;

INSERT INTO categorias (
    nome,
    slug,
    descricao
) VALUES
('Computadores', 'computadores', 'Computadores e notebooks'),
('Periféricos', 'perifericos', 'Mouse, teclado e outros periféricos'),
('Monitores', 'monitores', 'Monitores para uso pessoal e profissional'),
('Armazenamento', 'armazenamento', 'HDs, SSDs e dispositivos externos');


INSERT INTO produtos (
    categoria_id,
    nome,
    slug,
    descricao,
    preco,
    estoque
) VALUES
(
    1,
    'Notebook Profissional 15',
    'notebook-profissional-15',
    'Notebook para estudos, trabalho e desenvolvimento.',
    3599.90,
    12
),
(
    2,
    'Mouse sem fio',
    'mouse-sem-fio',
    'Mouse sem fio com conexão USB.',
    89.90,
    35
),
(
    2,
    'Teclado mecânico',
    'teclado-mecanico',
    'Teclado mecânico com iluminação.',
    279.90,
    18
),
(
    3,
    'Monitor LED 24',
    'monitor-led-24',
    'Monitor LED de 24 polegadas.',
    899.90,
    10
),
(
    4,
    'SSD 1 TB',
    'ssd-1-tb',
    'Unidade de armazenamento de estado sólido.',
    449.90,
    22
);
