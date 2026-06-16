-- Migración: Tabla para el registro de Abonos a Ventas a Crédito / Anticipos

CREATE TABLE IF NOT EXISTS `ventas_abonos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT 'Efectivo',
  `notas` varchar(255) DEFAULT NULL,
  `fecha_abono` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;