-- Posições de enfeites independentes para todos os temas.
ALTER TABLE tema_assets MODIFY COLUMN tipo ENUM('logo_desktop','logo_mobile','mascote','favicon','header_overlay','header_background','banner_home','background_site','decoracao_esquerda','decoracao_direita','decoracao_superior','outro','home_pendente','home_moldura','home_divisor','home_vantagens','home_atendimento') NOT NULL;
