-- Migração aditiva. Não recalcula pedidos antigos nem publica campanhas.
CREATE TABLE IF NOT EXISTS tema_estado_resolvido (
 id TINYINT PRIMARY KEY,
 tema_id BIGINT UNSIGNED NULL,
 atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO tema_estado_resolvido(id,tema_id) VALUES(1,NULL);
CREATE TABLE IF NOT EXISTS pedido_taxas_snapshot (
 pedido_id INT UNSIGNED PRIMARY KEY,
 taxa_pix DECIMAL(8,4) NOT NULL,
 taxa_cartao DECIMAL(8,4) NOT NULL,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(pedido_id) REFERENCES pedidos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS unidades_medida (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 codigo VARCHAR(20) NOT NULL UNIQUE,
 nome VARCHAR(60) NOT NULL,
 tipo ENUM('massa','volume','unidade','outro') NOT NULL,
 casas_decimais TINYINT UNSIGNED DEFAULT 3,
 ativo BOOLEAN DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categorias_insumos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 nome VARCHAR(100) NOT NULL,
 slug VARCHAR(120) NOT NULL UNIQUE,
 ativo BOOLEAN DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS insumos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 categoria_insumo_id BIGINT UNSIGNED NULL,
 nome VARCHAR(150) NOT NULL,
 descricao VARCHAR(500),
 unidade_base_id BIGINT UNSIGNED NOT NULL,
 estoque_atual DECIMAL(15,4) NOT NULL DEFAULT 0,
 estoque_minimo DECIMAL(15,4) NOT NULL DEFAULT 0,
 custo_medio_unitario DECIMAL(18,8) NOT NULL DEFAULT 0,
 metodo_custeio ENUM('medio_ponderado','ultimo_preco') DEFAULT 'medio_ponderado',
 perecivel BOOLEAN DEFAULT 0,
 controla_estoque BOOLEAN DEFAULT 1,
 ativo BOOLEAN DEFAULT 1,
 INDEX(nome),
 INDEX(categoria_insumo_id),
 INDEX(ativo),
 FOREIGN KEY(categoria_insumo_id) REFERENCES categorias_insumos(id),
 FOREIGN KEY(unidade_base_id) REFERENCES unidades_medida(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fornecedores (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 nome_fantasia VARCHAR(150) NOT NULL,
 razao_social VARCHAR(180),
 documento VARCHAR(20),
 telefone VARCHAR(30),
 email VARCHAR(180),
 observacao TEXT,
 ativo BOOLEAN DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fornecedor_insumos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 fornecedor_id BIGINT UNSIGNED NOT NULL,
 insumo_id BIGINT UNSIGNED NOT NULL,
 codigo_fornecedor VARCHAR(100),
 marca VARCHAR(120) NOT NULL DEFAULT '',
 preferencial BOOLEAN DEFAULT 0,
 UNIQUE(fornecedor_id,insumo_id,marca),
 FOREIGN KEY(fornecedor_id) REFERENCES fornecedores(id),
 FOREIGN KEY(insumo_id) REFERENCES insumos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS compras_insumos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 fornecedor_id BIGINT UNSIGNED NULL,
 numero_documento VARCHAR(100),
 data_compra DATE NOT NULL,
 valor_produtos DECIMAL(12,2) NOT NULL,
 frete DECIMAL(12,2) DEFAULT 0,
 desconto DECIMAL(12,2) DEFAULT 0,
 outros_custos DECIMAL(12,2) DEFAULT 0,
 total DECIMAL(12,2) NOT NULL,
 observacao TEXT,
 status ENUM('rascunho','confirmada','cancelada') DEFAULT 'rascunho',
 criado_por_admin_id INT UNSIGNED NULL,
 FOREIGN KEY(fornecedor_id) REFERENCES fornecedores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS compra_insumo_itens (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 compra_id BIGINT UNSIGNED NOT NULL,
 insumo_id BIGINT UNSIGNED NOT NULL,
 unidade_compra_id BIGINT UNSIGNED NOT NULL,
 quantidade_comprada DECIMAL(15,4) NOT NULL,
 fator_para_unidade_base DECIMAL(18,8) NOT NULL,
 quantidade_base DECIMAL(18,4) NOT NULL,
 valor_total_item DECIMAL(12,2) NOT NULL,
 custo_unitario_base DECIMAL(18,8) NOT NULL,
 lote VARCHAR(100),
 validade DATE,
 custo_anterior DECIMAL(18,8) NOT NULL DEFAULT 0,
 INDEX(insumo_id),
 FOREIGN KEY(compra_id) REFERENCES compras_insumos(id),
 FOREIGN KEY(insumo_id) REFERENCES insumos(id),
 FOREIGN KEY(unidade_compra_id) REFERENCES unidades_medida(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS insumo_precos_historico (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 insumo_id BIGINT UNSIGNED NOT NULL,
 compra_item_id BIGINT UNSIGNED NULL,
 custo_unitario DECIMAL(18,8) NOT NULL,
 custo_anterior DECIMAL(18,8),
 variacao_percentual DECIMAL(10,4),
 data_referencia DATETIME NOT NULL,
 INDEX(insumo_id,data_referencia),
 FOREIGN KEY(insumo_id) REFERENCES insumos(id),
 FOREIGN KEY(compra_item_id) REFERENCES compra_insumo_itens(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS estoque_insumos_movimentacoes (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 insumo_id BIGINT UNSIGNED NOT NULL,
 tipo ENUM('entrada_compra','consumo_producao','perda','ajuste_positivo','ajuste_negativo','devolucao') NOT NULL,
 quantidade DECIMAL(15,4) NOT NULL,
 saldo_anterior DECIMAL(15,4) NOT NULL,
 saldo_posterior DECIMAL(15,4) NOT NULL,
 compra_item_id BIGINT UNSIGNED NULL,
 producao_id BIGINT UNSIGNED NULL,
 motivo VARCHAR(255),
 admin_id INT UNSIGNED NULL,
 INDEX(insumo_id,criado_em),
 FOREIGN KEY(insumo_id) REFERENCES insumos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fichas_tecnicas (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 produto_id INT UNSIGNED NOT NULL,
 nome VARCHAR(150) NOT NULL,
 rendimento_quantidade DECIMAL(12,4) NOT NULL,
 rendimento_unidade VARCHAR(30) NOT NULL DEFAULT 'unidade',
 unidades_por_venda DECIMAL(12,4) NOT NULL DEFAULT 1,
 percentual_perda DECIMAL(8,4) DEFAULT 0,
 tempo_preparo_minutos INT UNSIGNED NULL,
 custo_indireto_manual DECIMAL(12,4) NOT NULL DEFAULT 0,
 versao INT UNSIGNED DEFAULT 1,
 ativo BOOLEAN DEFAULT 1,
 observacao TEXT,
 produto_ativo INT UNSIGNED GENERATED ALWAYS AS (IF(ativo=1,produto_id,NULL)) STORED,
 UNIQUE(produto_ativo),
 UNIQUE(produto_id,versao),
 FOREIGN KEY(produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ficha_tecnica_itens (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 ficha_tecnica_id BIGINT UNSIGNED NOT NULL,
 insumo_id BIGINT UNSIGNED NOT NULL,
 quantidade_base DECIMAL(15,4) NOT NULL,
 percentual_perda_extra DECIMAL(8,4) DEFAULT 0,
 tipo ENUM('ingrediente','embalagem') DEFAULT 'ingrediente',
 ordem SMALLINT UNSIGNED DEFAULT 1,
 FOREIGN KEY(ficha_tecnica_id) REFERENCES fichas_tecnicas(id),
 FOREIGN KEY(insumo_id) REFERENCES insumos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS custos_indiretos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 nome VARCHAR(150) NOT NULL,
 categoria ENUM('energia','gas','agua','mao_de_obra','limpeza','manutencao','outro') NOT NULL,
 valor_mensal DECIMAL(12,2) NOT NULL,
 forma_rateio ENUM('por_unidade','por_receita','por_tempo_producao','percentual_custo_direto','manual') NOT NULL,
 percentual_rateio DECIMAL(8,4),
 base_mensal DECIMAL(15,4) DEFAULT 0,
 ativo BOOLEAN DEFAULT 1,
 inicio_vigencia DATE NOT NULL,
 fim_vigencia DATE NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS despesas_operacionais (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 competencia DATE NOT NULL,
 categoria VARCHAR(100) NOT NULL,
 descricao VARCHAR(255) NOT NULL,
 valor DECIMAL(12,2) NOT NULL,
 recorrente BOOLEAN DEFAULT 0,
 observacao TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS produto_custos_snapshot (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 produto_id INT UNSIGNED NOT NULL,
 ficha_tecnica_id BIGINT UNSIGNED NULL,
 custo_ingredientes DECIMAL(12,4) NOT NULL,
 custo_embalagem DECIMAL(12,4) NOT NULL,
 custo_perdas DECIMAL(12,4) NOT NULL,
 custo_indireto_rateado DECIMAL(12,4) NOT NULL,
 custo_total DECIMAL(12,4) NOT NULL,
 custo_unitario DECIMAL(12,6) NOT NULL,
 custo_por_venda DECIMAL(12,6) NOT NULL,
 data_calculo DATETIME NOT NULL,
 origem ENUM('automatico','manual','recalculo') NOT NULL,
 INDEX(produto_id,data_calculo),
 FOREIGN KEY(produto_id) REFERENCES produtos(id),
 FOREIGN KEY(ficha_tecnica_id) REFERENCES fichas_tecnicas(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pedido_item_custos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 pedido_item_id INT UNSIGNED NOT NULL UNIQUE,
 produto_id INT UNSIGNED NOT NULL,
 quantidade INT UNSIGNED NOT NULL,
 custo_unitario_no_momento DECIMAL(12,6) NOT NULL,
 custo_total_no_momento DECIMAL(12,4) NOT NULL,
 custo_indireto_no_momento DECIMAL(12,4) NOT NULL DEFAULT 0,
 snapshot_produto_custo_id BIGINT UNSIGNED NULL,
 INDEX(produto_id),
 FOREIGN KEY(pedido_item_id) REFERENCES pedido_itens(id),
 FOREIGN KEY(produto_id) REFERENCES produtos(id),
 FOREIGN KEY(snapshot_produto_custo_id) REFERENCES produto_custos_snapshot(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS configuracoes_financeiras (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 chave VARCHAR(100) NOT NULL UNIQUE,
 valor VARCHAR(500) NOT NULL,
 tipo ENUM('decimal','inteiro','texto','booleano','json') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS temas_loja (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 nome VARCHAR(120) NOT NULL,
 slug VARCHAR(140) NOT NULL UNIQUE,
 descricao VARCHAR(500),
 tipo ENUM('padrao','sazonal','campanha') NOT NULL,
 status ENUM('rascunho','publicado','arquivado') DEFAULT 'rascunho',
 prioridade SMALLINT UNSIGNED DEFAULT 100,
 ativo_manual BOOLEAN DEFAULT 0,
 permitir_agendamento BOOLEAN DEFAULT 1,
 criado_por_admin_id INT UNSIGNED NULL,
 padrao_unico TINYINT GENERATED ALWAYS AS (IF(tipo='padrao',1,NULL)) STORED,
 UNIQUE(padrao_unico),
 INDEX(status,ativo_manual,prioridade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tema_paletas (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 tema_id BIGINT UNSIGNED NOT NULL UNIQUE,
 cor_primaria CHAR(7),
 cor_secundaria CHAR(7),
 cor_destaque CHAR(7),
 cor_fundo CHAR(7),
 cor_texto CHAR(7),
 cor_header_inicio CHAR(7),
 cor_header_fim CHAR(7),
 cor_footer CHAR(7),
 FOREIGN KEY(tema_id) REFERENCES temas_loja(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tema_assets (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 tema_id BIGINT UNSIGNED NOT NULL,
 tipo ENUM('logo_desktop','logo_mobile','mascote','favicon','header_overlay','header_background','banner_home','background_site','decoracao_esquerda','decoracao_direita','decoracao_superior','outro') NOT NULL,
 url_arquivo VARCHAR(500) NOT NULL,
 texto_alternativo VARCHAR(255),
 posicao_x DECIMAL(5,2) DEFAULT 50,
 posicao_y DECIMAL(5,2) DEFAULT 50,
 escala DECIMAL(5,2) DEFAULT 1,
 opacidade DECIMAL(5,2) DEFAULT 1,
 ordem SMALLINT UNSIGNED DEFAULT 1,
 ativo BOOLEAN DEFAULT 1,
 INDEX(tema_id,tipo,ativo),
 FOREIGN KEY(tema_id) REFERENCES temas_loja(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tema_componentes (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 tema_id BIGINT UNSIGNED NOT NULL,
 componente VARCHAR(100) NOT NULL,
 variante VARCHAR(100),
 ativo BOOLEAN DEFAULT 1,
 configuracao_json LONGTEXT,
 ordem SMALLINT UNSIGNED DEFAULT 1,
 FOREIGN KEY(tema_id) REFERENCES temas_loja(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tema_agendamentos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 tema_id BIGINT UNSIGNED NOT NULL,
 tipo_regra ENUM('intervalo_fixo','anual_fixo','regra_calendario','manual') NOT NULL,
 inicio_datetime DATETIME,
 fim_datetime DATETIME,
 mes_inicio TINYINT UNSIGNED,
 dia_inicio TINYINT UNSIGNED,
 mes_fim TINYINT UNSIGNED,
 dia_fim TINYINT UNSIGNED,
 regra_codigo VARCHAR(100),
 dias_antes SMALLINT UNSIGNED DEFAULT 0,
 dias_depois SMALLINT UNSIGNED DEFAULT 0,
 prioridade SMALLINT UNSIGNED DEFAULT 100,
 ativo BOOLEAN DEFAULT 1,
 INDEX(ativo,inicio_datetime,fim_datetime),
 INDEX(regra_codigo),
 FOREIGN KEY(tema_id) REFERENCES temas_loja(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tema_publicacoes (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 tema_id BIGINT UNSIGNED NOT NULL,
 acao ENUM('publicado','ativado','desativado','agendado','cancelado','restaurado') NOT NULL,
 admin_id INT UNSIGNED NULL,
 origem ENUM('manual','agendamento','sistema') NOT NULL,
 detalhes TEXT,
 FOREIGN KEY(tema_id) REFERENCES temas_loja(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tema_configuracoes (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 tema_id BIGINT UNSIGNED NOT NULL,
 chave VARCHAR(120) NOT NULL,
 valor TEXT,
 tipo ENUM('texto','numero','booleano','cor','json') NOT NULL,
 UNIQUE(tema_id,chave),
 FOREIGN KEY(tema_id) REFERENCES temas_loja(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS biblioteca_visual (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 nome VARCHAR(150) NOT NULL,
 categoria VARCHAR(100) NOT NULL,
 url_arquivo VARCHAR(500) NOT NULL,
 mime_type VARCHAR(100) NOT NULL,
 largura INT UNSIGNED,
 altura INT UNSIGNED,
 tamanho_bytes BIGINT UNSIGNED,
 possui_transparencia BOOLEAN DEFAULT 0,
 ativo BOOLEAN DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS modulos_auditoria (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 admin_id INT UNSIGNED NULL,
 modulo VARCHAR(40) NOT NULL,
 entidade VARCHAR(80) NOT NULL,
 entidade_id BIGINT UNSIGNED NULL,
 acao VARCHAR(60) NOT NULL,
 dados_antes_json LONGTEXT,
 dados_depois_json LONGTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO unidades_medida(codigo,nome,tipo) VALUES ('g','Grama','massa'),('kg','Quilograma','massa'),('ml','Mililitro','volume'),('l','Litro','volume'),('un','Unidade','unidade'),('pacote','Pacote','unidade'),('caixa','Caixa','unidade');
INSERT IGNORE INTO categorias_insumos(nome,slug) VALUES ('Ingredientes','ingredientes'),('Embalagens','embalagens');
INSERT IGNORE INTO configuracoes_financeiras(chave,valor,tipo) VALUES ('percentual_taxa_pix','0','decimal'),('percentual_taxa_cartao','0','decimal'),('meta_margem_bruta','40','decimal'),('meta_margem_contribuicao','30','decimal');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Padrão','padrao','padrao','publicado');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Natal','natal','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('São João','sao-joao','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Ano Novo','ano-novo','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Carnaval','carnaval','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Páscoa','pascoa','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Dia das Mães','dia-das-maes','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Dia dos Namorados','dia-dos-namorados','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Dia dos Pais','dia-dos-pais','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Dia das Crianças','dia-das-criancas','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Halloween','halloween','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Black Friday','black-friday','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Férias','ferias','sazonal','rascunho');
INSERT IGNORE INTO temas_loja(nome,slug,tipo,status) VALUES ('Volta às aulas','volta-as-aulas','sazonal','rascunho');

-- Paletas e decorações iniciais. Campanhas permanecem em rascunho.
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#8c252b','#244c38','#edc35c','#8c252b','#244c38' FROM temas_loja WHERE slug='natal';
INSERT INTO tema_componentes(tema_id,componente,ativo) SELECT t.id,'header_luzes',1 FROM temas_loja t WHERE t.slug='natal' AND NOT EXISTS(SELECT 1 FROM tema_componentes c WHERE c.tema_id=t.id AND c.componente='header_luzes');
INSERT INTO tema_componentes(tema_id,componente,ativo) SELECT t.id,'efeito_neve',1 FROM temas_loja t WHERE t.slug='natal' AND NOT EXISTS(SELECT 1 FROM tema_componentes c WHERE c.tema_id=t.id AND c.componente='efeito_neve');
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#783915','#a84c14','#ffd15b','#783915','#a84c14' FROM temas_loja WHERE slug='sao-joao';
INSERT INTO tema_componentes(tema_id,componente,ativo) SELECT t.id,'header_bandeirinhas',1 FROM temas_loja t WHERE t.slug='sao-joao' AND NOT EXISTS(SELECT 1 FROM tema_componentes c WHERE c.tema_id=t.id AND c.componente='header_bandeirinhas');
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#514632','#796948','#ecd387','#514632','#796948' FROM temas_loja WHERE slug='ano-novo';
INSERT INTO tema_componentes(tema_id,componente,ativo) SELECT t.id,'estrelas',1 FROM temas_loja t WHERE t.slug='ano-novo' AND NOT EXISTS(SELECT 1 FROM tema_componentes c WHERE c.tema_id=t.id AND c.componente='estrelas');
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#5c286e','#a53770','#ffd457','#5c286e','#a53770' FROM temas_loja WHERE slug='carnaval';
INSERT INTO tema_componentes(tema_id,componente,ativo) SELECT t.id,'efeito_confete',1 FROM temas_loja t WHERE t.slug='carnaval' AND NOT EXISTS(SELECT 1 FROM tema_componentes c WHERE c.tema_id=t.id AND c.componente='efeito_confete');
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#59402f','#856149','#efd098','#59402f','#856149' FROM temas_loja WHERE slug='pascoa';
INSERT INTO tema_componentes(tema_id,componente,ativo) SELECT t.id,'estrelas',1 FROM temas_loja t WHERE t.slug='pascoa' AND NOT EXISTS(SELECT 1 FROM tema_componentes c WHERE c.tema_id=t.id AND c.componente='estrelas');
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#782d46','#a64e64','#f0b5c0','#782d46','#a64e64' FROM temas_loja WHERE slug='dia-das-maes';
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#7c2638','#ac4354','#f0b8a8','#7c2638','#ac4354' FROM temas_loja WHERE slug='dia-dos-namorados';
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#234454','#456878','#e6c781','#234454','#456878' FROM temas_loja WHERE slug='dia-dos-pais';
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#3e417a','#754d92','#ffe171','#3e417a','#754d92' FROM temas_loja WHERE slug='dia-das-criancas';
INSERT INTO tema_componentes(tema_id,componente,ativo) SELECT t.id,'efeito_confete',1 FROM temas_loja t WHERE t.slug='dia-das-criancas' AND NOT EXISTS(SELECT 1 FROM tema_componentes c WHERE c.tema_id=t.id AND c.componente='efeito_confete');
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#392344','#653461','#f0a348','#392344','#653461' FROM temas_loja WHERE slug='halloween';
INSERT INTO tema_componentes(tema_id,componente,ativo) SELECT t.id,'estrelas',1 FROM temas_loja t WHERE t.slug='halloween' AND NOT EXISTS(SELECT 1 FROM tema_componentes c WHERE c.tema_id=t.id AND c.componente='estrelas');
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#242424','#424242','#f4c450','#242424','#424242' FROM temas_loja WHERE slug='black-friday';
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#255761','#3e7980','#ffd37d','#255761','#3e7980' FROM temas_loja WHERE slug='ferias';
INSERT IGNORE INTO tema_paletas(tema_id,cor_primaria,cor_secundaria,cor_destaque,cor_header_inicio,cor_header_fim) SELECT id,'#285544','#437e61','#f7d46e','#285544','#437e61' FROM temas_loja WHERE slug='volta-as-aulas';
