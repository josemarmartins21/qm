-- --------------------------------------------------------
-- Anfitrião:                    127.0.0.1
-- Versão do servidor:           8.0.30 - MySQL Community Server - GPL
-- SO do servidor:               Win64
-- HeidiSQL Versão:              12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- A despejar estrutura da base de dados para qm
DROP DATABASE IF EXISTS `qm`;
CREATE DATABASE IF NOT EXISTS `qm` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `qm`;

-- A despejar estrutura para tabela qm.categoria
DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `categoria_id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.categoria: ~0 rows (aproximadamente)

-- A despejar estrutura para tabela qm.client
DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `client_id` int NOT NULL AUTO_INCREMENT,
  `primeiro_nome` varchar(45) DEFAULT NULL,
  `ultimo_nome` varchar(45) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('ativo','suspenso') DEFAULT 'ativo',
  `municipio` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.client: ~4 rows (aproximadamente)
INSERT INTO `client` (`client_id`, `primeiro_nome`, `ultimo_nome`, `telefone`, `email`, `password`, `status`, `municipio`) VALUES
	(16, 'Celson', 'David', '911223366', 'david@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 'ativo', NULL),
	(17, 'Antonio', 'Celestino', '921586398', 'celestino@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 'ativo', NULL),
	(18, 'Benjamim', 'Bingo', '952364812', 'benjamim@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 'ativo', NULL),
	(19, 'Gilson', 'Miguel', '930710658', 'miguel@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 'ativo', NULL);

-- A despejar estrutura para tabela qm.client_has_plan
DROP TABLE IF EXISTS `client_has_plan`;
CREATE TABLE IF NOT EXISTS `client_has_plan` (
  `client_client_id` int NOT NULL,
  `planos_plan_id` int NOT NULL,
  `created_at` date DEFAULT NULL,
  PRIMARY KEY (`client_client_id`,`planos_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.client_has_plan: ~6 rows (aproximadamente)
INSERT INTO `client_has_plan` (`client_client_id`, `planos_plan_id`, `created_at`) VALUES
	(0, 0, NULL),
	(1, 1, '2026-02-07'),
	(2, 3, '2026-06-19'),
	(3, 2, '2026-06-11'),
	(4, 1, '2026-02-07'),
	(5, 2, '2026-02-07'),
	(16, 3, '2026-06-20');

-- A despejar estrutura para tabela qm.faturas
DROP TABLE IF EXISTS `faturas`;
CREATE TABLE IF NOT EXISTS `faturas` (
  `fatura_id` int NOT NULL AUTO_INCREMENT,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_emissao` date DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `status` enum('pendente','paga','vencida') DEFAULT 'pendente',
  `client_id` int DEFAULT NULL,
  PRIMARY KEY (`fatura_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `faturas_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.faturas: ~9 rows (aproximadamente)
INSERT INTO `faturas` (`fatura_id`, `valor`, `data_emissao`, `data_vencimento`, `status`, `client_id`) VALUES
	(1, 15000.03, NULL, '2026-03-20', 'pendente', NULL),
	(2, 15000.03, NULL, '2026-03-20', 'pendente', NULL),
	(3, 15000.00, NULL, '2026-03-26', 'pendente', NULL),
	(4, 15000.03, NULL, '2026-03-13', 'pendente', NULL),
	(5, 15000.03, NULL, '2026-03-09', 'paga', NULL),
	(6, 15000.00, NULL, '2026-03-09', 'pendente', NULL),
	(7, 15000.00, NULL, '2026-03-19', 'paga', NULL),
	(8, 15000.03, NULL, '2026-07-11', 'pendente', NULL),
	(9, 3.00, NULL, '2026-07-19', 'paga', NULL),
	(10, 3.00, NULL, '2026-07-20', 'pendente', 16);

-- A despejar estrutura para tabela qm.historico_de_assinaturas
DROP TABLE IF EXISTS `historico_de_assinaturas`;
CREATE TABLE IF NOT EXISTS `historico_de_assinaturas` (
  `historico_id` int NOT NULL AUTO_INCREMENT,
  `client_client_id` int NOT NULL,
  `planos_plan_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_expiracao` datetime NOT NULL,
  `status` enum('ativa','expirada','cancelada') DEFAULT 'ativa',
  PRIMARY KEY (`historico_id`),
  KEY `fk_hist_cliente` (`client_client_id`),
  KEY `fk_hist_plano` (`planos_plan_id`),
  CONSTRAINT `fk_hist_cliente` FOREIGN KEY (`client_client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_plano` FOREIGN KEY (`planos_plan_id`) REFERENCES `planos` (`plan_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.historico_de_assinaturas: ~0 rows (aproximadamente)

-- A despejar estrutura para tabela qm.pagamentos
DROP TABLE IF EXISTS `pagamentos`;
CREATE TABLE IF NOT EXISTS `pagamentos` (
  `pagamento_id` int NOT NULL AUTO_INCREMENT,
  `fatura_id` int DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `metodo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pagamento_id`),
  KEY `fatura_id` (`fatura_id`),
  CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`fatura_id`) REFERENCES `faturas` (`fatura_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.pagamentos: ~3 rows (aproximadamente)
INSERT INTO `pagamentos` (`pagamento_id`, `fatura_id`, `valor_pago`, `data_pagamento`, `metodo`) VALUES
	(1, 5, 15000.03, '2026-02-07', 'Dinheiro'),
	(2, 7, 15000.00, '2026-02-21', 'Dinheiro'),
	(3, 9, 3.00, '2026-06-19', 'Dinheiro');

-- A despejar estrutura para tabela qm.planos
DROP TABLE IF EXISTS `planos`;
CREATE TABLE IF NOT EXISTS `planos` (
  `plan_id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `descricao` text,
  `categoria_categoria_id` int DEFAULT NULL,
  PRIMARY KEY (`plan_id`),
  KEY `categoria_categoria_id` (`categoria_categoria_id`),
  CONSTRAINT `planos_ibfk_1` FOREIGN KEY (`categoria_categoria_id`) REFERENCES `categoria` (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.planos: ~2 rows (aproximadamente)
INSERT INTO `planos` (`plan_id`, `nome`, `preco`, `descricao`, `categoria_categoria_id`) VALUES
	(2, 'Internet Silver', 15000.03, '', NULL),
	(3, 'Internet Basic', 3.00, 'Desfrute da nossa internet em todo lugar', NULL);

-- A despejar estrutura para tabela qm.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `primeiro_nome` varchar(45) DEFAULT NULL,
  `ultimo_nome` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_adm` tinyint DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.users: ~6 rows (aproximadamente)
INSERT INTO `users` (`user_id`, `primeiro_nome`, `ultimo_nome`, `email`, `password`, `is_adm`) VALUES
	(1, 'Pedro', 'Jorge', 'pedro@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 1),
	(2, 'Adão', 'Neto', 'adao@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 1),
	(3, 'Jolirio', 'Ngoio', 'jolirio@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 1),
	(4, 'Loureço', 'Domingos', 'lourenco@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 1),
	(5, 'Josimar', 'Martins', 'josemar@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 1),
	(6, 'Cristiano', 'Madaleno', 'cristiano@email.com', '$2y$12$j/slZvlKj90r4ayA6sckT.X6uHMLJfAtyb1khqBxp/nyzIi8lJOEe', 0);

-- A despejar estrutura para tabela qm.user_sessions
DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela qm.user_sessions: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
