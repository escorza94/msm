-- Migración: Actualización para Cuentas por Cobrar y Anticipos

ALTER TABLE `ventas`
ADD COLUMN `monto_recibido` decimal(10,2) NOT NULL DEFAULT '0.00' AFTER `total`,
ADD COLUMN `cambio` decimal(10,2) NOT NULL DEFAULT '0.00' AFTER `monto_recibido`;