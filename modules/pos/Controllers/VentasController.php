<?php

class VentasController extends Controller {
    
    public function pos() {
        auth_require();
        
        $db = Database::getInstance();
        
        // Obtener clientes para el selector
        $clientes = $db->query("SELECT id, nombre, telefono FROM clientes ORDER BY nombre ASC")->fetchAll();
        
        // Obtener productos activos con su imagen principal
        $productos = $db->query("
            SELECT i.id, i.sku, i.nombre, i.precio, i.stock,
                   (SELECT ruta FROM inventario_imagenes img WHERE img.producto_id = i.id ORDER BY es_principal DESC LIMIT 1) as imagen
            FROM inventario i
            WHERE i.estado = 'activo'
            ORDER BY i.nombre ASC
        ")->fetchAll();
        
        $this->render('ventas', 'pos', [
            'titulo' => 'Punto de Venta (POS)',
            'clientes' => $clientes,
            'productos' => $productos
        ]);
    }

    public function guardar() {
        auth_require();
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
        
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
        
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // 1. Guardar la cabecera (Venta / Cotización)
            $stmt = $db->prepare("INSERT INTO ventas (cliente_id, usuario_id, tipo, subtotal, descuento, costo_envio, total, notas_internas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $usuario_id, $tipo, $subtotal, $descuento, $costo_envio, $total, $notas]);
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
            
            $db->commit();
            
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
}