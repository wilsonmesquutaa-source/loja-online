-- Execute uma única vez no banco de dados já existente.
-- Mantém o zoom escolhido no editor de imagens das categorias.
ALTER TABLE categoria_imagens
    ADD COLUMN escala DECIMAL(4,2) NOT NULL DEFAULT 1.20
    AFTER posicao_y;
