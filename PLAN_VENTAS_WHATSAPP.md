# 🤖 Arquitectura Modular: Flujo de Ventas en WhatsApp

Este documento describe el diseño técnico para automatizar el cierre de ventas por WhatsApp. El bot guiará al cliente desde la prospección, recepción de ubicación (GPS), cotización de flete y creación del pedido, utilizando una arquitectura 100% modular y desacoplada.

---

## 🧩 1. Distribución de Herramientas (Function Calling)

Cada módulo es dueño absoluto de sus funciones (Tools) y las expone al bot de Inteligencia Artificial mediante el método `hookWaBotTools()`.

### 📞 Módulo WhatsApp (El Director de Comunicación)
* **Traductor de Ubicaciones (Nativo):** El Webhook interceptará los mensajes de tipo `location` y los traducirá a texto plano con coordenadas (`[Latitud, Longitud]`) para que la IA sepa exactamente dónde está el cliente.
* **Herramienta `solicitar_humano`:** Apaga el bot y alerta al vendedor en el CRM.

### 👥 Módulo CRM / Clientes (El Dueño de la Identidad)
* **Herramienta `verificar_perfil_cliente(telefono)`:** Revisa si el número de WhatsApp ya pertenece a un cliente registrado para saludarlo por su nombre.
* **Herramienta `registrar_nuevo_cliente(...)`:** Guarda el nombre, teléfono y ubicación del cliente en la base de datos antes de generarle un pedido.

### 🚚 Módulo Logística (El Dueño del Mapa)
* **Herramienta `cotizar_envio_whatsapp(coordenadas)`:** Calcula la distancia entre la sucursal y la ubicación del cliente, retornando el costo exacto del flete según las tarifas configuradas.

### 🛒 Módulo POS (El Dueño del Inventario)
* **Herramienta `buscar_producto_catalogo(query)`:** Consulta precios y stock en tiempo real.
* **Herramienta `crear_nota_venta_whatsapp(...)`:** La herramienta maestra. Recibe los productos, el costo de envío y el ID del cliente. Descuenta el inventario, manda el pedido a Logística y devuelve un `#Folio`.

### 💳 Módulo Finanzas / Pagos (El Dueño del Dinero)
* **Herramienta `generar_link_pago(folio)`:** Recibe el número de pedido, se conecta con la API de MercadoPago y devuelve la URL (Link de pago) lista para cobrar.

---

## 🧠 2. Contexto Dinámico en la Memoria (RAG)

En lugar de herramientas, usamos el `hookIaRagContext()` para inyectar "sabiduría pasiva" a la IA antes de que empiece a hablar.

* **Módulo Promociones:** Inyecta automáticamente los descuentos y cupones activos de hoy para que la IA los use como gancho de ventas al saludar.
* **Módulo Help (FAQ):** Inyecta políticas de garantía y tiempos de entrega.

---

## 🔄 3. El Flujo de Conversación Perfecto (Paso a Paso)

1. **Atracción:** El cliente saluda. El bot responde, es amigable y le ofrece la *Promoción del Día* (Contexto RAG).
2. **Búsqueda:** El cliente pide un colchón. El bot ejecuta `buscar_producto_catalogo` (POS) y le da opciones con precios reales.
3. **Cotización:** El cliente acepta. El bot le pide su **Ubicación de WhatsApp 📍**. El Webhook la recibe, la traduce y el bot ejecuta `cotizar_envio_whatsapp` (Logística) para darle el costo total (Muebles + Envío).
4. **Captura:** El cliente confirma. El bot le pide su nombre y ejecuta `registrar_nuevo_cliente` (CRM).
5. **Cierre:** El bot ejecuta `crear_nota_venta_whatsapp` (POS) para formalizar la venta en el sistema.
6. **Cobro:** Finalmente, el bot ejecuta `generar_link_pago` (Finanzas), le entrega el link de MercadoPago al cliente y se despide (o pasa a un humano si el cliente prefiere transferencia).

---

*Este documento sirve como hoja de ruta para la construcción del código.*