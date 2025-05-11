-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2025 a las 05:50:10
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
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comments`
--

INSERT INTO `comments` (`id`, `forum_id`, `user_id`, `content`, `created_at`, `updated_at`) VALUES
(52, 63, 1, 'Yo estoy libre', '2025-04-30 10:32:27', '2025-04-30 10:32:27'),
(53, 63, 1, 'Yo voy es por la cousin', '2025-04-30 10:32:44', '2025-04-30 10:32:44'),
(54, 63, 1, 'Llamame guapo', '2025-04-30 10:33:06', '2025-04-30 10:33:06'),
(55, 59, 1, 'xsdad', '2025-04-30 10:55:54', '2025-04-30 10:55:54'),
(57, 64, 1, 'no see', '2025-04-30 11:08:24', '2025-04-30 11:08:24'),
(58, 58, 1, 'a', '2025-05-10 00:54:17', '2025-05-10 00:54:17'),
(59, 58, 1, 'aaa', '2025-05-10 00:57:27', '2025-05-10 01:03:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forums`
--

CREATE TABLE `forums` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `userId` int(11) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `forums`
--

INSERT INTO `forums` (`id`, `title`, `description`, `userId`, `createdAt`) VALUES
(58, 'asasa', 'asdasdasdsdadasdasd', 1, '2025-04-30 00:00:00'),
(59, 'dasdlkjkasljd', 'dwlkñasjdlksajdlkñasjklñdjaslñjdkla', 1, '2025-04-30 00:00:00'),
(60, 'djsakljdñasljdñ', 'jdslakjdlakj´dkdsajdsllasdlñk', 1, '2025-04-30 00:00:00'),
(61, 'ñllds{fdskfñl{ksdñk', 'klñskfñllskdjsnfjkshdñoifsanasñhfasonfaso', 1, '2025-04-30 00:00:00'),
(63, '¿Por que la vida es asi?', 'Tengo una gran pregunta mi compañero santiago  no ha tenido una novia,entonces busco a alguien que lo quiera', 1, '2025-04-30 00:00:00'),
(64, 'Hola como estan', 'asdlsadkjaslkjkdlasjdlasjdlajldjlasjdlasjk', 1, '2025-04-30 00:00:00');

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
(23, 1, 58, '2025-04-30 11:45:58');

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
(1, 'juli', 'juli@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$L1J6RjBIRG5OR2xocWdKaQ$X5DDiUNdj/2p1g4QZvqllPC89grYlfOBR19AiQhNatY', 'sergio', NULL, 'default.jpg', 'activa', 0, NULL, '2025-04-29 22:33:57'),
(2, 'nico', 'nico@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$bGlUcjVFUHo0bUcxRGdBaA$PktH/ovUkXxzxZt040GW9pADImnlRiONnIy0PqjNsxQ', '', NULL, NULL, 'activa', 0, NULL, '2025-05-07 10:14:28');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `forums`
--
ALTER TABLE `forums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `forum_favorite`
--
ALTER TABLE `forum_favorite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
