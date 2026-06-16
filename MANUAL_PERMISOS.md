# 🛡️ Guía de Implementación: Matriz de Permisos (RBAC)

Este documento explica cómo utilizar el sistema de roles y permisos que acabamos de instalar en el ERP.

## 1. Conceptos Básicos
El sistema funciona a través de 3 tablas conectadas:
- `roles`: Contiene los nombres de los grupos (Ej. SuperAdmin, Vendedor, Chofer).
- `permissions`: El catálogo de permisos (Se recomienda usar puntos: `pos.ver`).
- `role_permissions`: Guarda las opciones que marcas en la Interfaz Web.

## 2. Inicialización de Datos (SQL)
Para que la Interfaz Visual funcione, necesitas que existan los permisos base en tu base de datos. Si no los tienes, ejecuta este SQL en tu phpMyAdmin:

```sql
-- Insertar algunos Roles si no los tienes aún
INSERT IGNORE INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'SuperAdministrador', 'Acceso irrestricto a todo el sistema'),
(2, 'Vendedor / Cajero', 'Acceso a ventas, clientes y WhatsApp'),
(3, 'Logística', 'Control de envíos y entregas');

-- Insertar los Permisos que existirán en el sistema
INSERT IGNORE INTO `permissions` (`name`, `description`) VALUES
('pos.ver', 'Ver el Punto de Venta (POS) y catálogo'),
('pos.crear', 'Crear y cobrar ventas'),
('clientes.ver', 'Ver el directorio de clientes'),
('clientes.crear', 'Crear o editar clientes en CRM'),
('whatsapp.ver', 'Ver y responder mensajes en WhatsApp'),
('finanzas.ver', 'Ver el libro mayor y estados de cuenta'),
('logistica.ver', 'Ver el tablero Kanban de envíos'),
('logistica.editar', 'Mover tarjetas y cambiar estados de envío');
```
*(Nota: Si tus tablas tienen otros nombres de columnas, como `nombre` en lugar de `name`, ajusta el SQL antes de correrlo).*

## 3. ¿Cómo proteger un Controlador en PHP?
Abre cualquier controlador (Ej. `PosController.php`) y justo después de `auth_require();` agrega:
```php
require_permission('pos.ver');
```
Si el usuario (cuyo rol no tenga la palomita en ese permiso) intenta entrar a la fuerza copiando la URL, será expulsado de inmediato con un mensaje rojo.

## 4. ¿Cómo ocultar un botón en el HTML?
Si solo quieres ocultar el botón de "Nuevo Ingreso" en Finanzas para que el cajero no lo vea:
```php
<?php if(has_permission('finanzas.crear')): ?>
    <button>Nuevo Ingreso</button>
<?php endif; ?>
```

## 5. El SuperAdministrador
Por regla de oro, el usuario cuyo `role_id` sea `1` **siempre** pasará las validaciones. El sistema nunca lo bloqueará, garantizando que el dueño del negocio jamás se quede fuera por error de configuración.

## 6. Siguientes Pasos
Dirígete a tu menú lateral: **Usuarios > Roles y Permisos** y verás la nueva pantalla de asignación.