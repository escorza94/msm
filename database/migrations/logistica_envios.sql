-- Migración: Módulo de Logística (Envíos)

CREATE TABLE IF NOT EXISTS `logistica_envios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `chofer_id` int(11) DEFAULT NULL,
  `estado` enum('pendiente','en_ruta','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `coordenadas_destino` varchar(100) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_entrega` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  CONSTRAINT `fk_logistica_envios_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;