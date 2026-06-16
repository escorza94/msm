-- Migración: Módulo de Finanzas (Cuentas y Libro Mayor)

-- 1. Tabla de Cuentas (Dónde está el dinero)
CREATE TABLE IF NOT EXISTS `finanzas_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('efectivo','banco','terminal','otro') NOT NULL DEFAULT 'efectivo',
  `saldo_inicial` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla de Categorías (Conceptos de ingresos y egresos para reportes)
CREATE TABLE IF NOT EXISTS `finanzas_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo_defecto` enum('ingreso','egreso','ambos') NOT NULL DEFAULT 'ambos',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla Central de Movimientos (El Libro Mayor / Ledger)
CREATE TABLE IF NOT EXISTS `finanzas_movimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuenta_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `concepto` varchar(255) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL COMMENT 'Efectivo, Tarjeta, Transferencia',
  `comprobante` varchar(255) DEFAULT NULL COMMENT 'Ruta a imagen o PDF de voucher',
  `origen_tipo` varchar(50) DEFAULT NULL COMMENT 'ej. venta, traspaso, abono, manual',
  `origen_id` int(11) DEFAULT NULL COMMENT 'ID del registro de origen (ej. ID de venta)',
  `grupo_transaccion` varchar(50) DEFAULT NULL COMMENT 'UUID para vincular traspasos (ingreso y egreso)',
  `fecha_movimiento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cuenta_id` (`cuenta_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `fk_finanzas_mov_cuenta` FOREIGN KEY (`cuenta_id`) REFERENCES `finanzas_cuentas` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_finanzas_mov_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_finanzas_mov_cat` FOREIGN KEY (`categoria_id`) REFERENCES `finanzas_categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos iniciales por defecto (Catálogos base)
INSERT IGNORE INTO `finanzas_cuentas` (`id`, `nombre`, `tipo`, `saldo_inicial`) VALUES
(1, 'Caja Mostrador', 'efectivo', 0.00),
(2, 'Cuenta BBVA', 'banco', 0.00),
(3, 'Terminal Clip', 'terminal', 0.00);

INSERT IGNORE INTO `finanzas_categorias` (`id`, `nombre`, `tipo_defecto`) VALUES
(1, 'Venta de Contado', 'ingreso'),
(2, 'Abono a Cuenta', 'ingreso'),
(3, 'Aportación de Capital', 'ingreso'),
(4, 'Pago a Proveedor', 'egreso'),
(5, 'Pago de Servicios (Luz, Agua, Internet)', 'egreso'),
(6, 'Nómina y Comisiones', 'egreso'),
(7, 'Gastos de Operación (Gasolina, Papelería)', 'egreso'),
(8, 'Traspaso Interno', 'ambos');