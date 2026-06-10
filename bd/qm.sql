-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 26-Abr-2026 às 00:31
-- Versão do servidor: 10.1.16-MariaDB
-- PHP Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qmbeta`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria`
--

CREATE TABLE `categoria` (
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `client`
--

CREATE TABLE `client` (
  `client_id` int(11) NOT NULL,
  `primeiro_nome` varchar(45) DEFAULT NULL,
  `ultimo_nome` varchar(45) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `municipio` varchar(100) DEFAULT NULL,
  `status` enum('ativo','suspenso') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `client`
--

INSERT INTO `client` (`client_id`, `primeiro_nome`, `ultimo_nome`, `telefone`, `email`, `password`, `municipio`, `status`) VALUES
(1, 'Jorge', 'skua', '924555555', 'jorge@gmail.com', NULL, NULL, 'ativo'),
(2, 'Jorge', 'Paiva', '924555555', 'jorge@gmail.com', NULL, NULL, 'ativo'),
(3, 'LourenÃ§o', 'Jorge', '946192765', 'lo@gmail.com', NULL, NULL, 'ativo'),
(4, 'LourenÃ§o', 'JosÃ©', '935555554', 'lou@gmail.com', '$2y$10$DPFGz1jR/SRCLaA4stGPre1mzgbTWNoXtk4zUr99SUdhbAXYAVwv6', NULL, 'ativo'),
(5, 'AdÃ£o', 'Neto', '946192761', 'adao@gmail.com', '$2y$10$eOJknlbbfRwqKh9P6eTWCedPAlhltCGqkHGv/SpYjxkRdfDWVRhiy', NULL, 'ativo');

-- --------------------------------------------------------

--
-- Estrutura da tabela `client_has_plan`
--

CREATE TABLE `client_has_plan` (
  `client_client_id` int(11) NOT NULL,
  `planos_plan_id` int(11) NOT NULL,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `client_has_plan`
--

INSERT INTO `client_has_plan` (`client_client_id`, `planos_plan_id`, `created_at`) VALUES
(0, 0, NULL),
(1, 1, '2026-02-07'),
(4, 1, '2026-02-07'),
(5, 2, '2026-02-07');

-- --------------------------------------------------------

--
-- Estrutura da tabela `faturas`
--

CREATE TABLE `faturas` (
  `fatura_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_emissao` date DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `status` enum('pendente','paga','vencida') DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `faturas`
--

INSERT INTO `faturas` (`fatura_id`, `client_id`, `valor`, `data_emissao`, `data_vencimento`, `status`) VALUES
(1, NULL, '15000.03', NULL, '2026-03-20', 'pendente'),
(2, NULL, '15000.03', NULL, '2026-03-20', 'pendente'),
(3, NULL, '15000.00', NULL, '2026-03-26', 'pendente'),
(4, NULL, '15000.03', NULL, '2026-03-13', 'pendente'),
(5, 5, '15000.03', NULL, '2026-03-09', 'paga'),
(6, 1, '15000.00', NULL, '2026-03-09', 'pendente'),
(7, 4, '15000.00', NULL, '2026-03-19', 'paga');

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_de_assinaturas`
--

CREATE TABLE `historico_de_assinaturas` (
  `historico_id` int(11) NOT NULL,
  `client_client_id` int(11) NOT NULL,
  `planos_plan_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_expiracao` datetime NOT NULL,
  `status` enum('ativa','expirada','cancelada') DEFAULT 'ativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `pagamento_id` int(11) NOT NULL,
  `fatura_id` int(11) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `metodo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `pagamentos`
--

INSERT INTO `pagamentos` (`pagamento_id`, `fatura_id`, `valor_pago`, `data_pagamento`, `metodo`) VALUES
(1, 5, '15000.03', '2026-02-07', 'Dinheiro'),
(2, 7, '15000.00', '2026-02-21', 'Dinheiro');

-- --------------------------------------------------------

--
-- Estrutura da tabela `planos`
--

CREATE TABLE `planos` (
  `plan_id` int(11) NOT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `descricao` text,
  `categoria_categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `planos`
--

INSERT INTO `planos` (`plan_id`, `nome`, `preco`, `descricao`, `categoria_categoria_id`) VALUES
(1, 'Skua', '15000.00', 'dvbgb', NULL),
(2, 'Paulo Costa', '15000.03', '', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `primeiro_nome` varchar(45) DEFAULT NULL,
  `ultimo_nome` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_adm` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`user_id`, `primeiro_nome`, `ultimo_nome`, `email`, `password`, `is_adm`) VALUES
(1, 'Alberto', 'skua', 'skua@gmail.com', '$2y$10$RhzquAV4E9qz1IAqLNHgW.Yd9aKNnfn8pLsZtaLdeujaNDbcego3e', 1),
(2, 'Ana', 'Paula', 'paula@gmail.com', '$2y$10$.byow3FNRDbR0XwbKmODP.Ppa2Kyfol4ye27R8Up0z5H1ehNr7rcy', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `user_sessions`
--

INSERT INTO `user_sessions` (`session_id`, `user_id`, `last_activity`) VALUES
('1430vom5ur5lj9jtv3arqr2820', 2, '2026-02-08 20:59:14'),
('4t9tmqm3m3je3f4tae0rfkoo13', 1, '2026-04-07 21:37:07'),
('5v7hd7d5b9nh3acgrf39rscr52', 1, '2026-02-02 21:36:10'),
('7nftej1pcg93rkf1l81jtiiqs4', 1, '2026-01-27 21:47:33'),
('dei9klocvbpbo8ib2jdvl6smp4', 1, '2026-02-21 10:46:57'),
('dhmrrpndom2b6jhas8vqa9l3c2', 2, '2026-02-08 21:14:15'),
('dquj1uosloo29h8tgl3nqi9m10', 1, '2026-01-21 20:01:53'),
('dtu891st64ni4av0dbkg04mbr3', 1, '2026-01-20 06:17:30'),
('ibj1h977c95ap3bc4m1nspfek4', 1, '2026-01-19 21:40:48'),
('r2tj1a1qg4m07dkmuqec7bd782', 2, '2026-02-08 21:14:57'),
('r7vvlea70o9eoghprsi3g2tgt1', 1, '2026-02-07 19:54:44'),
('t5t3c8jduogvguvasb12870ft0', 2, '2026-04-25 21:49:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`categoria_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `client_has_plan`
--
ALTER TABLE `client_has_plan`
  ADD PRIMARY KEY (`client_client_id`,`planos_plan_id`);

--
-- Indexes for table `faturas`
--
ALTER TABLE `faturas`
  ADD PRIMARY KEY (`fatura_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `historico_de_assinaturas`
--
ALTER TABLE `historico_de_assinaturas`
  ADD PRIMARY KEY (`historico_id`),
  ADD KEY `fk_hist_cliente` (`client_client_id`),
  ADD KEY `fk_hist_plano` (`planos_plan_id`);

--
-- Indexes for table `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`pagamento_id`),
  ADD KEY `fatura_id` (`fatura_id`);

--
-- Indexes for table `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `categoria_categoria_id` (`categoria_categoria_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `categoria_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `faturas`
--
ALTER TABLE `faturas`
  MODIFY `fatura_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `historico_de_assinaturas`
--
ALTER TABLE `historico_de_assinaturas`
  MODIFY `historico_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `pagamento_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `planos`
--
ALTER TABLE `planos`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `faturas`
--
ALTER TABLE `faturas`
  ADD CONSTRAINT `faturas_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`);

--
-- Limitadores para a tabela `historico_de_assinaturas`
--
ALTER TABLE `historico_de_assinaturas`
  ADD CONSTRAINT `fk_hist_cliente` FOREIGN KEY (`client_client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hist_plano` FOREIGN KEY (`planos_plan_id`) REFERENCES `planos` (`plan_id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`fatura_id`) REFERENCES `faturas` (`fatura_id`);

--
-- Limitadores para a tabela `planos`
--
ALTER TABLE `planos`
  ADD CONSTRAINT `planos_ibfk_1` FOREIGN KEY (`categoria_categoria_id`) REFERENCES `categoria` (`categoria_id`);

--
-- Limitadores para a tabela `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
