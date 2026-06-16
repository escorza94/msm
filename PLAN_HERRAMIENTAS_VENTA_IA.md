# 🛠️ Plan de Herramientas IA para Cierre de Ventas Automático

Este documento define la secuencia exacta de herramientas (`Function Calling`) que Google Gemini necesita tener a su disposición para poder llevar a un cliente desde la consulta inicial hasta el pago y generación del pedido, de forma 100% autónoma por WhatsApp.

---

## 📦 FASE 1: Exploración y Oferta (Ya implementadas parcialmente)
El objetivo es enamorar al cliente y darle precios reales.

1. **`buscar_producto_catalogo(query)`** [Módulo POS]
   - **Propósito:** Buscar si un producto existe y su precio.
   - **Estado:** ✅ Implementada.

2. **`enviar_imagen_producto(query)`** [Módulo POS]
   - **Propósito:** Enviar la foto real del producto al WhatsApp del cliente.
   - **Estado:** ✅ Implementada.

3. **`promociones_validar_cupon(codigo, precio_normal)`** [Módulo Promociones]
   - **Propósito:** Aplicar descuentos y mostrar el precio final de oferta.
   - **Estado:** ✅ Implementada.

---

## 🚚 FASE 2: Logística y Ubicación (Próximas a desarrollar)
El objetivo es saber a dónde se enviará y cuánto cuesta el flete.

4. **`explicar_como_enviar_ubicacion()`** [Módulo WhatsApp]
   - **Propósito:** Enseñar al cliente cómo usar la función "Adjuntar > Ubicación" nativa de WhatsApp.
   - *Nota técnica:* El Webhook de Node.js interceptará el mensaje tipo `location` y lo inyectará a la IA como texto `[Ubicación recibida: lat, lon]`.

5. **`cotizar_envio_coordenadas(latitud, longitud)`** [Módulo Logística]
   - **Propósito:** Tomar las coordenadas que mandó el cliente, calcular la distancia (Haversine o Google Maps API) contra la sucursal y aplicar las tarifas activas para devolver el costo exacto de envío.

---

## 👥 FASE 3: Registro y Toma de Datos
El objetivo es capturar al "Lead" en la base de datos antes de cobrarle.

6. **`registrar_cliente_whatsapp(nombre, telefono, latitud, longitud, direccion_texto)`** [Módulo CRM / Clientes]
   - **Propósito:** Guardar el expediente del cliente en MySQL para vincularlo a sus futuras compras e historial. Devuelve el `cliente_id`.

---

## 💳 FASE 4: Generación de Pedido y Cobro
El objetivo es formalizar la nota y obtener el dinero.

7. **`crear_nota_venta_pos(cliente_id, carrito[], costo_envio)`** [Módulo POS]
   - **Propósito:** Descuenta el stock del inventario, genera el registro en la tabla `ventas` con estado "Pendiente de Pago" y genera una orden en `logistica_envios`.
   - **Retorna:** Un número de Folio de venta `#1045`.

8. **`generar_link_pago_mercadopago(folio_venta)`** [Módulo Finanzas / API MercadoPago]
   - **Propósito:** Toma el total del folio y genera un "Preference ID" (Enlace de cobro) válido.
   - **Acción final:** La IA le responde al cliente: *"¡Todo listo! Tu pedido es el #1045. Puedes pagar seguro en este enlace: [LINK]. En cuanto pagues, te lo enviamos."*

---

## 🔄 Resumen del Flujo de la IA
`buscar_producto` -> `enviar_imagen` -> `validar_promocion` -> *Cliente acepta* -> `cotizar_envio_coordenadas` (previa recepción de GPS) -> `registrar_cliente` -> `crear_nota_venta` -> `generar_link_pago`.