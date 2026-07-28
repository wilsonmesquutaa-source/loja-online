CREATE DATABASE IF NOT EXISTS loja_virtual_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE loja_virtual_db;


CREATE TABLE categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL,
    descricao VARCHAR(255) NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_categorias_nome UNIQUE (nome),
    CONSTRAINT uq_categorias_slug UNIQUE (slug)
) ENGINE=InnoDB;


CREATE TABLE produtos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT UNSIGNED NOT NULL,
    nome VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    descricao TEXT NULL,
    preco DECIMAL(10,2) UNSIGNED NOT NULL,
    estoque INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_produtos_slug UNIQUE (slug),

    CONSTRAINT fk_produtos_categorias
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_produtos_categoria (categoria_id),
    INDEX idx_produtos_nome (nome),
    INDEX idx_produtos_status (status)
) ENGINE=InnoDB;


CREATE TABLE produto_imagens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produto_id INT UNSIGNED NOT NULL,
    url_imagem VARCHAR(500) NOT NULL,
    texto_alternativo VARCHAR(255) NULL,
    principal TINYINT(1) NOT NULL DEFAULT 0,
    ordem SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_imagens_produtos
        FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_imagens_produto (produto_id)
) ENGINE=InnoDB;


CREATE TABLE clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    google_sub VARCHAR(255) NOT NULL,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL,
    foto_url VARCHAR(500) NULL,
    email_verificado TINYINT(1) NOT NULL DEFAULT 0,
    ultimo_acesso DATETIME NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_clientes_google_sub UNIQUE (google_sub),
    CONSTRAINT uq_clientes_email UNIQUE (email)
) ENGINE=InnoDB;


CREATE TABLE enderecos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT UNSIGNED NOT NULL,
    identificacao VARCHAR(80) NOT NULL DEFAULT 'Endereço principal',
    destinatario VARCHAR(150) NOT NULL,
    cep VARCHAR(9) NOT NULL,
    logradouro VARCHAR(180) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(120) NULL,
    bairro VARCHAR(120) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    estado CHAR(2) NOT NULL,
    principal TINYINT(1) NOT NULL DEFAULT 0,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_enderecos_clientes
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_enderecos_cliente (cliente_id),
    INDEX idx_enderecos_cep (cep)
) ENGINE=InnoDB;


CREATE TABLE carrinhos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT UNSIGNED NULL,
    token_sessao CHAR(64) NULL,
    status ENUM('aberto', 'convertido', 'abandonado')
        NOT NULL DEFAULT 'aberto',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_carrinhos_token UNIQUE (token_sessao),

    CONSTRAINT fk_carrinhos_clientes
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_carrinhos_cliente (cliente_id),
    INDEX idx_carrinhos_status (status)
) ENGINE=InnoDB;


CREATE TABLE carrinho_itens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    carrinho_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NOT NULL,
    quantidade INT UNSIGNED NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10,2) UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_carrinho_produto
        UNIQUE (carrinho_id, produto_id),

    CONSTRAINT fk_carrinho_itens_carrinhos
        FOREIGN KEY (carrinho_id)
        REFERENCES carrinhos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_carrinho_itens_produtos
        FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_carrinho_itens_carrinho (carrinho_id),
    INDEX idx_carrinho_itens_produto (produto_id)
) ENGINE=InnoDB;


CREATE TABLE pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(40) NOT NULL,
    cliente_id INT UNSIGNED NOT NULL,
    status ENUM(
        'aguardando_pagamento',
        'pago',
        'em_separacao',
        'enviado',
        'entregue',
        'cancelado'
    ) NOT NULL DEFAULT 'aguardando_pagamento',
    subtotal DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
    frete DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
    desconto DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
    observacao VARCHAR(500) NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_pedidos_codigo UNIQUE (codigo),

    CONSTRAINT fk_pedidos_clientes
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_pedidos_cliente (cliente_id),
    INDEX idx_pedidos_status (status),
    INDEX idx_pedidos_criado_em (criado_em)
) ENGINE=InnoDB;


CREATE TABLE pedido_enderecos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    destinatario VARCHAR(150) NOT NULL,
    cep VARCHAR(9) NOT NULL,
    logradouro VARCHAR(180) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(120) NULL,
    bairro VARCHAR(120) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    estado CHAR(2) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_pedido_endereco UNIQUE (pedido_id),

    CONSTRAINT fk_pedido_enderecos_pedidos
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE pedido_itens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NOT NULL,
    nome_produto VARCHAR(150) NOT NULL,
    quantidade INT UNSIGNED NOT NULL,
    preco_unitario DECIMAL(10,2) UNSIGNED NOT NULL,
    subtotal DECIMAL(10,2) UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pedido_itens_pedidos
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_pedido_itens_produtos
        FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_pedido_itens_pedido (pedido_id),
    INDEX idx_pedido_itens_produto (produto_id)
) ENGINE=InnoDB;


CREATE TABLE pagamentos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    provedor VARCHAR(50) NOT NULL DEFAULT 'mercadopago',
    pagamento_externo_id VARCHAR(120) NULL,
    metodo ENUM('pix', 'cartao') NOT NULL,
    status ENUM(
        'pendente',
        'aprovado',
        'recusado',
        'cancelado',
        'reembolsado'
    ) NOT NULL DEFAULT 'pendente',
    valor DECIMAL(10,2) UNSIGNED NOT NULL,
    pix_copia_cola TEXT NULL,
    expira_em DATETIME NULL,
    aprovado_em DATETIME NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_pagamentos_externo
        UNIQUE (pagamento_externo_id),

    CONSTRAINT fk_pagamentos_pedidos
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_pagamentos_pedido (pedido_id),
    INDEX idx_pagamentos_status (status)
) ENGINE=InnoDB;


CREATE TABLE movimentacoes_estoque (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produto_id INT UNSIGNED NOT NULL,
    pedido_id INT UNSIGNED NULL,
    tipo ENUM(
        'entrada',
        'saida',
        'ajuste',
        'reserva',
        'devolucao'
    ) NOT NULL,
    quantidade INT UNSIGNED NOT NULL,
    saldo_anterior INT UNSIGNED NOT NULL,
    saldo_posterior INT UNSIGNED NOT NULL,
    observacao VARCHAR(500) NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_movimentacoes_produtos
        FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_movimentacoes_pedidos
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_movimentacoes_produto (produto_id),
    INDEX idx_movimentacoes_pedido (pedido_id),
    INDEX idx_movimentacoes_tipo (tipo)
) ENGINE=InnoDB;


CREATE TABLE usuarios_admin (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    ultimo_acesso DATETIME NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_usuarios_admin_email UNIQUE (email)
) ENGINE=InnoDB;


CREATE TABLE dispositivos_notificacao (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT UNSIGNED NOT NULL,
    token_fcm VARCHAR(512) NOT NULL,
    plataforma VARCHAR(30) NOT NULL DEFAULT 'web',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    ultimo_acesso DATETIME NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_dispositivos_token UNIQUE (token_fcm),

    CONSTRAINT fk_dispositivos_clientes
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_dispositivos_cliente (cliente_id),
    INDEX idx_dispositivos_ativo (ativo)
) ENGINE=InnoDB;


CREATE TABLE webhook_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provedor VARCHAR(50) NOT NULL,
    evento VARCHAR(100) NOT NULL,
    identificador_externo VARCHAR(120) NULL,
    payload JSON NOT NULL,
    processado TINYINT(1) NOT NULL DEFAULT 0,
    tentativas SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    erro TEXT NULL,
    recebido_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processado_em DATETIME NULL,

    INDEX idx_webhook_provedor (provedor),
    INDEX idx_webhook_evento (evento),
    INDEX idx_webhook_externo (identificador_externo),
    INDEX idx_webhook_processado (processado)
) ENGINE=InnoDB;
