CREATE TABLE IF NOT EXISTS `promociones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `tipo` enum('porcentaje','monto_fijo') NOT NULL DEFAULT 'porcentaje',
  `valor` decimal(10,2) NOT NULL DEFAULT '0.00',
  `codigo_cupon` varchar(50) DEFAULT NULL,
  `monto_minimo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cantidad_minima` int(11) NOT NULL DEFAULT '0',
  `productos_requeridos` varchar(255) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_cupon` (`codigo_cupon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Validar y agregar 'cantidad_minima' si la tabla ya existía sin perder datos
SET @dbname = DATABASE();
SET @tablename = 'promociones';
SET @columnname = 'cantidad_minima';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' int(11) NOT NULL DEFAULT 0 AFTER monto_minimo;')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Validar y agregar 'productos_requeridos' si la tabla ya existía sin perder datos
SET @columnname2 = 'productos_requeridos';
SET @preparedStatement2 = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname2)) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname2, ' varchar(255) DEFAULT NULL AFTER cantidad_minima;')
));
PREPARE alterIfNotExists2 FROM @preparedStatement2;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;