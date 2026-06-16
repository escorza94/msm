-- 1. Tabla de Categorías
CREATE TABLE IF NOT EXISTS inventario_categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar categoría por defecto
INSERT IGNORE INTO inventario_categorias (id, nombre) VALUES (1, 'General');

-- 2. Tabla de Proveedores
CREATE TABLE IF NOT EXISTS inventario_proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    telefono VARCHAR(50),
    email VARCHAR(100),
    direccion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Actualizar la tabla principal de inventario
ALTER TABLE inventario 
ADD COLUMN categoria_id INT DEFAULT 1 AFTER descripcion,
ADD COLUMN proveedor_id INT NULL AFTER categoria_id,
ADD COLUMN stock_minimo INT DEFAULT 5 AFTER stock,
ADD COLUMN ubicacion VARCHAR(150) NULL AFTER stock_minimo,
ADD COLUMN codigo_qr VARCHAR(255) NULL UNIQUE AFTER ubicacion;

-- 4. Tabla de Imágenes (Galería)
CREATE TABLE IF NOT EXISTS inventario_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    es_principal TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES inventario(id) ON DELETE CASCADE
);

-- 5. Tabla de Variantes (Atributos)
CREATE TABLE IF NOT EXISTS inventario_variantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    sku_variante VARCHAR(50) UNIQUE NOT NULL,
    atributo VARCHAR(50) NOT NULL, -- ej. 'Color', 'Material'
    valor VARCHAR(50) NOT NULL, -- ej. 'Rojo', 'Piel'
    precio_adicional DECIMAL(10,2) DEFAULT 0.00,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES inventario(id) ON DELETE CASCADE
);

-- 6. Tabla de Historial de Movimientos (Kardex)
CREATE TABLE IF NOT EXISTS inventario_movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    usuario_id INT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES inventario(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
);