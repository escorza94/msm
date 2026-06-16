CREATE TABLE IF NOT EXISTS `marketing_campanas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `texto_disparador` varchar(255) NOT NULL,
  `respuesta_automatica` text NOT NULL,
  `etiqueta_contacto` varchar(50) DEFAULT NULL,
  `promocion_id` int(11) DEFAULT NULL,
  `activar_bot` tinyint(1) DEFAULT 1,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;