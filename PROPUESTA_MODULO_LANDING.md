# 🌐 Propuesta: Módulo Landing (Gestor de Página Web)

Tener un módulo `landing` te permitirá controlar lo que ven tus clientes en la página principal (pública) sin tener que tocar el código cada vez que quieras cambiar una oferta o un producto.

Aprovechando la arquitectura y módulos existentes en el ERP (Inventario, POS, Promociones, WhatsApp, Logística), la propuesta abarca los siguientes puntos:

## 1. Gestor de Banners (Carrusel Principal)
* **Subida de Imágenes:** Panel para cargar banners promocionales (ej. "Buen Fin", "Día de las Madres").
* **Orden y Enlaces:** Función para ordenar (arrastrar/soltar) qué banner sale primero y asignarles un enlace (ej. redirigir a un producto específico o directo al bot de WhatsApp con un mensaje predefinido).

## 2. Integración con "Productos Destacados"
* **Catálogo Dinámico:** Sección para buscar productos de tu **Inventario actual** y marcarlos como "Destacados" o "Novedades".
* **Sincronización:** El sistema tomará automáticamente la foto principal, el nombre y el precio real del inventario para exhibirlos. Si el precio cambia en el POS o se agota el stock, se reflejará instantáneamente en la web.

## 3. Integración con Promociones Activas
* **Cintillo Inteligente:** Conexión con el módulo `Promociones` para detectar si hay cupones activos (dentro de su fecha de vigencia).
* **Exhibición Automática:** Mostrará un aviso atractivo en la parte superior del sitio (Ej. *"¡Usa el código OTOÑO24 y obtén 10% de descuento en tu compra!"*).

## 4. Configuración General e Información de Contacto
* **Textos SEO:** Campos para editar el título de la página y la metadescripción para mejorar el posicionamiento en Google.
* **Redes Sociales:** Links dinámicos a Facebook, Instagram, TikTok, etc.
* **Botón Flotante de WhatsApp:** Vinculación con el módulo `WhatsApp Gateway` para conectar a los visitantes web directamente con la inteligencia artificial o los asesores.
* **Ubicación Integrada:** Consumo automático de las coordenadas almacenadas en `Logística` para inyectar un mapa de Google Maps con la ubicación exacta de la sucursal.

## 5. Secciones Ocultables (Toggles)
* **Interruptores de Visibilidad:** Botones de encendido/apagado (On/Off) para ocultar o mostrar secciones enteras de la web al instante (Ej. apagar la sección "Nuestra Historia" o "Testimonios" con un solo clic).

---

## 🗄️ Estructura de Base de Datos Sugerida
Para mantener el sistema ligero y rápido, solo requerimos agregar:

1. `landing_banners`: 
   - Para guardar el orden, la ruta de la imagen y los enlaces del carrusel promocional.

2. `landing_config`: 
   - Tabla tipo clave-valor (Key-Value) para guardar textos globales, links de redes sociales y el estado de visibilidad (toggles) de las secciones.

3. `landing_destacados`: 
   - Para registrar únicamente los IDs de los productos del inventario (`producto_id`) que se exhibirán en el escaparate virtual y su orden de aparición.