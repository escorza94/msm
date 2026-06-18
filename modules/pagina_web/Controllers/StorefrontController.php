<?php

class StorefrontController extends Controller {
    
    // Desactivamos el layout principal para esta vista pública.
    protected $layout = null;

    public function index() {
        $this->renderPage('inicio');
    }

    public function pagina() {
        $slug = sanitize($_GET['slug'] ?? 'inicio');
        $this->renderPage($slug);
    }

    private function renderPage($slug) {
        $db = Database::getInstance();
        
        // Cargar la API Key de Google Maps desde el config.php global
        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        $google_maps_api_key = $sysConfig['GOOGLE_MAPS_API_KEY'] ?? '';
        $business_logo = $sysConfig['BUSINESS_LOGO'] ?? '';

        $data = [
            'titulo' => 'Mueblería San Martín',
            'promocion_activa' => null,
            'config' => [],
            'sucursal_coords' => ['lat' => '0', 'lon' => '0'],
            'google_maps_api_key' => $google_maps_api_key,
            'business_logo' => $business_logo,
            'secciones' => [],
            'menu_enlaces' => []
        ];

        // 1. Configuración general del tema (Key-Value)
        try {
            $config_rows = $db->query("SELECT clave, valor FROM tienda_tema_config")->fetchAll();
            foreach ($config_rows as $row) {
                $data['config'][$row['clave']] = $row['valor'];
            }
            
            // Decodificar menú
            $data['menu_enlaces'] = json_decode($data['config']['menu_enlaces'] ?? '[]', true) ?: [
                ['titulo' => 'Productos', 'enlace' => '#productos'],
                ['titulo' => 'Ubicación', 'enlace' => '#ubicacion'],
                ['titulo' => 'Contacto', 'enlace' => '#contacto']
            ];
        } catch (\PDOException $e) {}
            
        // 2. Información de la página actual
        try {
            $pagina_db = $db->query("SELECT * FROM tienda_paginas WHERE slug = '$slug' AND estado = 'publicado'")->fetch();
            
            if (!$pagina_db) {
                // Intentar buscar la página dinámica 404 creada desde el Panel
                $pagina_db = $db->query("SELECT * FROM tienda_paginas WHERE slug = '404' AND estado = 'publicado'")->fetch();
                http_response_code(404); // Importante para el SEO de Google
                
                if (!$pagina_db) {
                    die("<div style='font-family:sans-serif; text-align:center; padding:100px; background:#f9fafb; min-height:100vh;'><h2 style='font-size:40px; color:#4f46e5; margin-bottom:10px;'>Error 404</h2><p style='color:#6b7280; font-size:18px;'>La página que buscas no existe.</p><br><p style='color:#9ca3af; font-size:14px;'><i>Tip para Admin: Ve a 'Panel de Tienda > Páginas', crea una nueva con el slug <b>404</b> y diséñala en el Constructor Visual.</i></p></div>");
                }
            }
            $nombre_empresa = $data['config']['nombre_empresa'] ?? 'Mueblería San Martín';
            $data['titulo'] = htmlspecialchars($pagina_db['titulo']) . ' | ' . htmlspecialchars($nombre_empresa);
        } catch (\PDOException $e) {
            die("<div style='font-family:sans-serif; text-align:center; padding:100px;'><h2 style='font-size:40px; color:#555;'>Error de Base de Datos</h2><p>Falta ejecutar las migraciones de páginas.</p></div>");
        }

        // 3. Promoción activa (cintillo superior)
        try {
            $hoy = date('Y-m-d');
            $data['promocion_activa'] = $db->query("
                SELECT * FROM promociones 
                WHERE estado = 'activo' 
                AND (fecha_inicio IS NULL OR fecha_inicio <= '$hoy')
                AND (fecha_fin IS NULL OR fecha_fin >= '$hoy')
                AND codigo_cupon IS NOT NULL AND codigo_cupon != ''
                ORDER BY id DESC LIMIT 1
            ")->fetch();
        } catch (\PDOException $e) {}

        // 4. Cargar las secciones dinámicas para la página actual (CMS Builder)
        try {
            $secciones_raw = $db->query("
                SELECT s.* 
                FROM tienda_secciones s
                JOIN tienda_pagina_secciones tps ON s.id = tps.seccion_id
                WHERE tps.pagina_id = {$pagina_db['id']} AND s.estado = 'activo'
                ORDER BY tps.orden ASC
            ")->fetchAll(PDO::FETCH_ASSOC);

            foreach ($secciones_raw as $sec) {
                $sec['config'] = json_decode($sec['configuracion'], true) ?: [];
                $sec['global_config'] = $data['config']; // <-- INYECTAMOS LA CONFIGURACIÓN GLOBAL
                
                // --- Lógica de carga de datos para secciones modulares ---
                $view_path = MODULES_PATH . "/pagina_web/BuilderSections/{$sec['tipo']}/view.php";
                if (file_exists($view_path)) {
                    // Si es un grid de productos, cargar los productos de la colección indicada en su JSON
                    if ($sec['tipo'] === 'grid_productos' && !empty($sec['config']['coleccion_slug'])) {
                        $col_slug = preg_replace('/[^a-zA-Z0-9-_]/', '', $sec['config']['coleccion_slug']);
                        $limite = max(1, intval($sec['config']['limite_mostrar'] ?? 8));
                        try {
                            $sec['productos'] = $db->query("
                                SELECT i.id, i.nombre, i.precio,
                                       (SELECT ruta FROM inventario_imagenes img WHERE img.producto_id = i.id ORDER BY es_principal DESC, img.id ASC LIMIT 1) as imagen
                                FROM inventario i
                                JOIN tienda_coleccion_productos tcp ON i.id = tcp.producto_id
                                JOIN tienda_colecciones tc ON tcp.coleccion_id = tc.id
                                WHERE i.estado = 'activo' AND i.stock > 0
                                AND tc.slug = '$col_slug' 
                                ORDER BY i.id DESC
                                LIMIT $limite
                            ")->fetchAll(PDO::FETCH_ASSOC);
                        } catch (\PDOException $e) {
                            $sec['productos'] = [];
                            // Para depuración, podemos inyectar el error en la sección
                            $sec['error'] = $e->getMessage();
                        }
                    }
                    // Aquí podrías añadir más `if` para otros tipos de secciones que necesiten cargar datos (ej. grid_promociones)

                    $sec['view_path'] = $view_path; // Guardamos la ruta de la vista para usarla en el template
                }
                
                if ($sec['tipo'] === 'grid_promociones' && !empty($sec['config']['coleccion_slug'])) {
                    $col_slug = preg_replace('/[^a-zA-Z0-9-_]/', '', $sec['config']['coleccion_slug']);
                    $limite = max(1, intval($sec['config']['limite_mostrar'] ?? 6));
                    $hoy = date('Y-m-d');
                    try {
                        $promos = $db->query("
                            SELECT p.* FROM promociones p
                            JOIN tienda_coleccion_promociones tcp ON p.id = tcp.promocion_id
                            JOIN tienda_colecciones tc ON tcp.coleccion_id = tc.id
                            WHERE p.estado = 'activo' AND tc.slug = '$col_slug' 
                            AND (p.fecha_inicio IS NULL OR p.fecha_inicio <= '$hoy')
                            AND (p.fecha_fin IS NULL OR p.fecha_fin >= '$hoy')
                            ORDER BY p.id DESC LIMIT $limite
                        ")->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Extraer imágenes y nombres de los productos incluidos en la promoción
                        foreach ($promos as &$promo) {
                            $promo['productos_incluidos'] = [];
                            if (!empty($promo['productos_requeridos'])) {
                                $req_ids = json_decode($promo['productos_requeridos'], true);
                                if (is_array($req_ids) && count($req_ids) > 0) {
                                    $placeholders = implode(',', array_fill(0, count($req_ids), '?'));
                                    $stmtReq = $db->prepare("
                                        SELECT id, nombre, 
                                            (SELECT ruta FROM inventario_imagenes img WHERE img.producto_id = inventario.id ORDER BY es_principal DESC, img.id ASC LIMIT 1) as imagen 
                                        FROM inventario 
                                        WHERE id IN ($placeholders)
                                    ");
                                    $stmtReq->execute($req_ids);
                                    $promo['productos_incluidos'] = $stmtReq->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                        }
                        unset($promo);
                        $sec['promociones'] = $promos;
                    } catch (\PDOException $e) { $sec['promociones'] = []; }
                }
                $data['secciones'][] = $sec;
            }
        } catch (\PDOException $e) {}

        // 5. Coordenadas de la sucursal (desde Logística)
        try {
            $config_logistica = $db->query("SELECT clave, valor FROM logistica_configuracion WHERE clave IN ('latitud_sucursal', 'longitud_sucursal')")->fetchAll();
            foreach ($config_logistica as $row) {
                if ($row['clave'] === 'latitud_sucursal') $data['sucursal_coords']['lat'] = $row['valor'];
                if ($row['clave'] === 'longitud_sucursal') $data['sucursal_coords']['lon'] = $row['valor'];
            }

        } catch (\PDOException $e) {}

        // NOTA: Se carga la vista directamente para evitar un posible error del framework 
        // con las rutas de vistas que están en subdirectorios.
        // Esto es un bypass temporal al método $this->render().
        extract($data);
        
        $viewPath = MODULES_PATH . '/pagina_web/Views/tema_default/templates/index.php';
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Error 500: Vista no encontrada (verificación manual) en $viewPath");
        }
    }

    public function verProducto($slug) {
        $db = Database::getInstance();
        $id = intval($slug); // El 'slug' de la URL es en realidad el ID del producto.

        // 1. Cargar configuración global (colores, logo, etc.)
        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        $data = [
            'google_maps_api_key' => $sysConfig['GOOGLE_MAPS_API_KEY'] ?? '',
            'business_logo' => $sysConfig['BUSINESS_LOGO'] ?? '',
            'config' => [],
            'menu_enlaces' => []
        ];
        try {
            $config_rows = $db->query("SELECT clave, valor FROM tienda_tema_config")->fetchAll();
            foreach ($config_rows as $row) { $data['config'][$row['clave']] = $row['valor']; }
            $data['menu_enlaces'] = json_decode($data['config']['menu_enlaces'] ?? '[]', true);
        } catch (\PDOException $e) {}

        // 2. Buscar el producto por su ID
        $stmt = $db->prepare("SELECT i.*, c.nombre as categoria_nombre FROM inventario i LEFT JOIN inventario_categorias c ON i.categoria_id = c.id WHERE i.id = ? AND i.estado = 'activo'");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();

        if (!$producto) {
            http_response_code(404);
            die("<div style='font-family:sans-serif; text-align:center; padding:100px;'><h2 style='font-size:40px; color:#4f46e5;'>Error 404</h2><p>Producto no encontrado o no disponible.</p></div>");
        }

        // 3. Cargar galería de imágenes del producto
        $stmtImg = $db->prepare("SELECT * FROM inventario_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, id ASC");
        $stmtImg->execute([$producto['id']]);
        $imagenes = $stmtImg->fetchAll();

        // 4. Cargar productos relacionados (misma categoría, excluyendo el actual)
        $relacionados = $db->query("
            SELECT i.id, i.nombre, i.precio,
                   (SELECT ruta FROM inventario_imagenes img WHERE img.producto_id = i.id ORDER BY es_principal DESC, img.id ASC LIMIT 1) as imagen
            FROM inventario i
            WHERE i.categoria_id = {$producto['categoria_id']} AND i.id != {$producto['id']} AND i.estado = 'activo'
            ORDER BY RAND() LIMIT 4
        ")->fetchAll();

        $data['titulo'] = htmlspecialchars($producto['nombre']) . ' | ' . htmlspecialchars($data['config']['nombre_empresa'] ?? 'Tienda');
        $data['producto'] = $producto;
        $data['imagenes'] = $imagenes;
        $data['relacionados'] = $relacionados;

        // 5. Renderizar la nueva vista de producto
        extract($data);
        $viewPath = MODULES_PATH . '/pagina_web/Views/tema_default/templates/producto.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Error 500: La plantilla de producto no fue encontrada.");
        }
    }
}