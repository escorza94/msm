CREATE TABLE IF NOT EXISTS wa_contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    whatsapp_id VARCHAR(50) NOT NULL UNIQUE,
    tipo_chat ENUM('individual', 'grupo', 'estado') DEFAULT 'individual',
    nombre VARCHAR(150) NOT NULL,
    etiqueta VARCHAR(50) DEFAULT 'desconocido',
    bot_activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wa_mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contacto_id INT NOT NULL,
    usuario_id INT NULL,
    direccion ENUM('entrante', 'saliente') NOT NULL,
    tipo ENUM('texto', 'imagen', 'documento', 'audio', 'video', 'sticker') DEFAULT 'texto',
    contenido TEXT NOT NULL,
    estado ENUM('enviado', 'entregado', 'leido', 'error', 'recibido') DEFAULT 'enviado',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contacto_id) REFERENCES wa_contactos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
);