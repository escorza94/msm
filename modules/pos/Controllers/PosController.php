<?php

class PosController extends Controller {
    
    public function index() {
        auth_require();
        require_permission('pos.ver');
        
        $db = Database::getInstance();
        
        // Traemos también latitud y longitud si existen en la tabla (asumiendo que CRM Clientes las guarda)
        // NOTA: Asegúrate de que la tabla 'clientes' tenga columnas latitud y longitud, si no las tiene, remuévelas del query
        $clientes = $db->query("SELECT id, nombre, telefono, latitud, longitud FROM clientes ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener productos activos con su imagen principal
        $productos = $db->query("
            SELECT i.id, i.sku, i.nombre, i.precio, i.stock,
                   (SELECT ruta FROM inventario_imagenes img WHERE img.producto_id = i.id ORDER BY es_principal DESC LIMIT 1) as imagen
            FROM inventario i
            WHERE i.estado = 'activo'
            ORDER BY i.nombre ASC
        ")->fetchAll();
        
        // Obtener tarifas de logística
        try {
            $tarifas_envio = $db->query("SELECT * FROM logistica_tarifas WHERE estado = 'activo' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener coordenadas de la sucursal
            $config_rows = $db->query("SELECT clave, valor FROM logistica_configuracion")->fetchAll();
            $config_logistica = [];
            foreach ($config_rows as $row) {
                $config_logistica[$row['clave']] = $row['valor'];
            }
        } catch (\PDOException $e) {
            // Por si aún no ejecutan la migración
            $tarifas_envio = [];
            $config_logistica = [
                'latitud_sucursal' => '0',
                'longitud_sucursal' => '0'
            ];
        }

        // Obtener la API Key de Google Maps desde config.php
        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        $google_maps_key = $sysConfig['GOOGLE_MAPS_API_KEY'] ?? '';

        // Obtener cuentas de finanzas activas para el selector de cobro
        $cuentas_finanzas = [];
        try {
            $cuentas_finanzas = $db->query("SELECT id, nombre, tipo FROM finanzas_cuentas WHERE estado = 'activo' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) { }
        
        // Obtener todas las promociones activas
        $promociones_activas = [];
        try {
            $promociones_activas = $db->query("SELECT * FROM promociones WHERE estado = 'activo' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) { }

        // Separar promociones automáticas (Sin código de cupón)
        $promos_automaticas = array_values(array_filter($promociones_activas, function($p) {
            return empty(trim($p['codigo_cupon']));
        }));

        $this->render('pos', 'pos', [
            'titulo' => 'Punto de Venta (POS)',
            'clientes' => $clientes,
            'productos' => $productos,
            'tarifas_envio' => $tarifas_envio,
            'config_logistica' => $config_logistica,
            'google_maps_key' => $google_maps_key,
            'cuentas_finanzas' => $cuentas_finanzas,
            'promos_automaticas' => $promos_automaticas,
            'promociones_activas' => $promociones_activas,
            'cliente_seleccionado' => intval($_GET['cliente_id'] ?? 0)
        ]);
    }

    public function guardar() {
        auth_require();
        require_permission('pos.crear');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || empty($data['carrito'])) {
            json_response(['error' => 'El carrito está vacío'], 400);
        }

        $cliente_id = !empty($data['cliente_id']) ? intval($data['cliente_id']) : null;
        $tipo = in_array($data['tipo'], ['cotizacion', 'venta']) ? $data['tipo'] : 'cotizacion';
        $subtotal = floatval($data['subtotal'] ?? 0);
        $descuento = floatval($data['descuento'] ?? 0);
        $costo_envio = floatval($data['costo_envio'] ?? 0);
        $total = floatval($data['total'] ?? 0);
        $notas = sanitize($data['notas'] ?? '');
        $metodo_pago = sanitize($data['metodo_pago'] ?? 'Efectivo');
        $cuenta_destino = intval($data['cuenta_id'] ?? 1);
        
        $monto_recibido = floatval($data['monto_recibido'] ?? 0);
        $cambio = 0;
        
        if ($tipo === 'venta') {
            if ($monto_recibido >= $total) {
                $estado_pago = 'pagado';
                $cambio = $monto_recibido - $total;
            } elseif ($monto_recibido > 0) {
                $estado_pago = 'parcial';
            } else {
                $estado_pago = 'pendiente';
            }
        } else {
            $estado_pago = 'pendiente';
            $monto_recibido = 0; // Las cotizaciones no manejan ingreso de dinero
        }
        
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
        
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // 1. Guardar la cabecera (Venta / Cotización)
            $stmt = $db->prepare("INSERT INTO ventas (cliente_id, usuario_id, tipo, subtotal, descuento, costo_envio, total, monto_recibido, cambio, notas_internas, metodo_pago, estado_pago) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $usuario_id, $tipo, $subtotal, $descuento, $costo_envio, $total, $monto_recibido, $cambio, $notas, $metodo_pago, $estado_pago]);
            $venta_id = $db->lastInsertId();
            
            // 2. Guardar el detalle (Carrito)
            $stmtDetalle = $db->prepare("INSERT INTO ventas_detalles (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            
            // Preparamos las consultas para el Kardex e Inventario (Solo si es Venta)
            $stmtActualizarStock = $db->prepare("UPDATE inventario SET stock = stock - ? WHERE id = ?");
            $stmtKardex = $db->prepare("INSERT INTO inventario_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, motivo) VALUES (?, ?, 'salida', ?, ?)");
            
            foreach ($data['carrito'] as $item) {
                $prod_id = intval($item['id']);
                $cantidad = intval($item['cantidad']);
                $precio = floatval($item['precio']);
                $item_subtotal = $cantidad * $precio;
                
                $stmtDetalle->execute([$venta_id, $prod_id, $cantidad, $precio, $item_subtotal]);
                
                // 3. Impactar el inventario si el tipo es 'venta'
                if ($tipo === 'venta') {
                    $stmtActualizarStock->execute([$cantidad, $prod_id]);
                    
                    // Registrar salida en el Kardex
                    $motivo = "Venta #" . $venta_id;
                    $stmtKardex->execute([$prod_id, $usuario_id, $cantidad, $motivo]);
                }
            }
            
            // 4. Crear registro en Logística de Envíos si es venta y requiere envío
            if ($tipo === 'venta') {
                $tarifa_id = !empty($data['tarifa_id']) ? intval($data['tarifa_id']) : null;
                $entrega_personalizada = !empty($data['entrega_personalizada']);
                
                $requiere_envio = false;
                if ($costo_envio > 0 || $entrega_personalizada) {
                    $requiere_envio = true;
                } elseif ($tarifa_id !== null) {
                    // Verificar si la tarifa seleccionada NO es "Recoger en Tienda"
                    $stmtTarifa = $db->prepare("SELECT nombre FROM logistica_tarifas WHERE id = ?");
                    $stmtTarifa->execute([$tarifa_id]);
                    $tarifa = $stmtTarifa->fetch();
                    if ($tarifa && stripos(strtolower($tarifa['nombre']), 'recoger') === false) {
                        $requiere_envio = true;
                    }
                }
                
                if ($requiere_envio) {
                    $coordenadas = null;
                    $direccion_destino = null;
                    if ($entrega_personalizada && !empty($data['lat_destino']) && !empty($data['lon_destino'])) {
                        $coordenadas = sanitize($data['lat_destino']) . ',' . sanitize($data['lon_destino']);
                        $direccion_destino = sanitize($data['direccion_destino'] ?? '');
                    } else if ($cliente_id) {
                        // Si no es personalizada, jalamos las coordenadas del cliente de la BD
                        $stmtCliente = $db->prepare("SELECT latitud, longitud, direccion FROM clientes WHERE id = ?");
                        $stmtCliente->execute([$cliente_id]);
                        $cliente = $stmtCliente->fetch();
                        if ($cliente) {
                            if (!empty($cliente['latitud']) && !empty($cliente['longitud'])) {
                                $coordenadas = $cliente['latitud'] . ',' . $cliente['longitud'];
                            }
                            $direccion_destino = $cliente['direccion'] ?? null;
                        }
                    }
                    
                    $stmtEnvio = $db->prepare("INSERT INTO logistica_envios (venta_id, estado, coordenadas_destino, direccion_destino) VALUES (?, 'pendiente', ?, ?)");
                    $stmtEnvio->execute([$venta_id, $coordenadas, $direccion_destino]);
                }
            }
            
            // 5. Integración con FINANZAS: Registrar el ingreso SOLO si dejó dinero (monto_recibido > 0)
            if ($tipo === 'venta' && $monto_recibido > 0) {
                $categoria_venta = 1; // ID 1 = Venta de Contado
                $concepto = ($estado_pago === 'parcial' ? "Anticipo de Venta folio #" : "Venta folio #") . str_pad($venta_id, 5, '0', STR_PAD_LEFT);
                $ingreso_neto = min($monto_recibido, $total); // No registramos el cambio como ingreso
                
                $stmtFinanzas = $db->prepare("INSERT INTO finanzas_movimientos (cuenta_id, usuario_id, tipo, monto, categoria_id, concepto, metodo_pago, origen_tipo, origen_id) VALUES (?, ?, 'ingreso', ?, ?, ?, ?, 'venta', ?)");
                $stmtFinanzas->execute([$cuenta_destino, $usuario_id, $ingreso_neto, $categoria_venta, $concepto, $metodo_pago, $venta_id]);
            }

            $db->commit();
            
            // --- NOTIFICACIONES (VENTAS A ADMINISTRADORES) ---
            $configNotifPath = MODULES_PATH . '/notificaciones/config.json';
            $configNotif = file_exists($configNotifPath) ? json_decode(file_get_contents($configNotifPath), true) : [];
            
            if ($tipo === 'venta') {
                $push = $configNotif['alertas']['ventas_admin_push'] ?? true;
                $wa = $configNotif['alertas']['ventas_admin_wa'] ?? true;
                if ($push || $wa) {
                    $cliente_nombre = "Público en general";
                    if ($cliente_id) {
                        $stmtC = $db->prepare("SELECT nombre FROM clientes WHERE id = ?");
                        $stmtC->execute([$cliente_id]);
                        $cliente_nombre = $stmtC->fetchColumn() ?: $cliente_nombre;
                    }
                    notificar_rol(1, "🛒 Nueva Venta Confirmada", "Se ha registrado una venta por $" . number_format($total, 2) . "\nCliente: " . $cliente_nombre, "pos/ver?id={$venta_id}", $wa);
                }
            }

            json_response([
                'status' => 'success',
                'mensaje' => $tipo === 'venta' ? 'Venta confirmada y stock descontado' : 'Cotización guardada exitosamente',
                'id' => $venta_id
            ]);
        } catch (\Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            json_response(['error' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    public function historial() {
        auth_require();
        require_permission('pos.ver');
        $db = Database::getInstance();
        
        try {
            $ventas = $db->query("
                SELECT v.*, c.nombre as cliente_nombre 
                FROM ventas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                ORDER BY v.id DESC
            ")->fetchAll();
        } catch (\PDOException $e) {
            $ventas = [];
        }
        
        $this->render('pos', 'historial', [
            'titulo' => 'Historial de Transacciones',
            'ventas' => $ventas
        ]);
    }

    public function ver() {
        auth_require();
        require_permission('pos.ver');
        $id = intval($_GET['id'] ?? 0);
        
        if (!$id) redirect(base_url('pos/historial?error=Folio no especificado'));
        
        $db = Database::getInstance();
        
        try {
            $stmt = $db->prepare("
                SELECT v.*, 
                       c.nombre as cliente_nombre, c.telefono as cliente_telefono, c.correo as cliente_correo, c.direccion as cliente_direccion,
                       u.name as usuario_nombre
                FROM ventas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                LEFT JOIN users u ON v.usuario_id = u.id
                WHERE v.id = ?
            ");
            $stmt->execute([$id]);
            $venta = $stmt->fetch();
            
            if (!$venta) redirect(base_url('pos/historial?error=Transacción no encontrada'));
            
            $stmtDetalles = $db->prepare("
                SELECT vd.*, i.sku, i.nombre as producto_nombre
                FROM ventas_detalles vd
                LEFT JOIN inventario i ON vd.producto_id = i.id
                WHERE vd.venta_id = ?
            ");
            $stmtDetalles->execute([$id]);
            $detalles = $stmtDetalles->fetchAll();
            
            // Cargar abonos si es venta
            $abonos = [];
            if ($venta['tipo'] === 'venta') {
                $stmtAbonos = $db->prepare("SELECT a.*, c.nombre as cuenta_nombre FROM ventas_abonos a LEFT JOIN finanzas_cuentas c ON a.cuenta_id = c.id WHERE a.venta_id = ? ORDER BY a.fecha_abono ASC");
                $stmtAbonos->execute([$id]);
                $abonos = $stmtAbonos->fetchAll();
            }
            
            // Cargar envíos vinculados
            $stmtEnvios = $db->prepare("
                SELECT e.*, u.name as chofer_nombre
                FROM logistica_envios e
                LEFT JOIN users u ON e.chofer_id = u.id
                WHERE e.venta_id = ? 
                ORDER BY e.id DESC
            ");
            $stmtEnvios->execute([$id]);
            $envios = $stmtEnvios->fetchAll();
            
            // Cargar cuentas para el modal de abono
            $cuentas_finanzas = [];
            try {
                $cuentas_finanzas = $db->query("SELECT id, nombre, tipo FROM finanzas_cuentas WHERE estado = 'activo' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) { }
            
        } catch (\PDOException $e) {
            redirect(base_url('pos/historial?error=Error de base de datos'));
        }
        
        $this->render('pos', 'ver', [
            'titulo' => 'Detalle de ' . ($venta['tipo'] === 'cotizacion' ? 'Cotización' : 'Venta') . ' #' . str_pad($venta['id'], 5, '0', STR_PAD_LEFT),
            'venta' => $venta,
            'detalles' => $detalles,
            'abonos' => $abonos,
            'cuentas_finanzas' => $cuentas_finanzas,
            'envios' => $envios
        ]);
    }

    // --- HOOK PARA EL PANEL DERECHO DE WHATSAPP ---
    public function hookWhatsAppPanel($whatsapp_id) {
        $db = Database::getInstance();
        $html = '';
        try {
            $stmt = $db->prepare("SELECT id FROM clientes WHERE whatsapp_id = ? LIMIT 1");
            $stmt->execute([$whatsapp_id]);
            if ($cliente = $stmt->fetch()) {
                $stmtDeuda = $db->prepare("SELECT SUM(total - monto_recibido) FROM ventas WHERE cliente_id = ? AND estado_pago IN ('pendiente', 'parcial') AND tipo = 'venta'");
                $stmtDeuda->execute([$cliente['id']]);
                $deuda = $stmtDeuda->fetchColumn() ?: 0;
                
                $html .= '<div class="mb-4 text-center">';
                if ($deuda > 0) { $html .= '<div class="inline-block px-3 py-1 bg-red-50 border border-red-100 rounded-lg w-full"><span class="text-[10px] font-bold text-red-500 uppercase block leading-none mb-1">Deuda Pendiente</span><span class="text-lg font-black text-red-600">$' . number_format($deuda, 2) . '</span></div>'; } 
                else { $html .= '<div class="inline-block px-3 py-1 bg-green-50 border border-green-100 rounded-lg w-full"><span class="text-[10px] font-bold text-green-600 uppercase block leading-none">Sin adeudos pendientes</span></div>'; }
                $html .= '</div>';

                $html .= '<div class="space-y-2 mb-4"><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 pl-1">Ventas</p><a href="' . base_url('pos?cliente_id=' . $cliente['id']) . '" target="_blank" class="block w-full p-2.5 bg-white hover:bg-green-50 text-gray-700 hover:text-green-700 text-sm font-bold rounded-lg transition border border-gray-100 shadow-sm flex items-center"><div class="w-7 h-7 rounded bg-green-100 text-green-600 flex items-center justify-center mr-3"><i class="fas fa-cash-register"></i></div> Crear Venta (POS)</a></div>';
            }
        } catch (\Exception $e) {}
        return ['order' => 20, 'html' => $html]; // Order 20 = Debajo de Clientes
    }

    public function marcarEntregado() {
        auth_require();
        require_permission('pos.crear');
        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            $db = Database::getInstance();
            $db->prepare("UPDATE ventas SET estado_entrega = 'entregado' WHERE id = ?")->execute([$id]);
            // Si tenía un envío asignado pero el cliente vino a recogerlo en persona, lo cancelamos/entregamos también
            $db->prepare("UPDATE logistica_envios SET estado = 'entregado', fecha_entrega = NOW() WHERE venta_id = ? AND estado != 'entregado'")->execute([$id]);
        }
        redirect(base_url('pos/ver?id=' . $id . '&success=Mercancía marcada como entregada al cliente'));
    }

    // --- HOOKS PARA EL COPILOTO DE IA (HERRAMIENTAS) ---
    public function hookIaCopilotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'pos_ventas_hoy',
                    'description' => 'Obtiene el número de ventas realizadas y el total de dinero recaudado en notas de venta en el día de hoy.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'dummy' => ['type' => 'STRING', 'description' => 'Parametro vacio.']
                        ]
                    ]
                ]
            ]
        ];
    }

    public function executeIaCopilotTool($name, $args) {
        if ($name === 'pos_ventas_hoy') {
            $db = Database::getInstance();
            $data = $db->query("SELECT COUNT(id) as notas, SUM(total) as total_ventas FROM ventas WHERE DATE(fecha_creacion) = CURDATE() AND tipo = 'venta'")->fetch();
            return ['notas_hoy' => intval($data['notas']), 'dinero_hoy' => floatval($data['total_ventas'])];
        }
        return ['error' => 'Herramienta no soportada por el POS'];
    }

    // --- HOOKS PARA EL BOT DE WHATSAPP PÚBLICO (CLIENTES) ---
    public function hookWaBotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'buscar_producto_catalogo',
                    'description' => 'Busca productos en el catálogo de la mueblería para responderle al cliente con el precio, disponibilidad (sin dar cantidades exactas) y características.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'query' => ['type' => 'STRING', 'description' => 'Nombre del producto o mueble a buscar (Ej. sala, comedor, silla, colchón).']
                        ],
                        'required' => ['query']
                    ]
                ]
            ],
            [
                'declaration' => [
                    'name' => 'enviar_imagen_producto',
                    'description' => 'Envía al cliente una fotografía aleatoria de un producto. Usa esto cuando el cliente te pida fotos, imágenes o quiera ver cómo es el producto.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'query' => ['type' => 'STRING', 'description' => 'Nombre del producto o SKU para buscar su imagen (Ej. sala, comedor, silla).']
                        ],
                        'required' => ['query']
                    ]
                ]
            ]
        ];
    }

    public function executeWaBotTool($name, $args, $contacto_id = null) {
        if ($name === 'buscar_producto_catalogo') {
            $query = sanitize($args['query'] ?? '');
            if (empty($query)) return ['error' => 'Se requiere el nombre del producto a buscar.'];
            $db = Database::getInstance();
            $searchTerm = "%{$query}%";
            $stmt = $db->prepare("SELECT sku, nombre, precio, stock FROM inventario WHERE estado = 'activo' AND nombre LIKE ? LIMIT 5");
            $stmt->execute([$searchTerm]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($resultados as &$r) {
                $r['disponibilidad'] = intval($r['stock']) > 0 ? 'Disponible' : 'Agotado';
                unset($r['stock']); // Ocultar cantidad exacta a la IA
            }
            unset($r);
            
            return empty($resultados) ? ['mensaje' => "No se encontraron productos con el término '{$query}' en el catálogo."] : ['productos_encontrados' => $resultados];
        }
        if ($name === 'enviar_imagen_producto') {
            $query = sanitize($args['query'] ?? '');
            if (empty($query)) return ['error' => 'Se requiere el nombre del producto para buscar la imagen.'];
            
            $db = Database::getInstance();
            $searchTerm = "%{$query}%";
            $stmt = $db->prepare("SELECT id, nombre FROM inventario WHERE estado = 'activo' AND (sku = ? OR nombre LIKE ?) LIMIT 1");
            $stmt->execute([$query, $searchTerm]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) return ['error' => "No se encontraron productos con el término '{$query}'."];
            
            $stmtImg = $db->prepare("SELECT ruta FROM inventario_imagenes WHERE producto_id = ? ORDER BY RAND() LIMIT 1");
            $stmtImg->execute([$producto['id']]);
            $imagen = $stmtImg->fetch(PDO::FETCH_ASSOC);
            
            if (!$imagen || empty($imagen['ruta'])) return ['error' => "El producto '{$producto['nombre']}' no tiene imágenes registradas en el catálogo."];
            
            $contactoInfo = $db->query("SELECT whatsapp_id FROM wa_contactos WHERE id = " . intval($contacto_id))->fetch(PDO::FETCH_ASSOC);
            
            if ($contactoInfo) {
                $pathAbsoluto = BASE_PATH . '/' . $imagen['ruta'];
                if (file_exists($pathAbsoluto)) {
                    $mime = mime_content_type($pathAbsoluto);
                    $data = file_get_contents($pathAbsoluto);
                    $base64 = 'data:' . $mime . ';base64,' . base64_encode($data);
                    
                    $stmtIns = $db->prepare("INSERT INTO wa_mensajes (contacto_id, direccion, tipo, contenido, estado, es_bot) VALUES (?, 'saliente', 'imagen', ?, 'enviado', 1)");
                    $stmtIns->execute([$contacto_id, $imagen['ruta']]);
                    
                    $chWa = curl_init('http://localhost:3000/api/enviar');
                    curl_setopt($chWa, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($chWa, CURLOPT_POST, true);
                    curl_setopt($chWa, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    curl_setopt($chWa, CURLOPT_POSTFIELDS, json_encode(['numero' => $contactoInfo['whatsapp_id'], 'mensaje' => '📸 ' . $producto['nombre'], 'archivo' => $base64, 'nombreArchivo' => basename($imagen['ruta'])]));
                    curl_exec($chWa);
                    curl_close($chWa);
                    
                    return ['exito' => "Se ha enviado una imagen de '{$producto['nombre']}' al cliente exitosamente. Ya la puede ver en su WhatsApp."];
                }
                return ['error' => "El archivo de imagen no existe en el servidor."];
            }
            return ['error' => "No se encontró el destinatario en WhatsApp."];
        }
        return ['error' => 'Herramienta no soportada por el módulo POS'];
    }
}
