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

        $data = [
            'titulo' => 'Mueblería San Martín',
            'promocion_activa' => null,
            'config' => [],
            'sucursal_coords' => ['lat' => '0', 'lon' => '0'],
            'google_maps_api_key' => $google_maps_api_key,
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
                die("<div style='font-family:sans-serif; text-align:center; padding:100px;'><h2 style='font-size:40px; color:#555;'>Error 404</h2><p>La página que buscas no existe o se encuentra desactivada.</p></div>");
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
}