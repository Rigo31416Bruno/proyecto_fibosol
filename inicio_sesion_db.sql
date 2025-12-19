-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-12-2025 a las 07:47:09
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
-- Base de datos: `inicio_sesion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividadsesion`
--

CREATE TABLE `actividadsesion` (
  `ID_Registro` int(11) NOT NULL,
  `ID_Usuario` int(11) DEFAULT NULL,
  `FechaInicio` datetime DEFAULT NULL,
  `FechaFin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_producto_talla` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `total` decimal(10,2) DEFAULT NULL,
  `fecha_agregado` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ParentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `ParentID`) VALUES
(1, 'Hombre', NULL),
(2, 'Mujer', NULL),
(3, 'Camisas', 1),
(4, 'Chamarras', 1),
(5, 'Playeras', 1),
(6, 'Pantalones', 1),
(7, 'Shorts', 1),
(8, 'Trajes', 1),
(9, 'Jeans', 1),
(10, 'Abrigos', 1),
(11, 'Sudaderas', 1),
(12, 'Blusas', 2),
(13, 'Tops', 2),
(14, 'Faldas', 2),
(15, 'Vestidos', 2),
(16, 'Pantalones', 2),
(17, 'Shorts', 2),
(18, 'Chamarras', 2),
(19, 'Jerséis', 2),
(20, 'Trajes de baño', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_pedido` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_producto_talla` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id_pedido`, `id_producto`, `id_producto_talla`, `cantidad`, `precio_unitario`) VALUES
(1, 5, 10, 5, 200.00),
(2, 4, 8, 4, 500.00),
(2, 6, 15, 5, 300.00),
(3, 4, 8, 3, 500.00),
(4, 5, 10, 1, 200.00),
(4, 7, 17, 5, 700.00),
(5, 10, 25, 1, 400.00),
(6, 6, 14, 7, 300.00),
(6, 8, 19, 5, 200.00),
(7, 5, 11, 8, 200.00),
(7, 6, 13, 8, 300.00),
(8, 7, 16, 1, 700.00),
(8, 10, 26, 1, 400.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `ruta_pdf` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id_factura`, `id_usuario`, `id_pedido`, `ruta_pdf`, `fecha_creacion`) VALUES
(1, 13, 5, '../facturas/factura_5_1766052647.pdf', '2025-12-18 03:10:47'),
(2, 13, 5, '../facturas/factura_5_1766052721.pdf', '2025-12-18 03:12:01'),
(3, 13, 6, '../facturas/factura_6_1766112536.pdf', '2025-12-18 19:48:56'),
(4, 13, 7, '../facturas/factura_7_1766113546.pdf', '2025-12-18 20:05:46'),
(5, 13, 8, '../facturas/factura_8_1766124478.pdf', '2025-12-18 23:07:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha_pedido`, `total`) VALUES
(1, 13, '2025-12-18 02:34:43', 1000.00),
(2, 13, '2025-12-18 02:41:24', 3500.00),
(3, 13, '2025-12-18 02:59:09', 1500.00),
(4, 13, '2025-12-18 03:05:07', 3700.00),
(5, 13, '2025-12-18 03:10:09', 400.00),
(6, 13, '2025-12-18 19:48:52', 3100.00),
(7, 13, '2025-12-18 20:05:42', 4000.00),
(8, 13, '2025-12-18 23:07:56', 1100.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `categoria` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `precio`, `categoria`, `imagen`) VALUES
(1, 'Camisa Negra Simple', 200.00, 1, '../uploads/camisa_negra_simple.jpg'),
(3, 'Pantalones Rectos', 600.00, 2, '../uploads/pantalones_rectos.jpg'),
(4, 'Camisa Negra Lisa', 500.00, 5, '../img/playera_negra_lisa.jpg'),
(5, 'Playera Negra Stwd', 200.00, 5, '../img/playera_negra_stwd.jpg'),
(6, 'Playera Negra Paradise', 300.00, 5, '../img/playera_negra_paradise.jpg'),
(7, 'Playera Imagine Dragons', 700.00, 5, '../img/playera_imagine_dragons.jpg'),
(8, 'Pantalones Slim', 200.00, 6, '../img/pantalones_slim.jpg'),
(9, 'Pantalones Skinny', 350.00, 6, '../img/pantalones_skinny.jpg'),
(10, 'Pantalones Cargo Negros', 400.00, 6, '../img/pantalones_cargo_negros.jpg'),
(11, 'Pantalones Wide Leg', 250.00, 6, '../img/pantalones_wide_leg.jpg'),
(12, 'Playera Estampado Ciudad', 700.00, 5, '../img/playera_estampado_ciudad.jpg'),
(13, 'Blusa Chifon Lazos', 200.00, 12, '../img/blusa_chifon_lazos.jpg'),
(14, 'Blusa Peplum', 350.00, 12, '../img/blusa_peplum.jpg'),
(15, 'Blusa Oversized', 500.00, 12, '../img/blusa_oversized.jpg'),
(16, 'Blusa Mezcla Lino', 450.00, 12, '../img/blusa_mezcla_lino.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_tallas`
--

CREATE TABLE `producto_tallas` (
  `id_producto_talla` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_talla` int(11) NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_tallas`
--

INSERT INTO `producto_tallas` (`id_producto_talla`, `id_producto`, `id_talla`, `stock`) VALUES
(1, 1, 1, 10),
(2, 1, 2, 15),
(3, 1, 3, 12),
(4, 3, 1, 8),
(5, 3, 2, 20),
(6, 3, 3, 15),
(7, 4, 1, 0),
(8, 4, 2, -2),
(9, 4, 3, 1),
(10, 5, 1, 0),
(11, 5, 2, 2),
(12, 5, 3, 15),
(13, 6, 1, -8),
(14, 6, 2, -7),
(15, 6, 3, 0),
(16, 7, 1, 8),
(17, 7, 2, 0),
(18, 7, 3, 11),
(19, 8, 1, -1),
(20, 8, 2, 14),
(21, 8, 3, 12),
(22, 9, 1, 11),
(23, 9, 2, 16),
(24, 9, 3, 13),
(25, 10, 1, 0),
(26, 10, 2, 16),
(27, 10, 3, 8),
(28, 11, 1, 10),
(29, 11, 2, 14),
(30, 11, 3, 12),
(31, 12, 1, 8),
(32, 12, 2, 12),
(33, 12, 3, 10),
(34, 13, 1, 9),
(35, 13, 2, 13),
(36, 13, 3, 11),
(37, 14, 1, 10),
(38, 14, 2, 14),
(39, 14, 3, 12),
(40, 15, 1, 11),
(41, 15, 2, 15),
(42, 15, 3, 13),
(43, 16, 1, 0),
(44, 16, 2, 0),
(45, 16, 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperaciones`
--

CREATE TABLE `recuperaciones` (
  `ID` int(11) NOT NULL,
  `Correo` varchar(255) NOT NULL,
  `Codigo` varchar(32) NOT NULL,
  `ExpiresAt` datetime NOT NULL,
  `CreatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `ID_Rol` int(11) NOT NULL,
  `NombreRol` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`ID_Rol`, `NombreRol`) VALUES
(1, 'Cliente'),
(2, 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tallas`
--

CREATE TABLE `tallas` (
  `id_talla` int(11) NOT NULL,
  `nombre` varchar(10) NOT NULL,
  `orden` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tallas`
--

INSERT INTO `tallas` (`id_talla`, `nombre`, `orden`) VALUES
(1, 'CH', 1),
(2, 'MED', 2),
(3, 'GDE', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_Usuario` int(11) NOT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Contraseña` varchar(100) DEFAULT NULL,
  `ID_Rol` int(11) DEFAULT NULL,
  `Direccion` varchar(100) DEFAULT NULL,
  `Telefono` char(10) DEFAULT NULL,
  `Estatus` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_Usuario`, `Nombre`, `Correo`, `Contraseña`, `ID_Rol`, `Direccion`, `Telefono`, `Estatus`) VALUES
(13, 'rigoberto', 'r.castro23@info.uas.edu.mx', '$2y$10$c/Fp7G0uJgu14TtFuM2ZKu9D4wSD8Jc7DzQ5KiGSkGsiqFOnoEUM.', 1, 'Calle de la Buenaventura, 4 de Marzo, Culiacán, Culiacán Rosales, Culiacán, Sinaloa, 80054, México', NULL, 'activo'),
(15, 'rigohot', 'rigotrainer@gmail.com', '$2y$10$JjDOg18hVUwK8aIZ/4WspuvKBG6UjjMIE/RBGc5ZXB3JPj2idpKma', 2, NULL, NULL, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `verificaciones`
--

CREATE TABLE `verificaciones` (
  `ID` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Correo` varchar(100) NOT NULL,
  `ContrasenaHash` varchar(255) NOT NULL,
  `Token` varchar(64) NOT NULL,
  `Codigo` varchar(16) DEFAULT NULL,
  `ExpiresAt` datetime NOT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividadsesion`
--
ALTER TABLE `actividadsesion`
  ADD PRIMARY KEY (`ID_Registro`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `idx_id_producto` (`id_producto`),
  ADD KEY `unique_usuario_producto` (`id_usuario`,`id_producto`) USING BTREE;

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD KEY `ParentID` (`ParentID`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_producto_talla` (`id_producto_talla`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `categoria` (`categoria`);

--
-- Indices de la tabla `producto_tallas`
--
ALTER TABLE `producto_tallas`
  ADD PRIMARY KEY (`id_producto_talla`),
  ADD UNIQUE KEY `unique_producto_talla` (`id_producto`,`id_talla`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_talla` (`id_talla`);

--
-- Indices de la tabla `recuperaciones`
--
ALTER TABLE `recuperaciones`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Correo` (`Correo`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID_Rol`);

--
-- Indices de la tabla `tallas`
--
ALTER TABLE `tallas`
  ADD PRIMARY KEY (`id_talla`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Correo` (`Correo`);

--
-- Indices de la tabla `verificaciones`
--
ALTER TABLE `verificaciones`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Token` (`Token`),
  ADD KEY `Correo` (`Correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `producto_tallas`
--
ALTER TABLE `producto_tallas`
  MODIFY `id_producto_talla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `recuperaciones`
--
ALTER TABLE `recuperaciones`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `ID_Rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tallas`
--
ALTER TABLE `tallas`
  MODIFY `id_talla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `verificaciones`
--
ALTER TABLE `verificaciones`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`ParentID`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_3` FOREIGN KEY (`id_producto_talla`) REFERENCES `producto_tallas` (`id_producto_talla`) ON DELETE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `producto_tallas`
--
ALTER TABLE `producto_tallas`
  ADD CONSTRAINT `producto_tallas_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE,
  ADD CONSTRAINT `producto_tallas_ibfk_2` FOREIGN KEY (`id_talla`) REFERENCES `tallas` (`id_talla`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
