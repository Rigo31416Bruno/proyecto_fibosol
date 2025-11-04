-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-11-2025 a las 10:24:39
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
-- Base de datos: `login_fidel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdCuenta` int(11) NOT NULL,
  `IdRol` int(11) DEFAULT NULL,
  `IdEstatus` int(11) DEFAULT NULL,
  `Nombre` varchar(75) NOT NULL,
  `Correo` varchar(50) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Direccion` varchar(255) DEFAULT NULL,
  `Telefono` char(10) DEFAULT NULL,
  `correo_verificado` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`IdCuenta`, `IdRol`, `IdEstatus`, `Nombre`, `Correo`, `Contrasena`, `Direccion`, `Telefono`, `correo_verificado`, `verification_token`, `token_expires`) VALUES
(1, NULL, NULL, 'rigo', 'rigo@gmail.com', '$2y$10$tCd6osRU7pZFfssH5gowiO9qTmSIc0mPuzvOVF7.kCtJTttoZTIKK', NULL, NULL, 0, NULL, NULL),
(7, NULL, NULL, 'rigo2', 'r.castro23@info.uas.edu.mx', '$2y$10$2ATuKDJBTiEnRrI6Kpx7keKUTgq7MTqxbdw536bKCmggqXGQAcbzu', NULL, NULL, 1, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IdCuenta`),
  ADD UNIQUE KEY `Correo` (`Correo`),
  ADD KEY `IdRol` (`IdRol`),
  ADD KEY `IdEstatus` (`IdEstatus`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdCuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`IdRol`) REFERENCES `roles` (`IdRol`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`IdEstatus`) REFERENCES `estatus` (`IdEstatus`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
