<?php

class PromocionesController extends Controller {
    
    public function index() {
        auth_require();
        require_permission('promociones.ver');
        $db = Database::getInstance();
        
        try {
            $promociones = $db->query("SELECT * FROM promociones ORDER BY id DESC")->fetchAll();
        } catch (\PDOException $e) {
            $promociones = [];
        }
        
        $this->render('promociones', 'index', [
            'titulo' => 'Promociones y Cupones',
            'promociones' => $promociones
        ]);
    }

    public function nuevo() {
        auth_require();
        require_permission('promociones.crear');
        $db = Database::getInstance();
        $productos = $db->query("SELECT id, nombre, sku FROM inventario WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        $this->render('promociones', 'nuevo', [
            'titulo' => 'Nueva Promoción',
            'productos' => $productos
        ]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('promociones.crear');
        
        $nombre = sanitize($_POST['nombre'] ?? '');
        $tipo = in_array($_POST['tipo'] ?? '', ['porcentaje', 'monto_fijo']) ? $_POST['tipo'] : 'porcentaje';
        $valor = floatval($_POST['valor'] ?? 0);
        $codigo_cupon = !empty($_POST['codigo_cupon']) ? strtoupper(sanitize($_POST['codigo_cupon'])) : null;
        $monto_minimo = floatval($_POST['monto_minimo'] ?? 0);
        $cantidad_minima = intval($_POST['cantidad_minima'] ?? 0);
        $productos_requeridos = !empty($_POST['productos_requeridos']) && is_array($_POST['productos_requeridos']) ? json_encode(array_map('intval', $_POST['productos_requeridos'])) : null;
        $fecha_inicio = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
        $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

        if (empty($nombre) || $valor <= 0) {
            redirect(base_url('promociones/nuevo?error=El nombre y el valor son obligatorios'));
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("INSERT INTO promociones (nombre, tipo, valor, codigo_cupon, monto_minimo, cantidad_minima, productos_requeridos, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $tipo, $valor, $codigo_cupon, $monto_minimo, $cantidad_minima, $productos_requeridos, $fecha_inicio, $fecha_fin]);
            redirect(base_url('promociones?success=Promoción creada con éxito'));
        } catch (\PDOException $e) {
            redirect(base_url('promociones/nuevo?error=Error al guardar. Si usaste un cupón, verifica que no esté repetido.'));
        }
    }

    public function ver() {
        auth_require();
        require_permission('promociones.ver');
        $id = intval($_GET['id'] ?? 0);
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT * FROM promociones WHERE id = ?");
        $stmt->execute([$id]);
        $promocion = $stmt->fetch();
        
        if (!$promocion) redirect(base_url('promociones?error=Promoción no encontrada'));
        
        $productos_requeridos = [];
        if (!empty($promocion['productos_requeridos'])) {
            $req_ids = json_decode($promocion['productos_requeridos'], true);
            if (is_array($req_ids) && count($req_ids) > 0) {
                $placeholders = implode(',', array_fill(0, count($req_ids), '?'));
                $stmtProd = $db->prepare("SELECT id, sku, nombre FROM inventario WHERE id IN ($placeholders)");
                $stmtProd->execute($req_ids);
                $productos_requeridos = $stmtProd->fetchAll();
            }
        }

        $this->render('promociones', 'ver', [
            'titulo' => 'Detalle de Promoción',
            'promocion' => $promocion,
            'productos_requeridos' => $productos_requeridos
        ]);
    }

    public function editar() {
        auth_require();
        require_permission('promociones.crear');
        $id = intval($_GET['id'] ?? 0);
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM promociones WHERE id = ?");
        $stmt->execute([$id]);
        $promocion = $stmt->fetch();
        
        if (!$promocion) redirect(base_url('promociones?error=Promoción no encontrada'));
        
        $productos = $db->query("SELECT id, nombre, sku FROM inventario WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        $this->render('promociones', 'editar', [
            'titulo' => 'Editar Promoción',
            'promocion' => $promocion,
            'productos' => $productos
        ]);
    }

    public function postEditar() {
        auth_require();
        require_permission('promociones.crear');
        $id = intval($_POST['id'] ?? 0);
        if (!$id) redirect(base_url('promociones?error=ID no válido'));

        $nombre = sanitize($_POST['nombre'] ?? '');
        $tipo = in_array($_POST['tipo'] ?? '', ['porcentaje', 'monto_fijo']) ? $_POST['tipo'] : 'porcentaje';
        $valor = floatval($_POST['valor'] ?? 0);
        $codigo_cupon = !empty($_POST['codigo_cupon']) ? strtoupper(sanitize($_POST['codigo_cupon'])) : null;
        $monto_minimo = floatval($_POST['monto_minimo'] ?? 0);
        $cantidad_minima = intval($_POST['cantidad_minima'] ?? 0);
        $productos_requeridos = !empty($_POST['productos_requeridos']) && is_array($_POST['productos_requeridos']) ? json_encode(array_map('intval', $_POST['productos_requeridos'])) : null;
        $fecha_inicio = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
        $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

        if (empty($nombre) || $valor <= 0) { redirect(base_url("promociones/editar?id=$id&error=El nombre y el valor son obligatorios")); }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("UPDATE promociones SET nombre=?, tipo=?, valor=?, codigo_cupon=?, monto_minimo=?, cantidad_minima=?, productos_requeridos=?, fecha_inicio=?, fecha_fin=? WHERE id=?");
            $stmt->execute([$nombre, $tipo, $valor, $codigo_cupon, $monto_minimo, $cantidad_minima, $productos_requeridos, $fecha_inicio, $fecha_fin, $id]);
            redirect(base_url('promociones?success=Promoción actualizada con éxito'));
        } catch (\PDOException $e) {
            redirect(base_url("promociones/editar?id=$id&error=Error al actualizar. Posible cupón repetido."));
        }
    }

    public function cambiarEstado() {
        auth_require();
        require_permission('promociones.crear');
        $id = intval($_GET['id'] ?? 0);
        
        if ($id) {
            $db = Database::getInstance();
            $db->prepare("UPDATE promociones SET estado = IF(estado = 'activo', 'inactivo', 'activo') WHERE id = ?")->execute([$id]);
        }
        redirect(base_url('promociones?success=Estado actualizado'));
    }

    // --- API para el Punto de Venta (POS) ---
    public function validarCupon() {
        auth_require();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $codigo = sanitize($data['codigo'] ?? '');
        $subtotal = floatval($data['subtotal'] ?? 0);
        $cantidad_items = intval($data['cantidad_items'] ?? 0);
        $carrito = is_array($data['carrito'] ?? null) ? $data['carrito'] : [];

        if (empty($codigo)) {
            json_response(['error' => 'Código de cupón vacío'], 400);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM promociones WHERE codigo_cupon = ? AND estado = 'activo'");
        $stmt->execute([$codigo]);
        $promo = $stmt->fetch();

        if (!$promo) {
            json_response(['error' => 'Cupón no válido o inactivo'], 404);
        }

        $hoy = date('Y-m-d');
        if ($promo['fecha_inicio'] && $promo['fecha_inicio'] > $hoy) {
            json_response(['error' => 'El cupón aún no entra en vigencia'], 400);
        }
        if ($promo['fecha_fin'] && $promo['fecha_fin'] < $hoy) {
            json_response(['error' => 'El cupón ha expirado'], 400);
        }
        if ($promo['monto_minimo'] > 0 && $subtotal < $promo['monto_minimo']) {
            json_response(['error' => 'El carrito no alcanza el monto mínimo de $' . number_format($promo['monto_minimo'], 2) . ' para este cupón'], 400);
        }
        if ($promo['cantidad_minima'] > 0 && $cantidad_items < $promo['cantidad_minima']) {
            json_response(['error' => 'Debes llevar al menos ' . $promo['cantidad_minima'] . ' productos para aplicar este cupón'], 400);
        }
        
        if (!empty($promo['productos_requeridos'])) {
            $requeridos = json_decode($promo['productos_requeridos'], true);
            if (is_array($requeridos) && count($requeridos) > 0) {
                $ids_en_carrito = array_map(function($item) { return intval($item['id']); }, $carrito);
                
                // Verificar si todos los productos requeridos están en el carrito
                $faltan = array_diff($requeridos, $ids_en_carrito);
                if (count($faltan) > 0) {
                    // Traer nombres para un mensaje más amigable
                    $nombres_faltantes = $db->query("SELECT nombre FROM inventario WHERE id IN (" . implode(',', $faltan) . ")")->fetchAll(PDO::FETCH_COLUMN);
                    json_response(['error' => 'Faltan productos para este paquete: ' . implode(', ', $nombres_faltantes)], 400);
                }
            }
        }

        // Calcular el descuento exacto
        $monto_descuento = 0;
        if ($promo['tipo'] === 'porcentaje') {
            $monto_descuento = ($subtotal * floatval($promo['valor'])) / 100;
        } else {
            $monto_descuento = min(floatval($promo['valor']), $subtotal); // No puede descontar más de lo que cuesta
        }

        json_response([
            'status' => 'success',
            'mensaje' => 'Cupón aplicado: ' . htmlspecialchars($promo['nombre']),
            'descuento_calculado' => $monto_descuento,
            'promocion' => $promo
        ]);
    }

    // --- HOOK PARA INYECTAR CONTEXTO A LA MEMORIA DE LA IA (RAG) ---
    public function hookIaRagContext($contacto_id = null) {
        $db = Database::getInstance();
        $contexto = "";
        
        try {
            $hoy = date('Y-m-d');
            $promos = $db->query("
                SELECT nombre, tipo, valor, codigo_cupon, monto_minimo, cantidad_minima, productos_requeridos 
                FROM promociones 
                WHERE estado = 'activo' 
                AND (fecha_inicio IS NULL OR fecha_inicio <= '$hoy')
                AND (fecha_fin IS NULL OR fecha_fin >= '$hoy')
            ")->fetchAll();
            
            if (!empty($promos)) {
                $contexto .= "🎉 **PROMOCIONES ACTIVAS HOY (Puedes usarlas como gancho de venta):**\n";
                foreach ($promos as $p) {
                    $descuento = $p['tipo'] === 'porcentaje' ? floatval($p['valor']) . "%" : "$" . number_format($p['valor'], 2);
                    $cupon = !empty($p['codigo_cupon']) ? " (Código: {$p['codigo_cupon']})" : "";
                    $condiciones = [];
                    if ($p['monto_minimo'] > 0) $condiciones[] = "Compra mínima de $" . number_format($p['monto_minimo'], 2);
                    if ($p['cantidad_minima'] > 0) $condiciones[] = "Llevar al menos {$p['cantidad_minima']} productos";
                    $texto_condiciones = !empty($condiciones) ? " | Condiciones: " . implode(', ', $condiciones) : "";
                    
                    $contexto .= "- **{$p['nombre']}**: Descuento de {$descuento}{$cupon}{$texto_condiciones}\n";
                }
            }
        } catch (\Exception $e) {}

        return $contexto;
    }

    // --- HOOKS PARA EL COPILOTO DE IA (HERRAMIENTAS INTERNAS) ---
    public function hookIaCopilotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'promociones_consultar_activas',
                    'description' => 'Consulta la lista de promociones y cupones de descuento que están activos en el sistema el día de hoy.',
                    'parameters' => ['type' => 'OBJECT', 'properties' => ['dummy' => ['type' => 'STRING', 'description' => 'Parámetro vacío.']]]
                ]
            ]
        ];
    }

    public function executeIaCopilotTool($name, $args) {
        if ($name === 'promociones_consultar_activas') {
            $db = Database::getInstance();
            $hoy = date('Y-m-d');
            $promos = $db->query("
                SELECT nombre, tipo, valor, codigo_cupon, monto_minimo, cantidad_minima, productos_requeridos 
                FROM promociones 
                WHERE estado = 'activo' 
                AND (fecha_inicio IS NULL OR fecha_inicio <= '$hoy')
                AND (fecha_fin IS NULL OR fecha_fin >= '$hoy')
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar los IDs de productos requeridos para el copiloto
            foreach ($promos as &$p) {
                if (!empty($p['productos_requeridos'])) {
                    $req_ids = json_decode($p['productos_requeridos'], true);
                    if (is_array($req_ids) && count($req_ids) > 0) {
                        $placeholders = implode(',', array_fill(0, count($req_ids), '?'));
                        $stmtProd = $db->prepare("SELECT nombre FROM inventario WHERE id IN ($placeholders)");
                        $stmtProd->execute($req_ids);
                        $p['nombres_productos_requeridos'] = $stmtProd->fetchAll(PDO::FETCH_COLUMN);
                    }
                }
            }
            unset($p);

            return empty($promos) ? ['mensaje' => 'No hay promociones activas hoy.'] : ['promociones_activas' => $promos];
        }
        return ['error' => 'Herramienta no soportada por Promociones'];
    }

    // --- HOOKS PARA EL BOT DE WHATSAPP PÚBLICO (CLIENTES) ---
    public function hookWaBotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'promociones_validar_cupon',
                    'description' => 'Valida un cupón y detalla sus reglas (productos requeridos, mínimos). Si pasas el precio normal, calcula el ahorro y precio final.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'codigo' => ['type' => 'STRING', 'description' => 'El código del cupón a validar.'],
                            'precio_normal' => ['type' => 'NUMBER', 'description' => 'Opcional. El precio original del producto o carrito para calcular cuánto ahorraría el cliente.']
                        ],
                        'required' => ['codigo']
                    ]
                ]
            ]
        ];
    }

    public function executeWaBotTool($name, $args, $contacto_id = null) {
        if ($name === 'promociones_validar_cupon') {
            $codigo = sanitize($args['codigo'] ?? '');
            $precio_normal = floatval($args['precio_normal'] ?? 0);
            
            if (empty($codigo)) return ['error' => 'Se requiere el código del cupón.'];
            
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM promociones WHERE codigo_cupon = ? AND estado = 'activo'");
            $stmt->execute([$codigo]);
            $promo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$promo) return ['mensaje' => 'El cupón no es válido o ha expirado.'];
            
            $detalles = [
                'nombre' => $promo['nombre'],
                'tipo_descuento' => $promo['tipo'] === 'porcentaje' ? floatval($promo['valor']) . '%' : '$' . number_format($promo['valor'], 2),
                'advertencia_obligatoria_ia' => '¡ATENCIÓN! DEBES MENCIONARLE AL CLIENTE LAS SIGUIENTES CONDICIONES PARA QUE APLIQUE LA OFERTA:',
                'condiciones_de_validez' => []
            ];
            
            if ($promo['monto_minimo'] > 0) $detalles['condiciones_de_validez'][] = "Compra mínima de $" . number_format($promo['monto_minimo'], 2);
            if ($promo['cantidad_minima'] > 0) $detalles['condiciones_de_validez'][] = "Llevar al menos " . $promo['cantidad_minima'] . " productos";
            
            if (!empty($promo['productos_requeridos'])) {
                $req_ids = json_decode($promo['productos_requeridos'], true);
                if (is_array($req_ids) && count($req_ids) > 0) {
                    $placeholders = implode(',', array_fill(0, count($req_ids), '?'));
                    $stmtProd = $db->prepare("SELECT nombre FROM inventario WHERE id IN ($placeholders)");
                    $stmtProd->execute($req_ids);
                    $nombres_prod = $stmtProd->fetchAll(PDO::FETCH_COLUMN);
                    $detalles['condiciones_de_validez'][] = "Solo aplica si compra estos productos juntos: " . implode(', ', $nombres_prod);
                }
            }
            
            if (empty($detalles['condiciones_de_validez'])) $detalles['condiciones_de_validez'][] = "Aplica para cualquier compra.";
            
            if ($precio_normal > 0) {
                $descuento = $promo['tipo'] === 'porcentaje' ? ($precio_normal * floatval($promo['valor'])) / 100 : min(floatval($promo['valor']), $precio_normal);
                $detalles['calculo_ahorro'] = [
                    'precio_antes' => '$' . number_format($precio_normal, 2),
                    'te_ahorras' => '$' . number_format($descuento, 2),
                    'precio_final_oferta' => '$' . number_format($precio_normal - $descuento, 2)
                ];
            }
            
            return ['cupon_valido' => true, 'informacion_promocion' => $detalles];
        }
        return ['error' => 'Herramienta no soportada por Promociones'];
    }
}