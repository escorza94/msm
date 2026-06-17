-- ==============================================================================
-- MIGRACIÓN: Arquitectura Tienda Online y CMS Modular (Layout Builder)
-- ==============================================================================

-- 1. Tabla de Configuración Global del Tema (Ajustes y Apariencia)
CREATE TABLE IF NOT EXISTS `tienda_tema_config` (
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar configuración por defecto
INSERT IGNORE INTO `tienda_tema_config` (`clave`, `valor`) VALUES
('seo_titulo', 'Mueblería San Martín | Calidad y Confianza'),
('seo_descripcion', 'Encuentra los mejores muebles para tu hogar. Calidad, buen precio y excelente servicio.'),
('whatsapp_numero', '5215555555555'),
('facebook_url', 'https://facebook.com/muebleriasanmartin'),
('instagram_url', 'https://instagram.com/muebleriasanmartin'),
('footer_texto', 'Calidad y confianza para amueblar tu vida. Contáctanos para cualquier duda o cotización.');

-- 2. Motor de Colecciones (Agrupaciones de Productos)
CREATE TABLE IF NOT EXISTS `tienda_colecciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `creado_en` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Colección requerida por defecto para la página de inicio
INSERT IGNORE INTO `tienda_colecciones` (`id`, `nombre`, `slug`, `descripcion`, `estado`) VALUES
(1, 'Productos Destacados', 'destacados', 'Lo mejor y más nuevo de nuestro catálogo.', 'activo');

-- 3. Tabla Pivote: Productos dentro de Colecciones
CREATE TABLE IF NOT EXISTS `tienda_coleccion_productos` (
  `coleccion_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`coleccion_id`,`producto_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `fk_tcp_coleccion` FOREIGN KEY (`coleccion_id`) REFERENCES `tienda_colecciones` (`id`) ON DELETE CASCADE
  -- NOTA: Se asume que existe la tabla `inventario` (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Estructura Base del CMS (Páginas)
CREATE TABLE IF NOT EXISTS `tienda_paginas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `estado` enum('publicado','borrador') DEFAULT 'publicado',
  `creado_en` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear la página principal (Inicio)
INSERT IGNORE INTO `tienda_paginas` (`id`, `titulo`, `slug`, `estado`) VALUES
(1, 'Inicio', 'inicio', 'publicado');

-- 5. Secciones Modulares (Los Ladrillos/Widgets del Diseño)
CREATE TABLE IF NOT EXISTS `tienda_secciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_interno` varchar(255) NOT NULL,
  `tipo` varchar(50) NOT NULL COMMENT 'carrusel_banners, grid_productos, texto_imagen, html_libre',
  `configuracion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'JSON con datos',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar dos secciones de ejemplo (Un carrusel y un Grid de Destacados)
INSERT IGNORE INTO `tienda_secciones` (`id`, `nombre_interno`, `tipo`, `configuracion`, `estado`) VALUES
(1, 'Carrusel Principal de Bienvenida', 'carrusel_banners', '{"banners": [{"imagen": "storage/landing/banner1.jpg", "enlace": "#", "titulo": "Gran Venta"}, {"imagen": "storage/landing/banner2.jpg", "enlace": "/ofertas", "titulo": "Meses sin intereses"}]}', 'activo'),
(2, 'Cuadrícula de Novedades', 'grid_productos', '{"titulo_seccion": "Nuestros Favoritos", "subtitulo": "Descubre lo que todos están comprando", "coleccion_slug": "destacados", "limite_mostrar": 8}', 'activo');

-- 6. Constructor Visual (Qué secciones van en qué página)
CREATE TABLE IF NOT EXISTS `tienda_pagina_secciones` (
  `pagina_id` int(11) NOT NULL,
  `seccion_id` int(11) NOT NULL,
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`pagina_id`,`seccion_id`),
  KEY `seccion_id` (`seccion_id`),
  CONSTRAINT `fk_tps_pagina` FOREIGN KEY (`pagina_id`) REFERENCES `tienda_paginas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tps_seccion` FOREIGN KEY (`seccion_id`) REFERENCES `tienda_secciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Asignar las secciones creadas a la página de Inicio (ID 1)
INSERT IGNORE INTO `tienda_pagina_secciones` (`pagina_id`, `seccion_id`, `orden`) VALUES
(1, 1, 1), -- Primero el Carrusel (Orden 1)
(1, 2, 2); -- Luego los Productos Destacados (Orden 2)