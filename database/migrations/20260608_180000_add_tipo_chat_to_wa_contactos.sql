-- Migración para agregar la columna tipo_chat si no existe

SET @dbname = DATABASE();
SET @tablename = 'wa_contactos';
SET @columnname = 'tipo_chat';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1', -- Si existe, simplemente ejecuta un SELECT 1 vacío
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ENUM(''individual'', ''grupo'', ''estado'') DEFAULT ''individual'' AFTER whatsapp_id;')
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;