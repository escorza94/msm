-- Tabla para las Colecciones
CREATE TABLE tienda_colecciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    imagen_portada VARCHAR(255) DEFAULT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla pivote (Relación muchos a muchos: Colecciones <-> Inventario)
CREATE TABLE tienda_coleccion_productos (
    coleccion_id INT NOT NULL,
    producto_id INT NOT NULL,
    PRIMARY KEY (coleccion_id, producto_id)
);

-- Tabla para Páginas Estáticas (Sobre Nosotros, Aviso de Privacidad, etc.)
CREATE TABLE tienda_paginas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    contenido_html LONGTEXT,
    estado ENUM('publicado', 'borrador') DEFAULT 'publicado',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para guardar el JSON con el orden y configuración de las Secciones
CREATE TABLE tienda_tema_config (
    clave VARCHAR(100) PRIMARY KEY,
    valor_json JSON NOT NULL
);