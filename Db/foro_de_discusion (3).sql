-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-04-2025 a las 16:08:11
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
-- Estructura de tabla para la tabla `foros`
--

CREATE TABLE `foros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `foros`
--

INSERT INTO `foros` (`id`, `titulo`, `descripcion`, `id_usuario`, `fecha_creacion`) VALUES
(2, 'sadda', 'asdasd', 29, '2025-04-02 23:09:07'),
(3, 'edsdasadsa', 'fdsfsdfsdfs', 29, '2025-04-02 23:11:35'),
(4, 'sadasd', 'sadadasdsa', 29, '2025-04-02 23:14:03'),
(5, 'sadadsadasd', 'sadadaasd', 29, '2025-04-02 23:14:44'),
(6, 'edsdasadsa', 'sadasdas', 29, '2025-04-02 23:53:35'),
(7, 'sadsad', 'sadad', 29, '2025-04-02 23:54:00'),
(8, 'adsas', '3213232', 29, '2025-04-02 23:56:21'),
(9, 'adsas', 'sadasda', 29, '2025-04-03 00:31:59'),
(10, 'scasdad', 'asdasddas', 29, '2025-04-03 00:32:11'),
(12, 'dassadasd', 'sdsadadasdasdsdasdasd as dsadsas s s s as s a saassad', 27, '2025-04-03 17:57:41'),
(13, 'dfdsfsdf', 'dsffsfsfdsfsdsd sds fds dm, s d s ls l slds', 27, '2025-04-04 16:16:58'),
(14, 'edsdasadsaa a a a a a a a a a a  a a', 'aaaaaaaaa a a a aa a a a a', 27, '2025-04-04 16:49:13'),
(15, 'edsdasadsa', 'aaaaaaaaa a a a aa a a a a', 27, '2025-04-04 16:49:46'),
(16, 'edsdasadsa', 'aaaaaaaaa a a a aa a a a a', 27, '2025-04-04 16:49:47'),
(17, 'edsdasadsa', 'aaaaaaaaa a a a aa a a a a', 27, '2025-04-04 16:49:48'),
(18, 'edsdasadsa', 'aaaaaaaaa a a a aa a a a a', 27, '2025-04-04 16:49:48'),
(19, 'adsas', 'aaaaaaaaa a a a aa a a a a', 27, '2025-04-04 16:50:48'),
(20, 'edsdasadsaaA', 'aaaaaaaaa a a a aa a a a a', 27, '2025-04-04 16:52:22'),
(21, 'adsas', 'sadadasd sada da ds asd asd', 27, '2025-04-04 16:58:57'),
(22, 'sahkdhaskdhas dasjhdkja skdsakhd ashdksahkdjhakjhdas', 'ksadljasdjlaskjdlasdjalksj dlajdlkasjdlkasjlksajdlaskdksajdlkjaskld', 27, '2025-04-04 17:28:15'),
(23, 'edsdasadsa', 'a a a a a a a aa a a a  aa a  a a', 27, '2025-04-05 21:56:41'),
(24, 'sdfsdfsdfsd', 'asnjkdhaskjhdkashjkdhjasndjkahsuicsammd kjasdhas psjadsklanlkansc', 27, '2025-04-07 07:49:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `role_Id` int(20) NOT NULL,
  `role_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`role_Id`, `role_name`) VALUES
(1, 'administrator'),
(2, 'user');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `intentos` int(11) DEFAULT 0,
  `ultimo_intento` datetime DEFAULT NULL,
  `estado` enum('activa','bloqueada') DEFAULT 'activa',
  `descripcion` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `fk_role_Id` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `intentos`, `ultimo_intento`, `estado`, `descripcion`, `profile_image`, `fk_role_Id`) VALUES
(27, 'juli', 'juli@gmail.com', '$2y$10$UH6vq1lCTRJDQyu9jWQx4O2HDpyKEl96q5C4WzrEpbggYlXPQXoaG', 0, NULL, 'activa', 'jajajaja', 'profile_27_67e36fb13e75a.png', 2),
(29, 'sergio', 'sergio@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$cHV5VGtlbWppRWVBLnhkQg$xavh95R4uaMVAI0Cig3cDDGu9lolK9GL0is6tdXJLIU', 4, '2025-04-03 17:41:29', 'activa', NULL, 'profile_27_67e36fb13e75a.png', 1),
(30, 'juli', 'zazaz@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$V3VYd2g3RXplL0dGV2NwbQ$hZj3jpUpmX+zXqi4KvLXQepz5F7KL/uHjt+MsJfvNB0', 0, NULL, 'activa', NULL, NULL, 2),
(31, 'alll', 'carlospos11@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$RG5hS0VqdE1zTEZOeWhiYg$0cZ993y79xhfX3jR8lh38EAg1bxnR6awZFPRiI4mQqI', 0, NULL, 'activa', NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `foros`
--
ALTER TABLE `foros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_Id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_role_Id` (`fk_role_Id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `foros`
--
ALTER TABLE `foros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `foros`
--
ALTER TABLE `foros`
  ADD CONSTRAINT `foros_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
