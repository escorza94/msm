# 🏢 Plan de Desarrollo: ERP Mueblería San Martín

Este documento describe la arquitectura y el roadmap del sistema de gestión integral (ERP + CRM) desarrollado de forma modular.

---

## ✅ Módulos Completados (Fase 1)

1. **Core / Dashboard**: Motor HMVC, sistema de ruteo dinámico y plantilla global (Tailwind + FontAwesome).
2. **Usuarios**: Sistema de autenticación, control de sesiones, protección de rutas y gestión de personal.
3. **WhatsApp Gateway**: Integración en tiempo real con Node.js, envío/recepción de mensajes, multimedia, manejo de contactos (Directorio) y estados.
4. **Inventario**: Catálogo avanzado de productos, control de stock, alertas de escasez (Stock mínimo), generador de etiquetas inteligentes (Códigos QR) y galería de imágenes múltiples.
5. **Proveedores**: Directorio independiente de empresas y contactos de abastecimiento.
6. **Clientes (CRM Central)**: Registro estructurado con geolocalización avanzada (Google Maps oculto/coordenadas) y vinculación directa con chats de WhatsApp.

---

## 🚀 Próximos Módulos a Desarrollar (Fase 2 - Roadmap)

### 1. Módulo de Ventas y Cotizaciones (POS) - 🔄 EN PROGRESO
* **Objetivo**: El motor comercial que conecta el Inventario, Logística y Clientes.
* **Características Principales**:
  * ✅ **Interfaz Punto de Venta**: Buscador ágil por SKU/Nombre con visualización de stock en tiempo real y selector de clientes.
  * **Cotizador rápido**: Selecciona productos, calcula totales y genera un **PDF elegante** listo para mandarse por WhatsApp con 1 clic.
  * ✅ **Notas de Venta**: Conversión de la cotización en una venta formal.
  * ✅ **Automatización de Stock**: Al confirmar una venta, el sistema genera automáticamente el descuento (salida) en el Kardex del Inventario.
  * 🔄 **Integración Logística (Envío)**: Selección de tarifas de fletes dinámicas (fijas o por distancia kilométrica) en la pantalla de cobro.

### 2. Módulo de Caja y Finanzas (Cuentas, Movimientos y Abonos) - 🔄 EN PROGRESO
* **Objetivo**: Controlar el flujo de efectivo, anticipos y deudas centralizando ingresos y egresos en un Libro Mayor.
* **Características Principales**:
  * **Catálogo de Cuentas / Bancos**: Definir y gestionar cuentas (Ej. Caja Chica Mostrador, Cuenta BBVA, Terminal Clip, Efectivo).
  * **Control de Movimientos**: 
    * *Ingresos*: Ventas de contado, anticipos (enganches), abonos a deudas, aportaciones externas.
    * *Egresos*: Pago a proveedores, nómina, gastos operativos (luz, gasolina), devoluciones a clientes.
    * *Traspasos*: Transferir saldos internamente (Ej. Vaciar la caja física a la cuenta bancaria al final del día).
  * **Estado de Cuenta de Clientes (Gestión de Deudas)**:
    * Clasificación de "Ventas de Contado" vs "Ventas a Crédito / Sistema de Apartado".
    * Registro ágil de Abonos a notas pendientes con impresión de tickets/recibos de pago.
  * *Plus*: Conexión con WhatsApp para enviar recordatorios de pago automáticos.
  * **Cortes de Caja**: Reporte diario detallado de saldos de apertura, entradas, salidas y saldos de cierre, agrupado por cuenta o método de pago.

### 3. Módulo de Logística (Envíos y Entregas)
* **Objetivo**: Garantizar que el mueble llegue a la casa del cliente de forma ordenada.
* **Características Principales**:
  * 🔄 **Gestión de Zonas y Tarifas**: CRUD de tarifas fijas y configuración de cobro dinámico por distancia (Ej. 10km base + $X por km extra).
  * 🔄 **Gestión de Envíos**: Panel para organizar el envío de las ventas concretadas en el POS (Registro automático desde POS implementado).
  * ✅ **Tablero visual (Kanban)**: *Pendiente de Envío -> En Ruta -> Entregado* en tiempo real con botón directo a Google Maps.
  * Asignación de pedidos a choferes específicos.
  * Generación de "Hoja de Ruta" (imprimible o digital) para organizar las entregas del día.