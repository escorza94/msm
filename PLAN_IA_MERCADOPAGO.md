# 🤖 Módulo IA (Agente de Ventas) + 💳 MercadoPago

Este documento describe la arquitectura para dotar al ERP "Mueblería San Martín" de un agente conversacional inteligente y pasarelas de pago digitales automáticas.

---

## 🧠 1. Arquitectura del Agente IA (Local RAG + Gemini)

El objetivo no es entrenar un modelo desde cero, sino usar **Google Gemini** inyectándole contexto de tu base de datos local en tiempo real (**RAG**).

### 1.1 Inyección de Contexto Local
Cada vez que la IA deba responder, el sistema armará un "System Prompt" dinámico que contendrá:
*   **Catálogo Actualizado:** Los 50 productos más relevantes o aquellos buscados en la conversación (Nombre, SKU, Precio, Stock real).
*   **Promociones Activas:** Cupones y reglas de descuento disponibles.
*   **Reglas del Negocio:** Tiempos de entrega, políticas de garantía y saludo oficial.
*   **Base de Conocimiento (Módulo Help):** Artículos y categorías (FAQ, políticas, guías de cuidados) consultados dinámicamente para preguntas abiertas.

### 1.2 Function Calling (Herramientas de la IA)
Se le darán "Herramientas" a Gemini para que pueda ejecutar acciones en el sistema PHP:
1.  `consultar_inventario(query)`: Permite a la IA buscar si hay "Sillas de madera" en la base de datos MySQL.
2.  `cotizar_productos(items[])`: Genera un total incluyendo envíos (calculado por coordenadas).
3.  `crear_venta(cliente_telefono, items[], direccion)`: Llama internamente al modelo de Ventas (POS) para separar el stock y dejar la venta como "Pendiente de pago".

---

## 📱 2. Integración en el Gateway de WhatsApp

Aprovechando el `WhatsappController` actual y la conexión con Node.js:

### 2.1 Flujo del Webhook (Mensaje Entrante)
1. Cliente envía mensaje: *"Hola, ¿tienen comedores de 4 sillas?"*
2. `webhookIncoming` en PHP recibe el mensaje.
3. PHP revisa si el modo "Asistente IA" está activo para ese cliente (Toggle en el CRM).
4. Si está activo, PHP agrupa el historial reciente de chat + Contexto RAG -> Envía a API de Gemini.
5. Gemini responde (o ejecuta una función).
6. PHP manda la respuesta de texto a Node.js para que el WhatsApp la entregue.

### 2.2 Traspaso a Humano (Handoff)
Si el cliente pide "hablar con un asesor" o la venta se traba, la IA ejecutará la función `solicitar_humano()`. El sistema enviará una notificación visual en el panel y pausará el bot para ese cliente.

---

## 💳 3. Integración MercadoPago (Links y Webhooks)

El objetivo es cerrar la venta 100% digital sin intervención del cajero.

### 3.1 Generación del Link (Preference)
*   Cuando la IA ejecuta `crear_venta`, el sistema (vía API de MercadoPago) generará una "Preferencia de Pago".
*   La Preferencia devolverá un `init_point` (Link de pago).
*   La IA responderá: *"¡Excelente! Tu pedido está reservado. Puedes pagar seguro aquí: [Link MercadoPago]"*.

### 3.2 Webhook de MercadoPago (IPN - Notificación de Pagos)
*   Se creará un endpoint (ej: `misistema.com/mercadopago/webhook`).
*   Cuando el cliente paga, MercadoPago manda un POST a ese endpoint.
*   El sistema verifica el estado (`approved`).
*   **Automatización:**
    1. Busca la `venta_id` ligada a la preferencia.
    2. Crea un Abono automático en `ventas_abonos`.
    3. Actualiza el estado a `pagado`.
    4. Genera el ingreso en el Libro Mayor (`finanzas_movimientos`) en la cuenta "MercadoPago".
    5. Manda un WhatsApp automático de confirmación: *"¡Hemos recibido tu pago! Tu pedido pasa a Logística."*

---

## 📁 4. Tareas y Nuevos Archivos Requeridos

1.  **`modules/ia/Controllers/IaController.php`**
    *   Módulo para gestionar el System Prompt, las herramientas (Functions) y comunicación cURL con Gemini API.
2.  **`modules/ia/Models/RagService.php`**
    *   Modelo encargado de formatear el catálogo de MySQL en texto compacto para la IA.
3.  **`modules/pagos/Controllers/MercadoPagoController.php`**
    *   Controlador con el SDK/cURL de MercadoPago para crear links y recibir el Webhook IPN.
4.  **Actualizar `wa_contactos` (Tabla)**
    *   Agregar columna `bot_activo` (BOOLEAN DEFAULT 1) para permitir apagado manual.
5.  **Ajustes al Módulo Dashboard/WhatsApp**
    *   Crear botón (Toggle) en la UI del Chat para encender/apagar a la IA.
6.  **Módulo `help` (Centro de Ayuda / Base de Conocimiento)**
    *   Módulo CRUD con las tablas `help_categorias` y `help_articulos`.
    *   Integración directa en el servicio RAG (`RagService.php`) para nutrir a la IA con contexto textual extendido.

---

## 🚀 Próximo Paso Recomendado
Comenzar creando el módulo independiente `ia` y el servicio RAG que le enseñe a Gemini el catálogo antes de conectarlo con WhatsApp.