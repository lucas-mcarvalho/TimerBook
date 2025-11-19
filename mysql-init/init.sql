-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 12/11/2025 às 12:36
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `Users`
--

-- --------------------------------------------------------
USE Users;
--
-- Estrutura para tabela `Admin`
--

CREATE TABLE `Admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `Admin`
--

INSERT INTO `Admin` (`id`, `nome`, `email`, `senha`, `profile_photo`, `username`, `reset_token`, `reset_token_expire`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin123', NULL, 'admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `Books`
--

CREATE TABLE `Books` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `ano_publicacao` int(11) DEFAULT NULL,
  `caminho_arquivo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `capa_livro` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `Books`
--

INSERT INTO `Books` (`id`, `titulo`, `autor`, `ano_publicacao`, `caminho_arquivo`, `created_at`, `user_id`, `capa_livro`) VALUES
(131, 'O fausto', 'Goethe', 2010, 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/690b233743601-faustogoethe.pdf', '2025-11-05 10:13:13', 71, 'https://imgusrs.s3.sa-east-1.amazonaws.com/Capas_dos_Livros/690b23398fb6a-71J6Mdqu7NL._AC_UF1000,1000_QL80_.jpg'),
(133, 'Dom CasMurro', '2020', 2222, 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/690b25471b256-Dom Casmurro - Dom_Casmurro-Machado_de_Assis.pdf', '2025-11-05 10:21:59', 71, 'https://imgusrs.s3.sa-east-1.amazonaws.com/Capas_dos_Livros/690b2547beca2-61x1ZHomWUL.jpg'),
(134, 'Memorias Postumas', 'Candido', 2022, 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/690b318105550-memoriasBras.pdf', '2025-11-05 11:14:09', 71, 'https://imgusrs.s3.sa-east-1.amazonaws.com/Capas_dos_Livros/690b3181a2fa5-memorias-postumas-bras-cubas-machado-assis_large.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Reading`
--

CREATE TABLE `Reading` (
  `id` int(11) NOT NULL,
  `pk_usuario` int(11) NOT NULL,
  `livro` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tempo_gasto` int(11) DEFAULT NULL,
  `paginas_lidas` int(11) DEFAULT NULL,
  `data_inicio` datetime DEFAULT NULL,
  `data_fim` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `Reading`
--

INSERT INTO `Reading` (`id`, `pk_usuario`, `livro`, `status`, `tempo_gasto`, `paginas_lidas`, `data_inicio`, `data_fim`) VALUES
(96, 71, 131, 'em andamento', 62, 100, '2025-11-05 07:13:18', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `SessaoLeitura`
--

CREATE TABLE `SessaoLeitura` (
  `id` int(11) NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime DEFAULT NULL,
  `tempo_sessao` int(11) DEFAULT NULL,
  `pk_leitura` int(11) NOT NULL,
  `paginas_lidas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `SessaoLeitura`
--

INSERT INTO `SessaoLeitura` (`id`, `data_inicio`, `data_fim`, `tempo_sessao`, `pk_leitura`, `paginas_lidas`) VALUES
(258, '2025-11-05 07:13:18', '2025-11-05 07:13:27', 9, 96, 5),
(260, '2025-11-05 08:26:49', '2025-11-05 08:27:04', 15, 96, 4),
(261, '2025-11-05 08:27:09', '2025-11-05 08:27:47', 38, 96, 91);

-- --------------------------------------------------------

--
-- Estrutura para tabela `User`
--

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `User`
--

INSERT INTO `User` (`id`, `nome`, `email`, `senha`, `profile_photo`, `username`, `reset_token`, `reset_token_expire`) VALUES
(29, 'Lucasf', 'lucasggtyy358@gmail.com', '$2y$10$VTT/8Je2NcmVgKx6hl2vi.a.2akV3uqnwxvCJGoN7LnDmaQ7duM2G', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68eacd4757026.gif', 'lupp22', NULL, NULL),
(30, 'Tiago123', 'barbosa.castro@mail.uft.edu.br', '$2y$10$MIVKU2mDySdYTLvV/M1./.1Shg4coxX5d.5EQrMnvAZlfaWIRl1wq', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68e59207d1d20.jpg', 'barbosa123', NULL, NULL),
(31, 'Matheus', 'matheus.spontes0406@gmail.com', '$2y$10$uYU4puqmxd49byKWPWxdi.GEI15YjX9EWTheEm1DtMGbjnAQSx5e.', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68eade38148cd.jpg', 'math1234', NULL, NULL),
(37, 'vitorkawan', 'vitorkawan1@gmail.com', '$2y$10$q.rSv/Fv571E/.poClUD4.66EhFFzGjwNBvgw93v5FLhzOXo/whA.', NULL, 'kawanzinho', NULL, NULL),
(55, 'Teste777', 'testejoao777@gmail.com', '$2y$10$PdF4l1PN4GOfTvMYQsOHu.nrXMVxuSWpzf2.zeSMStGDcB9VPg45i', NULL, 'testejoao777', NULL, NULL),
(56, 'testejoao22', 'teste24@gmail.com', '$2y$10$llQ7LCFQbimmHTZ9YQwH8OA1PgUAGPjChA4Dx9zUTBmwved8ndoUC', NULL, 'testejo', NULL, NULL),
(57, 'teste123', 'teste123@gmail.com', '$2y$10$mg6Is/43IBP60FhLRulOM.jLJqpb06.EOC4Kk73j74nwAF4R10Iw2', NULL, 'teste1234', NULL, NULL),
(68, 'Teste_123', 'teste71040@gmail.com', '$2y$10$evfbc1e7j9R5pfWc0ifMeOTi/0rYFQCjItQ9slLucYBiRG2BeGAu6', NULL, 'teste', NULL, NULL),
(69, 'João da Silva', 'joaodasilva@gmail.com', '$2y$10$chOSNAcIc36Ls8KIPrKSpehiH/rq0OU5cogqUtB2uPSno2E01Y2tC', NULL, 'joaodasilv', NULL, NULL),
(70, 'Teste', 'testejoao22@gmail.com', '$2y$10$5wou3bMT/nC0HA4o3c2H9.OrkWx3BrVOkGIaU4z1hxEZWnXz.fTAW', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68def864e5938.png', 'testej22', NULL, NULL),
(71, 'teste', 'usertest22@gmail.com', '$2y$10$1bCSlMuJKGVsJY4RkWohvOQp2upfXQw59euXn/DSYZ0xxSMQQxShK', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68dfff3d0ba81.jpg', 'usertest', NULL, NULL),
(73, 'vitorkawan', 'vk@gmail.com', '$2y$10$LLjqY4NIPGYNPzxrLnAqV.iDDCauSDzicMwZHtGV2wi7bLQqqHQNW', NULL, 'kwn', NULL, NULL),
(79, 'TESTEnovoUsuario', 'ahbyuyb@gmai.com', '$2y$10$1ac46Ex7ByTqOD/ag11mwOppFJCbLlftS6qS12xLGoJuoEL3b/qfu', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68e593228d74a.jpg', 'TesteNovoUsuario', NULL, NULL),
(84, 'Tiago Barbosa de Castro', 'tiagobc06@gmail.com', '$2y$10$P7vaJsLb/FHctznlgXcltOo3AQFYMWsykQNHm42oTozhvjpe5hwja', 'https://lh3.googleusercontent.com/a/ACg8ocKuxbDLjdhC2ni2rRRWmLqujQkLG7lgXqdZXXMLw52gS5m4Yl8=s96-c', 'tiagobc06', NULL, NULL),
(122, 'kayke', 'kaykezago@gmail.com', '$2y$10$KVL6AfDVEI8ffmotwSSw7OmS9nofwWaLplyy8NemhY5KKxwkKKmKi', NULL, 'kayke', NULL, NULL),
(123, 'Lucas Monteiro', 'lucasgamescarva@gmail.com', '$2y$10$Qk2RrZCTxAzOtUbCpMxrzerZdgMPOpwwEj5lZcV4OHVnU6SemMRo.', 'https://lh3.googleusercontent.com/a/ACg8ocIHWhwwJZtRZffMSVxHRy3kiaMolFpfRfbiUgCnLg_N49XH6FT7=s96-c', 'lucasgamescarva', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `Admin`
--
ALTER TABLE `Admin`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `Books`
--
ALTER TABLE `Books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_book_user` (`user_id`);

--
-- Índices de tabela `Reading`
--
ALTER TABLE `Reading`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pk_usuario` (`pk_usuario`),
  ADD KEY `livro` (`livro`);

--
-- Índices de tabela `SessaoLeitura`
--
ALTER TABLE `SessaoLeitura`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sessaoLeitura_leitura` (`pk_leitura`);

--
-- Índices de tabela `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Admin`
--
ALTER TABLE `Admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `Books`
--
ALTER TABLE `Books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT de tabela `Reading`
--
ALTER TABLE `Reading`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de tabela `SessaoLeitura`
--
ALTER TABLE `SessaoLeitura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT de tabela `User`
--
ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Books`
--
ALTER TABLE `Books`
  ADD CONSTRAINT `fk_book_user` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `SessaoLeitura`
--
ALTER TABLE `SessaoLeitura`
  ADD CONSTRAINT `fk_sessaoLeitura_leitura` FOREIGN KEY (`pk_leitura`) REFERENCES `Reading` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
