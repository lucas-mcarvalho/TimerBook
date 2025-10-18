-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 17/10/2025 às 22:07
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

--
-- Estrutura para tabela `Admin`
--

CREATE TABLE `Admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `email` varchar(30) NOT NULL,
  `senha` varchar(255) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
(96, 'teste2', 'teste23', 1948, 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/68e3c59d0251f-SprintES.PDF', '2025-10-06 16:35:25', 74, 'https://imgusrs.s3.sa-east-1.amazonaws.com/Capas_dos_Livros/68e3c59d1f8a2-OnePunchMan.png'),
(97, 'Memórias Póstumas de Brás Cubas', 'Machado de Assis', 1881, 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/68e3c7fcd7e5e-Memórias Póstumas de Brás Cubas - Machado de Assis.pdf', '2025-10-06 16:45:33', 31, 'https://imgusrs.s3.sa-east-1.amazonaws.com/Capas_dos_Livros/68e3c7fd04729-91GAAzBixYL._UF894,1000_QL80_.jpg'),
(116, 'Noite na Taverna', 'Álvares de Azevedo', 1855, 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/68e66dff620db-Noite na Taverna.pdf', '2025-10-08 16:58:17', 31, 'https://imgusrs.s3.sa-east-1.amazonaws.com/Capas_dos_Livros/68e66e0088ba4-1911281645-noite-na-taverna.jpg'),
(117, 'Dom Casmurro', 'Machado de Assis', 1899, 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/68e66f610202f-Dom_Casmurro-Machado_de_Assis.pdf', '2025-10-08 17:04:11', 31, 'https://imgusrs.s3.sa-east-1.amazonaws.com/Capas_dos_Livros/68e66f6294b91-61x1ZHomWUL.jpg'),
(120, 'O fausto', 'Goethe', 201, 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/68e8331e56c67-faustogoethe.pdf', '2025-10-10 01:11:43', 71, 'https://imgusrs.s3.sa-east-1.amazonaws.com/Capas_dos_Livros/68e8331f1a81b-Fausto.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `User`
--

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `email` varchar(30) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `User`
--

INSERT INTO `User` (`id`, `nome`, `email`, `senha`, `profile_photo`, `username`, `reset_token`, `reset_token_expire`) VALUES
(29, 'Lucasff', 'lucasggtyy358@gmail.com', '$2y$10$VTT/8Je2NcmVgKx6hl2vi.a.2akV3uqnwxvCJGoN7LnDmaQ7duM2G', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68e8268351f99.gif', 'lupp22', NULL, NULL),
(30, 'Tiago Barbosa', 'barbosa.castro@mail.uft.edu.br', '$2y$10$EYHXERZyVyeZoIuWPK1oVembA2o9j7UTy5op1b5vW675GZx/ndyce', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68e59207d1d20.jpg', 'barbosa123', NULL, NULL),
(31, 'Matheus', 'matheus.spontes0406@gmail.com', '$2y$10$uYU4puqmxd49byKWPWxdi.GEI15YjX9EWTheEm1DtMGbjnAQSx5e.', '68df26e03b70d.jpg', 'math1234', NULL, NULL),
(37, 'vitorkawan', 'vitorkawan1@gmail.com', '$2y$10$q.rSv/Fv571E/.poClUD4.66EhFFzGjwNBvgw93v5FLhzOXo/whA.', NULL, 'kawanzinho', NULL, NULL),
(38, 'Kayke Zago Pinheiro ', 'kayke@gmail.com', '$2y$10$E90zGhtANgk8spcNr.IMsOqrvdEgdR.3sAnx0OAKsTPdvTl6tnFm6', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68e7e0c05fe3f.jpg', 'Kayke002', NULL, NULL),
(55, 'Teste777', 'testejoao777@gmail.com', '$2y$10$PdF4l1PN4GOfTvMYQsOHu.nrXMVxuSWpzf2.zeSMStGDcB9VPg45i', NULL, 'testejoao777', NULL, NULL),
(56, 'testejoao22', 'teste24@gmail.com', '$2y$10$llQ7LCFQbimmHTZ9YQwH8OA1PgUAGPjChA4Dx9zUTBmwved8ndoUC', NULL, 'testejo', NULL, NULL),
(57, 'teste123', 'teste123@gmail.com', '$2y$10$mg6Is/43IBP60FhLRulOM.jLJqpb06.EOC4Kk73j74nwAF4R10Iw2', NULL, 'teste1234', NULL, NULL),
(58, 'Teste', 'teste22@gmail.com', '$2y$10$ybSaF0g3khP9Tr8HS3.Ek.Uv17uYMLS8af/UZJqZ1igFqi/sAz/6C', '/uploads/68dd859952b8f.png', 'teste12345', NULL, NULL),
(68, 'Teste_123', 'teste71040@gmail.com', '$2y$10$evfbc1e7j9R5pfWc0ifMeOTi/0rYFQCjItQ9slLucYBiRG2BeGAu6', NULL, 'teste', NULL, NULL),
(69, 'João da Silva', 'joaodasilva@gmail.com', '$2y$10$chOSNAcIc36Ls8KIPrKSpehiH/rq0OU5cogqUtB2uPSno2E01Y2tC', NULL, 'joaodasilv', NULL, NULL),
(70, 'Teste', 'testejoao22@gmail.com', '$2y$10$5wou3bMT/nC0HA4o3c2H9.OrkWx3BrVOkGIaU4z1hxEZWnXz.fTAW', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68def864e5938.png', 'testej22', NULL, NULL),
(71, 'teste', 'usertest22@gmail.com', '$2y$10$1bCSlMuJKGVsJY4RkWohvOQp2upfXQw59euXn/DSYZ0xxSMQQxShK', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68dfff3d0ba81.jpg', 'usertest', NULL, NULL),
(73, 'vitorkawan', 'vk@gmail.com', '$2y$10$LLjqY4NIPGYNPzxrLnAqV.iDDCauSDzicMwZHtGV2wi7bLQqqHQNW', NULL, 'kwn', NULL, NULL),
(74, 'fish ball cat ', 'catboll@gmail.com', '$2y$10$AEm2rZ39DTQ38THrxEiAE.yLMAm9gAtBbtPKlakMdiQFlG0oMG7w2', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68e1b458c74ad.gif', 'cat', NULL, NULL),
(79, 'TESTEnovoUsuario', 'ahbyuyb@gmai.com', '$2y$10$1ac46Ex7ByTqOD/ag11mwOppFJCbLlftS6qS12xLGoJuoEL3b/qfu', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68e593228d74a.jpg', 'TesteNovoUsuario', NULL, NULL),
(84, 'Tiago Barbosa de Castro', 'tiagobc06@gmail.com', '$2y$10$P7vaJsLb/FHctznlgXcltOo3AQFYMWsykQNHm42oTozhvjpe5hwja', 'https://lh3.googleusercontent.com/a/ACg8ocKuxbDLjdhC2ni2rRRWmLqujQkLG7lgXqdZXXMLw52gS5m4Yl8=s96-c', 'tiagobc06', NULL, NULL),
(85, 'Matheus Silva Pontes', 'matheus.pontes@mail.uft.edu.br', '$2y$10$.80l4BOeo5NUxH/3IclFSuvqUvPio1boR6wSHKenzn/6SvLZaF5be', 'https://imgusrs.s3.sa-east-1.amazonaws.com/profile_photos/68e80435a889f.jpg', 'matheus.pontes', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT de tabela `User`
--
ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Books`
--
ALTER TABLE `Books`
  ADD CONSTRAINT `fk_book_user` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
