-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28-Ago-2026 às 21:25
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `loja_virtual_db`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinhos`
--

CREATE TABLE `carrinhos` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED DEFAULT NULL,
  `token_sessao` char(64) DEFAULT NULL,
  `status` enum('aberto','convertido','abandonado') NOT NULL DEFAULT 'aberto',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `carrinhos`
--

INSERT INTO `carrinhos` (`id`, `cliente_id`, `token_sessao`, `status`, `criado_em`, `atualizado_em`) VALUES
(9, NULL, 'd913720a160ec25d1a2878bf3ca0e785d4464ad1be39f5d4d6c0ba63aedf82b9', 'aberto', '2026-08-13 17:23:10', '2026-08-13 17:23:10'),
(13, NULL, '85e72be766cdebcbf6d658445bd2ecd07458b7fc44293adbda8ae2624e544bf4', 'aberto', '2026-08-20 18:17:17', '2026-08-20 18:17:17'),
(16, NULL, '9443b9b43c2117d342e912e9ece3db43c374c6889e981aaf2f7935bf9000f7ef', 'aberto', '2026-08-24 19:23:49', '2026-08-24 19:23:49'),
(20, NULL, '346300a4bf037d2ecf08dd1772a9f7fcfc0add295bb40633f36be2cd4925c731', 'aberto', '2026-08-25 18:06:57', '2026-08-25 18:06:57'),
(25, NULL, 'c96a3dd01f642c1a129acb3772103a14b33153d4675bf43723a955b0ae14c8b5', 'aberto', '2026-08-26 19:02:55', '2026-08-26 19:02:55'),
(26, 15, '603478fd1d0e64afbbcfba514c9a9a5285eb72e509cfbff2ea034143511e8408', 'aberto', '2026-08-26 19:07:34', '2026-08-26 19:08:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinho_itens`
--

CREATE TABLE `carrinho_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `carrinho_id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `quantidade` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) UNSIGNED NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `carrinho_itens`
--

INSERT INTO `carrinho_itens` (`id`, `carrinho_id`, `produto_id`, `quantidade`, `preco_unitario`, `criado_em`, `atualizado_em`) VALUES
(27, 9, 22, 3, '15.00', '2026-08-13 17:23:31', '2026-08-13 17:23:31'),
(28, 9, 14, 1, '15.00', '2026-08-13 17:23:31', '2026-08-13 17:23:31'),
(40, 13, 24, 1, '15.00', '2026-08-20 18:17:17', '2026-08-20 18:17:17'),
(41, 13, 21, 1, '15.00', '2026-08-20 18:17:17', '2026-08-20 18:17:17'),
(42, 13, 22, 1, '15.00', '2026-08-20 18:17:17', '2026-08-20 18:17:17'),
(43, 13, 11, 1, '15.00', '2026-08-20 18:17:17', '2026-08-20 18:17:17'),
(52, 16, 24, 1, '15.00', '2026-08-24 19:23:49', '2026-08-24 19:23:49'),
(53, 16, 68, 1, '15.00', '2026-08-24 19:23:49', '2026-08-24 19:23:49'),
(54, 16, 69, 1, '15.00', '2026-08-24 19:23:49', '2026-08-24 19:23:49'),
(55, 16, 67, 1, '15.00', '2026-08-24 19:23:49', '2026-08-24 19:23:49'),
(72, 20, 24, 4, '15.00', '2026-08-25 18:06:57', '2026-08-25 18:06:57'),
(77, 25, 24, 4, '15.00', '2026-08-26 19:02:55', '2026-08-26 19:02:55'),
(78, 26, 33, 2, '70.00', '2026-08-26 19:07:34', '2026-08-26 19:07:34');

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `preco_revenda` decimal(10,2) DEFAULT NULL,
  `quantidade_minima_revenda` int(10) UNSIGNED DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `destaque` tinyint(1) NOT NULL DEFAULT 0,
  `ordem_destaque` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `slug`, `descricao`, `preco`, `preco_revenda`, `quantidade_minima_revenda`, `ativo`, `destaque`, `ordem_destaque`, `criado_em`, `atualizado_em`) VALUES
(1, 'Cento de Salgados Tradicionais', 'salgados-tradicionais', 'Coxinha, risoles, bolinhas de queijo, pastéis, croquetes e outros salgados clássicos para festas e eventos.', '60.00', NULL, NULL, 1, 1, 0, '2026-07-28 00:09:17', '2026-08-27 19:18:02'),
(2, 'Cento de Salgado Folhados', 'folhados', 'Croissants e roletes preparados com massa folhada leve e crocante.', '140.00', NULL, NULL, 1, 0, 0, '2026-07-28 00:09:17', '2026-08-07 17:18:18'),
(3, 'Salgados Grandes', 'salgados-grandes', 'Salgados maiores para consumo individual ou revenda, com opções de sabores variados.', '5.00', '3.00', 10, 1, 0, 0, '2026-07-28 00:09:17', '2026-07-30 11:56:13'),
(4, 'Empadões', 'empadoes', 'Empadões artesanais preparados com massa delicada e recheios saborosos.', '100.00', NULL, NULL, 1, 0, 0, '2026-07-29 21:37:35', '2026-07-30 11:56:13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria_imagens`
--

CREATE TABLE `categoria_imagens` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoria_id` int(10) UNSIGNED NOT NULL,
  `url_imagem` varchar(500) NOT NULL,
  `texto_alternativo` varchar(255) DEFAULT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `posicao_x` decimal(5,2) NOT NULL DEFAULT 50.00,
  `posicao_y` decimal(5,2) NOT NULL DEFAULT 50.00,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `categoria_imagens`
--

INSERT INTO `categoria_imagens` (`id`, `categoria_id`, `url_imagem`, `texto_alternativo`, `principal`, `ordem`, `posicao_x`, `posicao_y`, `criado_em`) VALUES
(4, 4, '/assets/uploads/categorias/categoria_4_50032245f729decd.png', 'Imagem de Empadões', 1, 1, '72.44', '36.03', '2026-08-27 19:06:42'),
(5, 1, '/assets/uploads/categorias/categoria_1_6c1d0e6de2a34ca0.png', 'Imagem de Cento de Salgados Tradicionais', 1, 1, '62.29', '15.92', '2026-08-27 19:11:12'),
(6, 3, '/assets/uploads/categorias/categoria_3_09154d6e4233e0f5.jpg', 'Imagem de Salgados Grandes', 1, 1, '54.99', '60.56', '2026-08-27 19:16:34'),
(7, 2, '/assets/uploads/categorias/categoria_2_94f1bdc60a75265d.png', 'Imagem de Cento de Salgado Folhados', 1, 1, '61.40', '85.10', '2026-08-28 16:46:45');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(10) UNSIGNED NOT NULL,
  `google_sub` varchar(255) DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(180) NOT NULL,
  `senha_hash` varchar(255) DEFAULT NULL,
  `foto_url` varchar(500) DEFAULT NULL,
  `email_verificado` tinyint(1) NOT NULL DEFAULT 0,
  `token_verificacao_email` char(64) DEFAULT NULL,
  `token_verificacao_expira_em` datetime DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id`, `google_sub`, `nome`, `email`, `senha_hash`, `foto_url`, `email_verificado`, `token_verificacao_email`, `token_verificacao_expira_em`, `ultimo_acesso`, `criado_em`, `atualizado_em`) VALUES
(15, NULL, 'Will', 'wilson_leonardo_mf@hotmail.com', '$2y$10$Dw5tlYMV69.AXjBRQwCoFONPPfyObpI2qlzXaG66MtLhO17FC3yLq', NULL, 1, NULL, NULL, '2026-08-26 16:08:15', '2026-08-26 19:08:02', '2026-08-26 19:08:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `dispositivos_notificacao`
--

CREATE TABLE `dispositivos_notificacao` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `token_fcm` varchar(512) NOT NULL,
  `plataforma` varchar(30) NOT NULL DEFAULT 'web',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `identificacao` varchar(80) NOT NULL DEFAULT 'Endereço principal',
  `destinatario` varchar(150) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(180) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(120) DEFAULT NULL,
  `bairro` varchar(120) NOT NULL,
  `cidade` varchar(120) NOT NULL,
  `estado` char(2) NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `enderecos`
--

INSERT INTO `enderecos` (`id`, `cliente_id`, `identificacao`, `destinatario`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `principal`, `criado_em`, `atualizado_em`) VALUES
(14, 15, 'Minha casa', 'Will', '60532530', 'Rua 914', '36', 'casa', 'Conjunto Ceará II', 'Fortaleza', 'CE', 1, '2026-08-26 19:08:02', '2026-08-26 19:08:02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `movimentacoes_estoque`
--

CREATE TABLE `movimentacoes_estoque` (
  `id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('entrada','saida','ajuste','reserva','devolucao') NOT NULL,
  `quantidade` int(10) UNSIGNED NOT NULL,
  `saldo_anterior` int(10) UNSIGNED NOT NULL,
  `saldo_posterior` int(10) UNSIGNED NOT NULL,
  `observacao` varchar(500) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `provedor` varchar(50) NOT NULL DEFAULT 'mercadopago',
  `pagamento_externo_id` varchar(120) DEFAULT NULL,
  `metodo` enum('pix','cartao') NOT NULL,
  `status` enum('pendente','aprovado','recusado','cancelado','reembolsado') NOT NULL DEFAULT 'pendente',
  `valor` decimal(10,2) UNSIGNED NOT NULL,
  `pix_copia_cola` text DEFAULT NULL,
  `expira_em` datetime DEFAULT NULL,
  `aprovado_em` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(40) NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `modalidade_recebimento` enum('entrega','retirada') NOT NULL DEFAULT 'entrega',
  `data_hora_agendada` datetime DEFAULT NULL,
  `inicio_preparo` datetime DEFAULT NULL,
  `fim_preparo_previsto` datetime DEFAULT NULL,
  `status` enum('aguardando_pagamento','pago','em_separacao','enviado','entregue','cancelado') NOT NULL DEFAULT 'aguardando_pagamento',
  `subtotal` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `frete` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `desconto` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `observacao` varchar(500) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido_enderecos`
--

CREATE TABLE `pedido_enderecos` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `destinatario` varchar(150) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(180) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(120) DEFAULT NULL,
  `bairro` varchar(120) NOT NULL,
  `cidade` varchar(120) NOT NULL,
  `estado` char(2) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `nome_produto` varchar(150) NOT NULL,
  `quantidade` int(10) UNSIGNED NOT NULL,
  `preco_unitario` decimal(10,2) UNSIGNED NOT NULL,
  `subtotal` decimal(10,2) UNSIGNED NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoria_id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo_preparo` enum('frito','forno','folhado','empadao') NOT NULL DEFAULT 'forno',
  `estoque` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `categoria_id`, `nome`, `slug`, `descricao`, `tipo_preparo`, `estoque`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 1, '25-Coxinha de Frango pequena', 'coxinha-de-frango-pequena', 'Massa artesanal macia e crocante, recheada com frango temperado e preparada com sabor caseiro.', 'frito', 12, 'ativo', '2026-07-28 00:09:17', '2026-08-24 19:06:03'),
(2, 1, '25-Bolinha de queijo', 'bolinha-de-queijo-pequena', 'Massa artesanal recheada com queijo derretido e sabor irresistível.', 'frito', 35, 'ativo', '2026-07-28 00:09:17', '2026-08-24 19:06:03'),
(7, 1, '25- Risole misto pequeno', '25-risole-misto-pequena', 'Massa artesanal recheada com presunto e queijo em uma combinação clássica.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(8, 1, '25- Risole de queijo pequeno', '25-risole-de-queijo-pequena', 'Massa artesanal recheada com queijo cremoso e sabor marcante.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(9, 1, '25- Risole de carne pequeno', '25-risole-de-carne-pequena', 'Massa artesanal recheada com carne bem temperada e preparada artesanalmente.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(10, 1, '25- Risole de frango pequeno', '25-risole-de-frango-pequena', 'Massa artesanal recheada com frango temperado e suculento.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(11, 1, '25- Pastel de carne pequeno', '25-pastel-de-carne-pequena', 'Pastel frito com massa crocante e recheio de carne bovina temperada.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(12, 1, '25- Pastel de queijo pequeno', '25-pastel-de-queijo-pequena', 'Pastel crocante recheado com queijo derretido e saboroso.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(13, 1, '25- Pastel misto pequeno', '25-pastel-misto-pequena', 'Pastel crocante recheado com a combinação tradicional de presunto e queijo.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(14, 1, '25- Pastel de frango pequeno', '25-pastel-de-frango-pequena', 'Pastel dourado recheado com frango temperado e preparado com cuidado.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(15, 2, '50- Croissant misto pequeno', '50-croissant-misto-pequena', 'Massa folhada leve e crocante, recheada com presunto e queijo.', 'folhado', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:07:02'),
(16, 2, '50- Croissant de queijo pequeno', '50-croissant-de-queijo-pequena', 'Croissant folhado com recheio de queijo.', 'folhado', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:07:02'),
(17, 2, '50- Croissant de frango pequeno', '50-croissant-de-frango-pequena', 'Croissant folhado recheado com frango temperado e sabor caseiro.', 'folhado', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:07:02'),
(18, 2, '50- Rolete misto pequeno', '50-rolete-misto-pequena', 'Rolete dourado e recheio de presunto e queijo.', 'folhado', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:07:02'),
(19, 2, '50- Rolete de queijo pequeno', '50-rolete-de-queijo-pequena', 'Rolete recheado com queijo derretido e saboroso.', 'folhado', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:07:02'),
(20, 2, '50- Rolete de frango pequeno', '50-rolete-de-frango-pequena', 'Rolete dourado recheado com frango bem temperado.', 'folhado', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:07:02'),
(21, 1, '25- Croquete de salsicha pequeno', '25-croquete-de-salsicha-pequena', 'Croquete crocante com recheio saboroso de salsicha.', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(22, 1, '25- Empada de frango pequena', '25-empada-de-frango-pequena', 'Massa delicada e saborosa, recheada com frango cremoso e bem temperado.', 'forno', 0, 'ativo', '2026-07-29 22:03:54', '2026-07-29 23:30:26'),
(23, 1, '25- Romeu e Julieta pequeno', '25-romeu-e-julieta-pequena', 'Salgado doce com a combinação clássica de queijo e goiabada.', 'forno', 0, 'ativo', '2026-07-29 22:03:54', '2026-07-29 23:30:26'),
(24, 1, '25- Canudinho de frango', '25-canudinho-de-frango-pequena', 'Massa crocante em formato de canudo, recheada com creme de frango e finalizado com queijo coalho ralado', 'frito', 0, 'ativo', '2026-07-29 22:03:54', '2026-08-24 19:06:03'),
(33, 2, '50- Croissant de carne do sol pequeno', '50-croissant-de-carne-do-sol-pequena', 'Croissant folhado recheado com carne do sol bem temperada.', 'folhado', 0, 'ativo', '2026-07-29 22:42:46', '2026-08-24 19:07:02'),
(34, 2, '50- Rolete de carne do sol pequeno', '50-rolete-de-carne-do-sol-pequena', 'Rolete crocante recheado com carne do sol temperada.', 'folhado', 0, 'ativo', '2026-07-29 22:42:46', '2026-08-24 19:07:02'),
(55, 3, 'Coxinha de frango', 'coxinha-de-frango', 'Massa artesanal preparada com cuidado, recheada com frango temperado e suculento.', 'frito', 0, 'ativo', '2026-07-29 23:42:06', '2026-08-24 19:07:16'),
(56, 3, 'Bolinha mista', 'bolinha-mista', 'Massa artesanal preparada com cuidado, recheada com a combinação de presunto e queijo.', 'frito', 0, 'ativo', '2026-07-29 23:42:06', '2026-08-24 19:07:16'),
(57, 3, 'Risole de carne', 'risole-de-carne', 'Massa artesanal preparada com cuidado, recheada com carne bem temperada.', 'frito', 0, 'ativo', '2026-07-29 23:42:06', '2026-08-24 19:07:16'),
(58, 3, 'Enrolada de salsicha', 'enrolada-de-salsicha', 'Massa artesanal preparada com cuidado, recheada com salsicha de sabor marcante.', 'frito', 0, 'ativo', '2026-07-29 23:42:06', '2026-08-24 19:07:16'),
(59, 3, 'Croissant de frango', 'croissant-de-frango', 'Massa artesanal preparada com cuidado, recheada com frango temperado e sabor caseiro.', 'forno', 0, 'ativo', '2026-07-29 23:42:06', '2026-07-29 23:42:06'),
(60, 3, 'Croissant de queijo', 'croissant-de-queijo', 'Massa artesanal preparada com cuidado, recheada com queijo cremoso e saboroso.', 'forno', 0, 'ativo', '2026-07-29 23:42:06', '2026-07-29 23:42:06'),
(61, 3, 'Pão pizza', 'pao-pizza', 'Massa artesanal preparada com cuidado, recheada com ingredientes tradicionais de pizza.', 'forno', 0, 'ativo', '2026-07-29 23:42:06', '2026-07-29 23:42:06'),
(62, 3, 'Empada', 'empada', 'Massa artesanal preparada com cuidado, recheada com saboroso recheio preparado de forma caseira.', 'forno', 0, 'ativo', '2026-07-29 23:42:06', '2026-07-29 23:42:06'),
(63, 3, 'Hambúrguer frito', 'hamburguer-frito', 'Massa artesanal preparada com cuidado, recheada com hambúrguer e ingredientes selecionados.', 'frito', 0, 'ativo', '2026-07-29 23:42:06', '2026-08-24 19:07:16'),
(64, 3, 'Pastelão de queijo', 'pastelao-de-queijo', 'Massa artesanal preparada com cuidado, recheada com queijo saboroso.', 'frito', 0, 'ativo', '2026-07-29 23:42:06', '2026-08-24 19:07:16'),
(65, 4, 'Empadão de Frango', 'empadao-de-frango', 'Massa podre artesanal, recheada com frango cremoso e temperado, preparado com sabor caseiro.', 'empadao', 0, 'ativo', '2026-07-30 14:30:01', '2026-08-24 19:07:42'),
(67, 1, '25- Croissant Misto Pequeno', 'croissant-misto', 'Croissant feito com massa artesanal tradicional recheado com queijo e presunto', 'forno', 0, 'ativo', '2026-08-24 18:30:43', '2026-08-24 18:32:02'),
(68, 1, '25- Croissant de Queijo Pequeno', 'croissant-queijo', 'Croissant feito com massa artesanal tradicional recheado com queijo.', 'forno', 0, 'ativo', '2026-08-24 18:32:46', '2026-08-24 18:33:39'),
(69, 1, '25- Croissant Frango Pequeno', 'croissant-frango', 'Croissant feito com massa artesanal tradicional recheado com frango desfiado', 'forno', 0, 'ativo', '2026-08-24 18:33:29', '2026-08-24 18:33:48');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produto_imagens`
--

CREATE TABLE `produto_imagens` (
  `id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `url_imagem` varchar(500) NOT NULL,
  `texto_alternativo` varchar(255) DEFAULT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `posicao_x` decimal(5,2) NOT NULL DEFAULT 50.00,
  `posicao_y` decimal(5,2) NOT NULL DEFAULT 50.00,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `produto_imagens`
--

INSERT INTO `produto_imagens` (`id`, `produto_id`, `url_imagem`, `texto_alternativo`, `principal`, `ordem`, `posicao_x`, `posicao_y`, `criado_em`) VALUES
(1, 1, '/assets/uploads/produtos/produto_1_5912ba00ceb950a0.jpg', 'Imagem de 25-Coxinha de Frango pequena', 1, 1, '43.41', '55.11', '2026-08-28 17:33:20'),
(2, 69, '/assets/uploads/produtos/produto_69_d5e29243dc64fa4a.jpg', 'Imagem de 25- Croissant Frango Pequeno', 1, 1, '59.62', '59.54', '2026-08-28 17:38:51'),
(3, 68, '/assets/uploads/produtos/produto_68_976de4bec507adfd.jpg', 'Imagem de 25- Croissant de Queijo Pequeno', 1, 1, '50.00', '50.00', '2026-08-28 17:40:44'),
(4, 67, '/assets/uploads/produtos/produto_67_d3ed20708a9f4916.jpg', 'Imagem de 25- Croissant Misto Pequeno', 1, 1, '43.59', '65.33', '2026-08-28 18:11:55'),
(5, 23, '/assets/uploads/produtos/produto_23_ddf040175dfd1a58.jpg', 'Imagem de 25- Romeu e Julieta pequeno', 1, 1, '50.00', '50.00', '2026-08-28 18:17:45'),
(6, 22, '/assets/uploads/produtos/produto_22_0679d4384878b91b.jpg', 'Imagem de 25- Empada de frango pequena', 1, 1, '50.00', '50.00', '2026-08-28 18:20:49'),
(7, 21, '/assets/uploads/produtos/produto_21_42b150b5e14c617f.jpg', 'Imagem de 25- Croquete de salsicha pequeno', 1, 1, '50.00', '50.00', '2026-08-28 18:35:32'),
(8, 11, '/assets/uploads/produtos/produto_11_a2db536b23402a3e.jpg', 'Imagem de 25- Pastel de carne pequeno', 1, 1, '50.00', '50.00', '2026-08-28 18:37:36'),
(9, 13, '/assets/uploads/produtos/produto_13_c9111a3e4b2b0613.jpg', 'Imagem de 25- Pastel misto pequeno', 1, 1, '50.00', '50.00', '2026-08-28 18:47:34'),
(10, 12, '/assets/uploads/produtos/produto_12_344d38be1848c4ad.jpg', 'Imagem de 25- Pastel de queijo pequeno', 1, 1, '50.00', '50.00', '2026-08-28 18:51:19'),
(11, 14, '/assets/uploads/produtos/produto_14_d725e0934cff5881.jpg', 'Imagem de 25- Pastel de frango pequeno', 1, 1, '50.00', '50.00', '2026-08-28 18:56:40'),
(12, 10, '/assets/uploads/produtos/produto_10_0035866aa1c2da43.jpg', 'Imagem de 25- Risole de frango pequeno', 1, 1, '50.00', '50.00', '2026-08-28 19:00:11'),
(13, 9, '/assets/uploads/produtos/produto_9_f6df70fd4c383227.jpg', 'Imagem de 25- Risole de carne pequeno', 1, 1, '50.00', '50.00', '2026-08-28 19:01:36'),
(14, 8, '/assets/uploads/produtos/produto_8_5248c011baacb943.jpg', 'Imagem de 25- Risole de queijo pequeno', 1, 1, '50.00', '50.00', '2026-08-28 19:02:58'),
(15, 7, '/assets/uploads/produtos/produto_7_28d082169de594e6.jpg', 'Imagem de 25- Risole misto pequeno', 1, 1, '50.00', '50.00', '2026-08-28 19:04:32'),
(16, 2, '/assets/uploads/produtos/produto_2_95f45069f125ce0e.jpg', 'Imagem de 25-Bolinha de queijo', 1, 1, '50.00', '50.00', '2026-08-28 19:06:32'),
(17, 24, '/assets/uploads/produtos/produto_24_3dc7c1aab05992eb.jpg', 'Imagem de 25- Canudinho de frango', 1, 1, '50.00', '50.00', '2026-08-28 19:13:57');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios_admin`
--

CREATE TABLE `usuarios_admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(180) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `usuarios_admin`
--

INSERT INTO `usuarios_admin` (`id`, `nome`, `email`, `senha_hash`, `status`, `ultimo_acesso`, `criado_em`, `atualizado_em`) VALUES
(2, 'Will Mesquita', 'admin@admin.com', '$2y$10$ncHglzOVOSt.b3ROUZXUzO5kDkukSoyXLNTF8OTXL.yhQbAvYAOcq', 'ativo', '2026-08-28 13:31:00', '2026-08-03 17:59:28', '2026-08-28 16:31:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `webhook_logs`
--

CREATE TABLE `webhook_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `provedor` varchar(50) NOT NULL,
  `evento` varchar(100) NOT NULL,
  `identificador_externo` varchar(120) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `processado` tinyint(1) NOT NULL DEFAULT 0,
  `tentativas` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `erro` text DEFAULT NULL,
  `recebido_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `processado_em` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carrinhos_token` (`token_sessao`),
  ADD KEY `idx_carrinhos_cliente` (`cliente_id`),
  ADD KEY `idx_carrinhos_status` (`status`);

--
-- Índices para tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carrinho_produto` (`carrinho_id`,`produto_id`),
  ADD KEY `idx_carrinho_itens_carrinho` (`carrinho_id`),
  ADD KEY `idx_carrinho_itens_produto` (`produto_id`);

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categorias_nome` (`nome`),
  ADD UNIQUE KEY `uq_categorias_slug` (`slug`);

--
-- Índices para tabela `categoria_imagens`
--
ALTER TABLE `categoria_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria_imagens_categoria` (`categoria_id`);

--
-- Índices para tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_clientes_email` (`email`),
  ADD UNIQUE KEY `uq_clientes_google_sub` (`google_sub`),
  ADD UNIQUE KEY `uq_clientes_token_verificacao_email` (`token_verificacao_email`);

--
-- Índices para tabela `dispositivos_notificacao`
--
ALTER TABLE `dispositivos_notificacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_dispositivos_token` (`token_fcm`),
  ADD KEY `idx_dispositivos_cliente` (`cliente_id`),
  ADD KEY `idx_dispositivos_ativo` (`ativo`);

--
-- Índices para tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_enderecos_cliente` (`cliente_id`),
  ADD KEY `idx_enderecos_cep` (`cep`);

--
-- Índices para tabela `movimentacoes_estoque`
--
ALTER TABLE `movimentacoes_estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_movimentacoes_produto` (`produto_id`),
  ADD KEY `idx_movimentacoes_pedido` (`pedido_id`),
  ADD KEY `idx_movimentacoes_tipo` (`tipo`);

--
-- Índices para tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pagamentos_externo` (`pagamento_externo_id`),
  ADD KEY `idx_pagamentos_pedido` (`pedido_id`),
  ADD KEY `idx_pagamentos_status` (`status`);

--
-- Índices para tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pedidos_codigo` (`codigo`),
  ADD KEY `idx_pedidos_cliente` (`cliente_id`),
  ADD KEY `idx_pedidos_status` (`status`),
  ADD KEY `idx_pedidos_criado_em` (`criado_em`);

--
-- Índices para tabela `pedido_enderecos`
--
ALTER TABLE `pedido_enderecos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pedido_endereco` (`pedido_id`);

--
-- Índices para tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_itens_pedido` (`pedido_id`),
  ADD KEY `idx_pedido_itens_produto` (`produto_id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_produtos_slug` (`slug`),
  ADD KEY `idx_produtos_categoria` (`categoria_id`),
  ADD KEY `idx_produtos_nome` (`nome`),
  ADD KEY `idx_produtos_status` (`status`);

--
-- Índices para tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_imagens_produto` (`produto_id`);

--
-- Índices para tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuarios_admin_email` (`email`);

--
-- Índices para tabela `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_webhook_provedor` (`provedor`),
  ADD KEY `idx_webhook_evento` (`evento`),
  ADD KEY `idx_webhook_externo` (`identificador_externo`),
  ADD KEY `idx_webhook_processado` (`processado`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `categoria_imagens`
--
ALTER TABLE `categoria_imagens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `dispositivos_notificacao`
--
ALTER TABLE `dispositivos_notificacao`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `movimentacoes_estoque`
--
ALTER TABLE `movimentacoes_estoque`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_enderecos`
--
ALTER TABLE `pedido_enderecos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `webhook_logs`
--
ALTER TABLE `webhook_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  ADD CONSTRAINT `fk_carrinhos_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD CONSTRAINT `fk_carrinho_itens_carrinhos` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinhos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carrinho_itens_produtos` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `categoria_imagens`
--
ALTER TABLE `categoria_imagens`
  ADD CONSTRAINT `fk_categoria_imagens_categorias` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `dispositivos_notificacao`
--
ALTER TABLE `dispositivos_notificacao`
  ADD CONSTRAINT `fk_dispositivos_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `fk_enderecos_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `movimentacoes_estoque`
--
ALTER TABLE `movimentacoes_estoque`
  ADD CONSTRAINT `fk_movimentacoes_pedidos` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimentacoes_produtos` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `fk_pagamentos_pedidos` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pedido_enderecos`
--
ALTER TABLE `pedido_enderecos`
  ADD CONSTRAINT `fk_pedido_enderecos_pedidos` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `fk_pedido_itens_pedidos` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedido_itens_produtos` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produtos_categorias` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD CONSTRAINT `fk_imagens_produtos` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
