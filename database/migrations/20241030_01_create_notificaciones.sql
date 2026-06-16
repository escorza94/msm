CREATE TABLE IF NOT EXISTS `notificaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `mensaje` TEXT NOT NULL,
    `enlace` VARCHAR(255) DEFAULT NULL,
    `leida` TINYINT(1) DEFAULT 0,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);