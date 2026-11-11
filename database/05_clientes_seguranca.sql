-- Executar no banco da loja. Migração aditiva e reaplicável.
CREATE TABLE IF NOT EXISTS cliente_recuperacao (
    cliente_id INT UNSIGNED NOT NULL PRIMARY KEY,
    versao_sessao INT UNSIGNED NOT NULL DEFAULT 0,
    token_hash CHAR(64) NULL,
    credencial_hash CHAR(64) NULL,
    expira_em BIGINT UNSIGNED NULL,
    ultimo_envio BIGINT UNSIGNED NOT NULL DEFAULT 0,
    dia_envio CHAR(10) NULL,
    envios_dia INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_recuperacao_token (token_hash),
    CONSTRAINT fk_recuperacao_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IDs históricos sem FK: o registro permanece após a exclusão do cadastro.
CREATE TABLE IF NOT EXISTS cliente_admin_auditoria (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NULL,
    cliente_id INT UNSIGNED NOT NULL,
    acao VARCHAR(40) NOT NULL,
    motivo VARCHAR(40) NOT NULL,
    criado_em BIGINT UNSIGNED NOT NULL,
    KEY idx_auditoria_admin (admin_id, acao, criado_em),
    KEY idx_auditoria_cliente (cliente_id, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
