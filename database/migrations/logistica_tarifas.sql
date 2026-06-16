-- Migración: Módulo de Logística (Tarifas y Configuración)

-- Tabla para las tarifas de envío
CREATE TABLE IF NOT EXISTS `logistica_tarifas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('fija', 'distancia') NOT NULL DEFAULT 'fija',
  `precio_base` decimal(10,2) NOT NULL DEFAULT '0.00',
  `km_base` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Kilómetros incluidos en el precio base',
  `precio_km_extra` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Costo por cada km extra',
  `estado` enum('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla para configuración de la sucursal (punto de origen para cálculo de distancia)
CREATE TABLE IF NOT EXISTS `logistica_configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(50) NOT NULL,
  `valor` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos iniciales de prueba
INSERT IGNORE INTO `logistica_tarifas` (`id`, `nombre`, `tipo`, `precio_base`, `km_base`, `precio_km_extra`) VALUES
(1, 'Recoger en Tienda', 'fija', 0.00, 0.00, 0.00),
(2, 'Envío por Distancia Automático', 'distancia', 150.00, 10.00, 25.00);

-- Coordenadas por defecto (Ejemplo para calcular distancia. Se podrán cambiar en el sistema)
INSERT IGNORE INTO `logistica_configuracion` (`clave`, `valor`) VALUES
('latitud_sucursal', '20.659698'),
('longitud_sucursal', '-103.349609');