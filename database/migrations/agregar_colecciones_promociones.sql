-- 1. Agregamos la columna 'tipo' a la tabla de colecciones (por defecto serán de 'productos')
ALTER TABLE tienda_colecciones 
ADD COLUMN tipo ENUM('productos', 'promociones') NOT NULL DEFAULT 'productos' AFTER descripcion;

-- 2. Creamos la tabla pivote para vincular colecciones con promociones
CREATE TABLE IF NOT EXISTS tienda_coleccion_promociones (
    coleccion_id INT NOT NULL,
    promocion_id INT NOT NULL,
    PRIMARY KEY (coleccion_id, promocion_id)
);