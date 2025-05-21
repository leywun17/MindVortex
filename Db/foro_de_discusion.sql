-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-05-2025 a las 22:51:42
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `foro_de_discusion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comments`
--

INSERT INTO `comments` (`id`, `forum_id`, `parent_id`, `user_id`, `content`, `created_at`, `updated_at`) VALUES
(109, 91, NULL, 3, 'sssssssssss', '2025-05-18 19:28:06', '2025-05-18 19:28:34'),
(114, 91, 109, 1, 'plapalap', '2025-05-18 19:58:30', '2025-05-18 19:58:30'),
(117, 88, 110, 1, 'pepepepep', '2025-05-20 21:17:10', '2025-05-20 21:17:10'),
(118, 92, NULL, 4, 'oaloal', '2025-05-21 00:03:05', '2025-05-21 00:03:05'),
(119, 92, 118, 4, 'fuck', '2025-05-21 00:13:47', '2025-05-21 00:13:47'),
(120, 92, 118, 5, 'edadas', '2025-05-21 15:42:09', '2025-05-21 15:42:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forums`
--

CREATE TABLE `forums` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `userId` int(11) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `forums`
--

INSERT INTO `forums` (`id`, `title`, `description`, `image`, `userId`, `createdAt`) VALUES
(88, 'sa', 'assssssssaaaaaaaaaaaaaaaaaaaaaaa', '5d935e5b6a9b51e73853175b07e19107.jpg', 1, '2025-05-16 00:00:00'),
(91, 'ppppp', 'pepeepepepepeepepepepepepep', '6d42fe43c1b272440d29802a9a11eff1.png', 3, '2025-05-16 00:00:00'),
(92, 'plapalapala', '18jsismsadmlakskjdand', 'e328ac6f2c56dc4e80b83a4fa1554b06.jpg', 4, '2025-05-21 00:00:00'),
(93, 'plpla', 'asdfghjkl´poiuytrewqzxcvbnm', '3943cb17ed0318aeea1c5ae43e59e8b7.jpg', 5, '2025-05-21 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forum_favorite`
--

CREATE TABLE `forum_favorite` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_foro` int(11) NOT NULL,
  `fecha_agregado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `forum_favorite`
--

INSERT INTO `forum_favorite` (`id`, `id_usuario`, `id_foro`, `fecha_agregado`) VALUES
(27, 1, 88, '2025-05-15 23:43:02'),
(28, 1, 91, '2025-05-18 00:02:47'),
(29, 3, 88, '2025-05-20 21:22:29'),
(30, 5, 92, '2025-05-21 15:41:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `is_read`, `created_at`) VALUES
(8, 3, 'comment', 'Han comentado en tu foro.', 1, '2025-05-18 04:29:01'),
(9, 1, 'comment', 'Han comentado en tu foro.', 1, '2025-05-18 04:49:10'),
(10, 1, 'comment', 'Han comentado en tu foro.', 1, '2025-05-18 04:49:17'),
(11, 1, 'comment', 'Han comentado en tu foro.', 1, '2025-05-18 23:08:47'),
(12, 1, 'comment', 'Han comentado en tu foro.', 1, '2025-05-18 23:08:53'),
(13, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:34:50'),
(14, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:34:50'),
(15, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:34:50'),
(16, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:49:55'),
(17, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:50:01'),
(18, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:50:18'),
(19, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:51:37'),
(20, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:52:05'),
(21, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:55:04'),
(22, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-18 23:56:19'),
(23, 3, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-19 00:19:04'),
(24, 3, 'reply', 'Han respondido a tu comentario.', 1, '2025-05-19 00:19:22'),
(25, 1, 'comment', 'Han comentado en tu foro.', 1, '2025-05-19 00:40:39'),
(26, 3, 'comment', 'Han comentado en tu foro.', 1, '2025-05-19 00:42:15'),
(27, 3, 'reply', 'Han respondido a tu comentario.', 1, '2025-05-19 00:58:30'),
(28, 3, 'reply', 'Han respondido a tu comentario.', 1, '2025-05-21 02:17:10'),
(29, 4, 'reply', 'Han respondido a tu comentario.', 0, '2025-05-21 20:42:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rols`
--

CREATE TABLE `rols` (
  `role_Id` int(20) NOT NULL,
  `role_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rols`
--

INSERT INTO `rols` (`role_Id`, `role_name`) VALUES
(1, 'administrator'),
(2, 'user');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `userName` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `userImage` varchar(255) DEFAULT NULL,
  `estado` enum('activa','bloqueada','inactiva') DEFAULT 'activa',
  `intentos` int(11) DEFAULT 0,
  `ultimo_intento` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `userName`, `descripcion`, `userImage`, `estado`, `intentos`, `ultimo_intento`, `created_at`) VALUES
(1, 'juli', 'juli@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$L1J6RjBIRG5OR2xocWdKaQ$X5DDiUNdj/2p1g4QZvqllPC89grYlfOBR19AiQhNatY', 'sergio', NULL, '../uploads/profile_images/default.png', 'activa', 0, NULL, '2025-04-29 22:33:57'),
(2, 'nico', 'nico@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$bGlUcjVFUHo0bUcxRGdBaA$PktH/ovUkXxzxZt040GW9pADImnlRiONnIy0PqjNsxQ', 'nico', NULL, NULL, 'activa', 0, NULL, '2025-05-07 10:14:28'),
(3, '', 'zazaz@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$MEJ5a1RrS0NxM1d2LkRBSQ$Hm0qRo5Ol0wS2ygXaJ9SRFxoEg2TKO0plP9+O+PCdjA', 'KEVIN NARANJO', NULL, '../uploads/profile_images/7779848ea60c6cd9a4cda716e97066b0.jpg', 'activa', 0, NULL, '2025-05-14 19:29:17'),
(4, '', 'alalaaa@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$WEZnaFZIU1RuQXZoVFJvQg$7i5YYX0//PrqQBGqCQmxK8pWB9M0CthIj7JMjVAN8xc', 'Alej', NULL, '../uploads/profile_images/carval costruimos.jpg', 'activa', 0, NULL, '2025-05-20 22:06:57'),
(5, '', 'and17@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$Uk1aSVhBTDlTc1k1aFlReg$XiC8zK93trQlYCLgGdFRhtoFRBWp9AKztxj3plhDYck', 'jasjas', NULL, NULL, 'activa', 0, NULL, '2025-05-21 15:40:48');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foro_id` (`forum_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`userId`);

--
-- Indices de la tabla `forum_favorite`
--
ALTER TABLE `forum_favorite`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`id_foro`),
  ADD KEY `id_foro` (`id_foro`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rols`
--
ALTER TABLE `rols`
  ADD PRIMARY KEY (`role_Id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT de la tabla `forums`
--
ALTER TABLE `forums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT de la tabla `forum_favorite`
--
ALTER TABLE `forum_favorite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `forums`
--
ALTER TABLE `forums`
  ADD CONSTRAINT `forums_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `forum_favorite`
--
ALTER TABLE `forum_favorite`
  ADD CONSTRAINT `forum_favorite_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_favorite_ibfk_2` FOREIGN KEY (`id_foro`) REFERENCES `forums` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
