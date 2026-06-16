<?php

class LogisticaController extends Controller {
    
    public function index() {
        auth_require();
        require_permission('logistica.ver');
        $this->render('logistica', 'index', [
            'titulo' => 'Panel Principal de Logística'
        ]);
    }

    public function configuracion() {
        auth_require();
        require_permission('logistica.editar');
        
        $db = Database::getInstance();
        
        // Obtener tarifas
        try {
            $tarifas = $db->query("SELECT * FROM logistica_tarifas ORDER BY id ASC")->fetchAll();
            
            // Obtener configuración de sucursal
            $config_rows = $db->query("SELECT * FROM logistica_configuracion")->fetchAll();
            $config = [];
            foreach ($config_rows as $row) {
                $config[$row['clave']] = $row['valor'];
            }
        } catch (\PDOException $e) {
            $tarifas = [];
            $config = [];
            // Si la tabla no existe, no rompemos la vista.
        }
        
        $this->render('logistica', 'configuracion', [
            'titulo' => 'Configuración de Logística',
            'tarifas' => $tarifas,
            'config' => $config
        ]);
    }
    
    public function entregas() {
        auth_require();
        require_permission('logistica.ver');
        
        $db = Database::getInstance();
        
        // Obtener envíos
        try {
            $envios = $db->query("
                SELECT e.*, 
                       v.id as folio_venta, v.total, (v.total - v.monto_recibido) as restante,
                       c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                       u.name as chofer_nombre
                FROM logistica_envios e
                JOIN ventas v ON e.venta_id = v.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN users u ON e.chofer_id = u.id
                ORDER BY e.fecha_creacion DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            $envios = [];
        }

        $this->render('logistica', 'entregas', [
            'titulo' => 'Tablero de Entregas',
            'envios' => $envios
        ]);
    }

    public function historial() {
        auth_require();
        require_permission('logistica.ver');
        
        $db = Database::getInstance();
        
        try {
            $envios = $db->query("
                SELECT e.*, 
                       v.id as folio_venta, v.total, (v.total - v.monto_recibido) as restante,
                       c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                       u.name as chofer_nombre
                FROM logistica_envios e
                JOIN ventas v ON e.venta_id = v.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN users u ON e.chofer_id = u.id
                ORDER BY e.fecha_creacion DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            $envios = [];
        }

        $this->render('logistica', 'historial', [
            'titulo' => 'Historial de Entregas',
            'envios' => $envios
        ]);
    }

    public function guardarConfiguracion() {
        auth_require();
        require_permission('logistica.editar');
        $db = Database::getInstance();
        $lat = sanitize($_POST['latitud'] ?? '0');
        $lon = sanitize($_POST['longitud'] ?? '0');
        
        $stmt = $db->prepare("INSERT INTO logistica_configuracion (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
        $stmt->execute(['latitud_sucursal', $lat]);
        $stmt->execute(['longitud_sucursal', $lon]);
        
        redirect(base_url('logistica?success=Configuración de sucursal guardada'));
    }

    public function guardarTarifa() {
        auth_require();
        require_permission('logistica.editar');
        $db = Database::getInstance();
        
        $id = intval($_POST['id'] ?? 0);
        $nombre = sanitize($_POST['nombre'] ?? '');
        $tipo = in_array($_POST['tipo'] ?? '', ['fija', 'distancia']) ? $_POST['tipo'] : 'fija';
        $precio_base = floatval($_POST['precio_base'] ?? 0);
        $km_base = floatval($_POST['km_base'] ?? 0);
        $precio_km_extra = floatval($_POST['precio_km_extra'] ?? 0);
        
        if (empty($nombre)) redirect(base_url('logistica?error=El nombre de la tarifa es obligatorio'));
        
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE logistica_tarifas SET nombre=?, tipo=?, precio_base=?, km_base=?, precio_km_extra=? WHERE id=?");
            $stmt->execute([$nombre, $tipo, $precio_base, $km_base, $precio_km_extra, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO logistica_tarifas (nombre, tipo, precio_base, km_base, precio_km_extra) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $tipo, $precio_base, $km_base, $precio_km_extra]);
        }
        redirect(base_url('logistica?success=Tarifa guardada correctamente'));
    }

    public function eliminarTarifa() {
        auth_require();
        require_permission('logistica.editar');
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            Database::getInstance()->prepare("DELETE FROM logistica_tarifas WHERE id=?")->execute([$id]);
        }
        redirect(base_url('logistica?success=Tarifa eliminada'));
    }

    public function actualizarEstadoEnvio() {
        auth_require();
        require_permission('logistica.editar');
        $id = intval($_POST['id'] ?? 0);
        $estado = sanitize($_POST['estado'] ?? '');
        
        if ($id && in_array($estado, ['pendiente', 'en_ruta', 'entregado', 'cancelado'])) {
            $db = Database::getInstance();
            
            if ($estado === 'entregado') { 
                $db->prepare("UPDATE logistica_envios SET estado = ?, fecha_entrega = NOW() WHERE id = ?")->execute([$estado, $id]); 
                $envio = $db->query("SELECT venta_id FROM logistica_envios WHERE id = $id")->fetch();
                if($envio) $db->prepare("UPDATE ventas SET estado_entrega = 'entregado' WHERE id = ?")->execute([$envio['venta_id']]);
            } else { 
                $db->prepare("UPDATE logistica_envios SET estado = ?, fecha_entrega = NULL WHERE id = ?")->execute([$estado, $id]); 
                $envio = $db->query("SELECT venta_id FROM logistica_envios WHERE id = $id")->fetch();
                if($envio) $db->prepare("UPDATE ventas SET estado_entrega = 'pendiente' WHERE id = ?")->execute([$envio['venta_id']]);
            }
            
            json_response(['status' => 'success']);
        }
        json_response(['error' => 'Datos inválidos'], 400);
    }

    // --- HOOK PARA EL PANEL DERECHO DE WHATSAPP ---
    public function hookWhatsAppPanel($whatsapp_id) {
        $db = Database::getInstance();
        $html = '';
        try {
            $stmt = $db->prepare("SELECT id FROM clientes WHERE whatsapp_id = ? LIMIT 1");
            $stmt->execute([$whatsapp_id]);
            if ($cliente = $stmt->fetch()) {
                $stmtEnvio = $db->prepare("SELECT id FROM logistica_envios e JOIN ventas v ON e.venta_id = v.id WHERE v.cliente_id = ? AND e.estado IN ('pendiente', 'en_ruta') LIMIT 1");
                $stmtEnvio->execute([$cliente['id']]);
                if ($stmtEnvio->fetch()) {
                    $html .= '<div class="space-y-2 mb-4"><a href="' . base_url('logistica/entregas') . '" target="_blank" class="block w-full p-2.5 bg-white hover:bg-purple-50 text-gray-700 hover:text-purple-700 text-sm font-bold rounded-lg transition border border-gray-100 shadow-sm flex items-center"><div class="w-7 h-7 rounded bg-purple-100 text-purple-600 flex items-center justify-center mr-3"><i class="fas fa-truck-fast text-xs"></i></div> Rastreo de Envío Activo</a></div>';
                }
            }
        } catch (\Exception $e) {}
        return ['order' => 30, 'html' => $html]; // Order 30 = Debajo de POS
    }

    // --- GENERADOR DIRECTO DE RUTA (BOTÓN PARA VISTA HTML) ---
    public function rutaGoogleMaps() {
        auth_require();
        require_permission('logistica.ver');
        
        $db = Database::getInstance();
        $envios = $db->query("SELECT coordenadas_destino FROM logistica_envios WHERE estado IN ('pendiente', 'en_ruta') AND coordenadas_destino IS NOT NULL AND coordenadas_destino != ''")->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($envios)) redirect(base_url('logistica/entregas?error=No hay envíos pendientes con coordenadas válidas'));
        
        $config_rows = $db->query("SELECT clave, valor FROM logistica_configuracion")->fetchAll();
        $config = []; foreach ($config_rows as $r) $config[$r['clave']] = $r['valor'];
        
        $lat_origen = $config['latitud_sucursal'] ?? '';
        $lon_origen = $config['longitud_sucursal'] ?? '';
        
        if (empty($lat_origen) || empty($lon_origen) || $lat_origen === '0' || $lat_origen === '0.0000000') {
            redirect(base_url('logistica/entregas?error=Configura las coordenadas de la sucursal (Origen) en Ajustes de Logística'));
        }
        
        $origen = $lat_origen . ',' . $lon_origen;
        $destino = array_pop($envios)['coordenadas_destino']; // Toma el último de la lista como destino final
        
        $waypoints = [];
        foreach ($envios as $e) { $waypoints[] = $e['coordenadas_destino']; }
        
        $url = "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($origen) . "&destination=" . urlencode($destino);
        if (!empty($waypoints)) { $url .= "&waypoints=" . urlencode(implode('|', $waypoints)); }
        
        redirect($url);
    }

    // --- VISUALIZADOR DE MAPA INTERACTIVO (LEAFLET) ---
    public function mapaRutas() {
        auth_require();
        require_permission('logistica.ver');
        
        $db = Database::getInstance();
        $envios = $db->query("
            SELECT e.*, v.id as folio_venta, c.nombre as cliente_nombre, c.telefono as cliente_telefono
            FROM logistica_envios e
            JOIN ventas v ON e.venta_id = v.id
            LEFT JOIN clientes c ON v.cliente_id = c.id
            WHERE e.estado IN ('pendiente', 'en_ruta') 
            AND e.coordenadas_destino IS NOT NULL 
            AND e.coordenadas_destino != ''
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $config_rows = $db->query("SELECT clave, valor FROM logistica_configuracion")->fetchAll();
        $config = []; foreach ($config_rows as $r) $config[$r['clave']] = $r['valor'];
        
        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        $google_maps_key = $sysConfig['GOOGLE_MAPS_API_KEY'] ?? '';

        $this->render('logistica', 'mapa_rutas', [
            'titulo' => 'Mapa Interactivo de Entregas',
            'envios' => $envios,
            'lat_origen' => $config['latitud_sucursal'] ?? '0',
            'lon_origen' => $config['longitud_sucursal'] ?? '0',
            'google_maps_key' => $google_maps_key
        ]);
    }

    // --- HOOKS PARA EL COPILOTO DE IA (HERRAMIENTAS) ---
    public function hookIaCopilotTools() {
        return [
            ['declaration' => ['name' => 'logistica_envios_pendientes', 'description' => 'Consulta la lista de envíos o entregas que están pendientes o en ruta y que aún no han sido entregados.', 'parameters' => ['type' => 'OBJECT', 'properties' => ['dummy' => ['type' => 'STRING', 'description' => 'Parámetro vacío.']]]]],
            ['declaration' => ['name' => 'logistica_generar_ruta_optima', 'description' => 'Genera un enlace mágico de Google Maps con la ruta óptima para entregar todos los pedidos pendientes del día ahorrando gasolina.', 'parameters' => ['type' => 'OBJECT', 'properties' => ['dummy' => ['type' => 'STRING', 'description' => 'Parámetro vacío.']]]]]
        ];
    }

    public function executeIaCopilotTool($name, $args) {
        $db = Database::getInstance();
        if ($name === 'logistica_envios_pendientes') {
            $envios = $db->query("SELECT e.id as envio_id, e.estado, e.direccion_destino, c.nombre as cliente, c.telefono FROM logistica_envios e JOIN ventas v ON e.venta_id = v.id LEFT JOIN clientes c ON v.cliente_id = c.id WHERE e.estado IN ('pendiente', 'en_ruta')")->fetchAll(PDO::FETCH_ASSOC);
            return empty($envios) ? ['mensaje' => 'No hay envíos pendientes, todo está entregado.'] : ['envios_pendientes' => $envios];
        }
        if ($name === 'logistica_generar_ruta_optima') {
            $envios = $db->query("SELECT coordenadas_destino FROM logistica_envios WHERE estado IN ('pendiente', 'en_ruta') AND coordenadas_destino IS NOT NULL AND coordenadas_destino != ''")->fetchAll(PDO::FETCH_ASSOC);
            if (empty($envios)) return ['error' => 'No hay envíos pendientes con coordenadas en el sistema.'];
            
            $config_rows = $db->query("SELECT clave, valor FROM logistica_configuracion")->fetchAll();
            $config = []; foreach ($config_rows as $r) $config[$r['clave']] = $r['valor'];
            $lat_origen = $config['latitud_sucursal'] ?? ''; $lon_origen = $config['longitud_sucursal'] ?? '';
            
            if (empty($lat_origen) || empty($lon_origen) || $lat_origen === '0' || $lat_origen === '0.0000000') {
                return ['error' => 'No se han configurado las coordenadas de la sucursal en Logística.'];
            }
            
            $origen = $lat_origen . ',' . $lon_origen;
            $destino = array_pop($envios)['coordenadas_destino'];
            $waypoints = []; foreach ($envios as $e) { $waypoints[] = $e['coordenadas_destino']; }
            
            $url = "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($origen) . "&destination=" . urlencode($destino);
            if (!empty($waypoints)) { $url .= "&waypoints=" . urlencode(implode('|', $waypoints)); }
            
            return ['mensaje' => 'Genera este enlace al usuario con formato Markdown. Dile que al dar clic se abrirá la app de Maps en su celular ya con la ruta ordenada: ', 'link_google_maps' => $url];
        }
        return ['error' => 'Herramienta no soportada por Logística'];
    }
}