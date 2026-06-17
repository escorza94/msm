# 🛒 Arquitectura: Módulo Tienda Online (Página Web Front-End)

Este documento define la arquitectura estructurada para el escaparate público de Mueblería San Martín.
A diferencia de una simple "landing page", se utiliza una estructura robusta de E-commerce (`tienda_*`) que se integra de forma nativa con el Inventario, Logística, Promociones y CRM del ERP.

---

## 🗄️ 1. Estructura de Base de Datos Base (`tienda_*`)

Para mantener la escalabilidad y preparar el terreno para un carrito de compras real en el futuro, utilizamos el motor de colecciones y configuraciones del tema:

### `tienda_tema_config` (Ajustes y Apariencia)
Tabla clave-valor (Key-Value) para gestionar la configuración global del sitio sin tocar código.
* **Ejemplos de Claves:** `seo_titulo`, `seo_descripcion`, `whatsapp_numero`, `facebook_url`, `footer_texto`.

### `tienda_colecciones` (Agrupaciones Dinámicas)
Reemplaza la idea limitante de "solo productos destacados". Permite crear infinitos grupos de productos.
* **Campos clave:** `id`, `nombre`, `slug` (ej. `destacados`, `salas-vintage`), `descripcion`, `estado`.
* *Nota:* La página de inicio busca automáticamente la colección con el slug `destacados`.

### `tienda_coleccion_productos` (Pivote)
Tabla relacional que conecta el inventario general con las colecciones.
* **Campos clave:** `coleccion_id`, `producto_id`, `orden`.
* *Beneficio:* Un mismo producto de tu `inventario` puede pertenecer a múltiples colecciones a la vez.

### `tienda_paginas` (Estructura Base del CMS)
Define las URLs y títulos de las páginas informativas del sitio (ej. `/inicio`, `/nosotros`, `/terminos-y-condiciones`).
* **Campos clave:** `id`, `titulo`, `slug`, `estado`.

### `tienda_secciones` (Bloques Reutilizables - Widgets) 🧩 NUEVO
Son los "ladrillos" visuales con los que armarás las páginas. Permite estandarizar el diseño sin escribir HTML.
* **Campos clave:** `id`, `nombre_interno`, `tipo` (ej. `carrusel_banners`, `tarjetas_info`, `grid_productos`, `texto_imagen`), `configuracion` (JSON con las imágenes, textos, links o IDs de colecciones a mostrar), `estado`.
* *Beneficio:* Un administrador puede crear un bloque llamado "Banner Buen Fin" (tipo: imagen_full) y tenerlo listo para usar.

### `tienda_pagina_secciones` (Layout Builder - Constructor) 🏗️ NUEVO
Tabla puente que define qué "bloques" aparecen en qué "página" y en qué orden.
* **Campos clave:** `pagina_id`, `seccion_id`, `orden`.
* *Flujo de trabajo:* Entras a la página "Inicio", le añades la sección "Carrusel Principal" (orden 1), luego la sección "Tarjetas de Beneficios" (orden 2), y finalmente la sección "Productos Destacados" (orden 3). Puedes reciclar secciones en diferentes páginas.

---

## 🔌 2. Integraciones Activas con Otros Módulos del ERP

El controlador público (`StorefrontController`) es el pegamento que une la página web con la operación interna del negocio:

* 📦 **Inventario:** Los productos exhibidos extraen su precio y disponibilidad (stock) en **tiempo real**. Si un producto se agota en el POS físico o cambia de precio, se refleja instantáneamente en la web.
* 🎁 **Promociones:** El cintillo superior lee la tabla `promociones` y muestra automáticamente el mejor cupón activo para incentivar la compra a los visitantes.
* 🚚 **Logística:** La sección "Visítanos" consume las coordenadas (`latitud_sucursal` y `longitud_sucursal`) de los ajustes de logística, renderizando un mapa de Google Maps exacto.
* 💬 **WhatsApp Gateway:** Todos los botones de contacto y "Ver en WhatsApp" redirigen al usuario hacia el número oficial, inyectando un mensaje pre-armado con el nombre del producto, lo que facilita el trabajo del Asistente IA (Bot) o de los vendedores.

---

## 🛠️ 3. Panel de Administración (Backend de la Tienda)

Dentro del ERP, el módulo `pagina_web` gestiona la cara comercial mediante las siguientes interfaces:

1. **Gestor de Colecciones:** Interfaz para crear grupos y asignarles productos del inventario actual. *(Implementado)*.
2. **Apariencia Global (Configuración del Tema):** Formulario maestro para modificar textos del Footer, enlaces sociales y números de contacto general.
3. **Gestor de Páginas y Secciones (Constructor Visual):** Una interfaz donde administras los bloques (Subir imágenes a carruseles, llenar tarjetas) y los ordenas arrastrándolos (Drag & Drop) dentro de tus páginas.

*Esta arquitectura garantiza un escaparate rápido, fácil de mantener por los administradores y listo para escalar a ventas con MercadoPago en línea en una fase posterior.*