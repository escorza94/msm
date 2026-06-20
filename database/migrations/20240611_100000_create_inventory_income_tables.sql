-- Migración para crear las tablas del módulo de Ingresos de Inventario
-- y añadir campos de costo al Kardex.

-- 1. Tabla para el encabezado del ingreso (quién, cuándo y con qué factura)
CREATE TABLE IF NOT EXISTS `inventario_ingresos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `referencia_factura` varchar(100) DEFAULT NULL,
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp(),
  `notas` text DEFAULT NULL,
  `costo_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabla para los productos específicos dentro de cada ingreso
CREATE TABLE IF NOT EXISTS `inventario_ingreso_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ingreso_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ingreso_id` (`ingreso_id`),
  KEY `producto_id` (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. MEJORA: Añadimos el costo al historial de movimientos (Kardex)
-- Esto nos permitirá saber el valor del inventario en cualquier movimiento.

-- Añadir 'costo_unitario' si no existe
SET @dbname = DATABASE();
SET @tablename = 'inventario_movimientos';
SET @columnname = 'costo_unitario';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0, 'SELECT 1', CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' DECIMAL(10,2) NULL DEFAULT NULL AFTER `cantidad`;')));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- Añadir 'costo_total' si no existe
SET @columnname2 = 'costo_total';
SET @preparedStatement2 = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname2) > 0, 'SELECT 1', CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname2, ' DECIMAL(12,2) NULL DEFAULT NULL AFTER `costo_unitario`;')));
PREPARE alterIfNotExists2 FROM @preparedStatement2; EXECUTE alterIfNotExists2; DEALLOCATE PREPARE alterIfNotExists2;