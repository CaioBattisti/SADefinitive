-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 14/05/2025 às 14:04
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sa_definitive`
--
CREATE DATABASE IF NOT EXISTS `sa_definitive` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sa_definitive`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil`

DROP TABLE IF EXISTS `perfil`;
CREATE TABLE IF NOT EXISTS `perfil` (
  `id_perfil` int NOT NULL AUTO_INCREMENT,
  `nome_perfil` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_perfil`),
  UNIQUE KEY `nome_perfil` (`nome_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfil`
--
INSERT INTO `perfil` (`id_perfil`, `nome_perfil`) VALUES
(1, 'Adm: Nível Alto de Acesso!'),
(2, 'Secretária: Nível médio de Acesso'),
(3, 'Funcionário: Nível de Baixo Acesso'),
(4, 'Fornecedor: Nível de Muito Baixo Acesso');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedor`
--

DROP TABLE IF EXISTS `fornecedor`;
CREATE TABLE IF NOT EXISTS `fornecedor` (
  `id_fornecedor` int NOT NULL AUTO_INCREMENT,
  `nome_fornecedor` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_empresa` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permissao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_fornecedor`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_fornecedor_permissao` (`permissao`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `fornecedor`
--
INSERT INTO `fornecedor` (`id_fornecedor`, `nome_fornecedor`, `endereco`, `telefone`, `email`, `nome_empresa`, `permissao`) VALUES
(1, 'José Silva', 'Av. Paulista, 1000', '11912345678', 'contato@techsupplies.com', 'Tech Supplies', 'Fornecedor: Nível de Muito Baixo Acesso(a)'),
(2, 'Marcos Souza', 'Rua dos Gamers, 200', '21912345678', 'contato@gamerstore.com', 'Gamer Store', 'Fornecedor: Nível de Muito Baixo Acesso(a)'),
(3, 'Fernanda Lima', 'Av. Brasil, 300', '31912345678', 'contato@eletronicosbr.com', 'Eletrônicos BR', 'Fornecedor: Nível de Muito Baixo Acesso(a)'),
(4, 'Carlos Mendes', 'Rua da Tecnologia, 400', '41912345678', 'contato@infotech.com', 'InfoTech', 'Fornecedor: Nível de Muito Baixo Acesso(a)'),
(5, 'Dick Wolf', 'Rua Chicago 214', '(78)8521-1254', 'fire@fire', 'Bombeiros Fire Ltda', 'Fornecedor: Nível de Muito Baixo Acesso(a)');

--
-- ATUALIZAÇÃO: Comando para corrigir o valor incorreto na tabela `fornecedor`.
--
UPDATE `fornecedor`
SET `permissao` = 'Fornecedor: Nível de Muito Baixo Acesso'
WHERE `permissao` = 'Fornecedor: Nível de Muito Baixo Acesso(a)';


-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario`
--

DROP TABLE IF EXISTS `funcionario`;
CREATE TABLE IF NOT EXISTS `funcionario` (
  `id_funcionario` int NOT NULL AUTO_INCREMENT,
  `nome_funcionario` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permissao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_funcionario`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_funcionario_permissao` (`permissao`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `funcionario`
--
INSERT INTO `funcionario` (`id_funcionario`, `nome_funcionario`, `endereco`, `telefone`, `email`, `permissao`) VALUES
(1, 'João Silva', 'Rua X, 500', '11955555555', 'joao@email.com', 'Adm: Nível Alto de Acesso!'),
(2, 'Mariana Oliveira', 'Rua Y, 600', '21966666666', 'mariana@email.com', 'Secretária: Nível médio de Acesso(a)'),
(3, 'Roberto Santos', 'Rua Z, 700', '31977777777', 'roberto@email.com', 'Funcionário: Nível de Baixo Acesso'),
(4, 'Camila Ferreira', 'Rua W, 800', '41988888888', 'camila@email.com', 'Funcionário: Nível de Baixo Acesso'),
(5, 'Jesse Pinkman', 'Rua Novo Mexico, 171', '2132145874', 'jesse@jesse.com', 'Adm: Nível Alto de Acesso!');

--
-- ATUALIZAÇÃO: Comando para corrigir o valor incorreto na tabela `funcionario`.
--
UPDATE `funcionario`
SET `permissao` = 'Secretária: Nível médio de Acesso'
WHERE `permissao` = 'Secretária: Nível médio de Acesso(a)';


-- --------------------------------------------------------

--
-- Estrutura para tabela `remedio`
--

DROP TABLE IF EXISTS `remedio`;
CREATE TABLE IF NOT EXISTS `remedio` (
  `id_remedio` int NOT NULL AUTO_INCREMENT,
  `nome_remedio` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validade` date NOT NULL,
  `qnt_estoque` int NOT NULL,
  `preco_unit` decimal(10, 2) NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_fornecedor` int NOT NULL,
  PRIMARY KEY (`id_remedio`),
  KEY `fk_remedio_fornecedor` (`id_fornecedor`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `remedio`
--
INSERT INTO `remedio` (`id_remedio`, `nome_remedio`, `descricao`, `validade`, `qnt_estoque`, `preco_unit`, `tipo`, `id_fornecedor`) VALUES
(1, 'Dipirona', 'Analgésico e antitérmico para dores e febre', '2027-01-20', 150, 8.50, 'Comprimido', 1),
(2, 'Amoxicilina', 'Antibiótico de amplo espectro', '2026-11-15', 75, 25.00, 'Comprimido', 2),
(3, 'Neosaldina', 'Analgésico e relaxante muscular', '2028-03-10', 200, 12.75, 'Gota', 1),
(4, 'Cloridrato de Propranolol', 'Betabloqueador para tratamento de hipertensão', '2027-05-22', 90, 35.50, 'Comprimido', 3),
(5, 'Loratadina', 'Anti-histamínico para alergias', '2026-08-30', 120, 15.20, 'Creme', 2);

--
-- Estrutura para tabela `usuario`
--
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_perfil` int DEFAULT NULL,
  `senha_temporaria` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_perfil` (`id_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuario`
--
INSERT INTO `usuario` (`id_usuario`, `nome`, `senha`, `email`, `id_perfil`, `senha_temporaria`) VALUES
(1, 'Administrador', '$2y$10$rIJhd7oXSRM1XbAdQCEsA.PF3n/rxNtIAUqCkcFybzE5J.mLBsq.q', 'admin@admin', 1, 0),
(2, 'Sergio Luiz da Silveira', '$2y$10$AKaq2b1ZyNzZs5u6ueiJq.t5xj02aj0aroz4IjHDPhdAsrhZL8MO.', 'sergio@sergio', 1, 0),
(6, 'Maria Souza', '$2y$10$RRDyLe.N/SHniQ03fG3mnuRN84K/D4wVS3BkftU7nUUFEqyOhwFDu', 'maria@empresa.com', 2, 0),
(7, 'Carlos Mendes', '$2y$10$RRDyLe.N/SHniQ03fG3mnuRN84K/D4wVS3BkftU7nUUFEqyOhwFDu', 'carlos@empresa.com', 3, 0),
(8, 'Ana Pereira', '$2y$10$xaWdXzOzYETic/DhbeHV2OZCAgBaOJzqo9j38DeAEKV2.grcV.L3u', 'ana@empresa.com', 4, 0),
(9, 'Joao Vitor', '$2y$10$2nzDym9SuKZba3OcGeUWKu7RB3CRhpVb1v.LXb9kYxBWVh1/dAG22', 'vitor@vitor', 1, 0),
(12, 'Grace Van Pelt', '$2y$10$g5h1LI20ufnY/p6062h5r.ezKU7eFlhhwRCSkuKTJiYUYulPIQjxq', 'grace@grace', 4, 0),
(13, 'Xavier', '$2y$10$ErMocH1x.avm4asmRnKzeOUF30fi4ZO33C/9H6D2opvlFZ6zEorR.', 'xavier@xavier', 1, 0);

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `fornecedor`
--
ALTER TABLE `fornecedor`
  ADD CONSTRAINT `fk_fornecedor_permissao` FOREIGN KEY (`permissao`) REFERENCES `perfil` (`nome_perfil`) ON DELETE SET NULL;

--
-- Restrições para tabelas `funcionario`
--
ALTER TABLE `funcionario`
  ADD CONSTRAINT `fk_funcionario_permissao` FOREIGN KEY (`permissao`) REFERENCES `perfil` (`nome_perfil`) ON DELETE SET NULL;

--
-- Restrições para tabelas `remedio`
--
ALTER TABLE `remedio`
  ADD CONSTRAINT `fk_remedio_fornecedor` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedor` (`id_fornecedor`);

--
-- Restrições para tabelas `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_perfil`) REFERENCES `perfil` (`id_perfil`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;