<?php

class InventarioController extends Controller {
    public function index() {
        auth_require(); // Proteger ruta
        require_permission('inventario.ver');
        
        $db = Database::getInstance();
        try {
            $productos = $db->query("SELECT i.*, c.nombre as categoria_nombre FROM inventario i LEFT JOIN inventario_categorias c ON i.categoria_id = c.id ORDER BY i.id DESC")->fetchAll();
        } catch (\PDOException $e) {
            $productos = [];
        }
        
        $this->render('inventario', 'index', [
            'titulo' => 'Control de Inventario',
            'productos' => $productos
        ]);
    }

    public function nuevo() {
        auth_require();
        require_permission('inventario.crear');
        $error = $_GET['error'] ?? null;
        
        $db = Database::getInstance();
        $categorias = $db->query("SELECT * FROM inventario_categorias ORDER BY nombre ASC")->fetchAll();
        $proveedores = $db->query("SELECT * FROM inventario_proveedores ORDER BY nombre ASC")->fetchAll();

        $this->render('inventario', 'nuevo', [
            'titulo' => 'Nuevo Producto',
            'error' => $error,
            'categorias' => $categorias,
            'proveedores' => $proveedores
        ]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('inventario.crear');
        
        $sku = sanitize($_POST['sku'] ?? '');
        $nombre = sanitize($_POST['nombre'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $categoria_id = intval($_POST['categoria_id'] ?? 1);
        $proveedor_id = !empty($_POST['proveedor_id']) ? intval($_POST['proveedor_id']) : null;
        $precio = floatval($_POST['precio'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $stock_minimo = intval($_POST['stock_minimo'] ?? 5);
        $ubicacion = sanitize($_POST['ubicacion'] ?? '');
        $estado = sanitize($_POST['estado'] ?? 'activo');

        if (empty($sku) || empty($nombre)) {
            redirect(base_url('inventario/nuevo?error=El SKU y el Nombre son obligatorios'));
        }

        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();

            // 1. Insertar Producto
            $stmt = $db->prepare("INSERT INTO inventario (sku, nombre, descripcion, categoria_id, proveedor_id, precio, stock, stock_minimo, ubicacion, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sku, $nombre, $descripcion, $categoria_id, $proveedor_id, $precio, $stock, $stock_minimo, $ubicacion, $estado]);
            
            $producto_id = $db->lastInsertId();

            // 2. Movimiento inicial en el Kardex (Si el stock > 0)
            if ($stock > 0) {
                $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
                $stmtKardex = $db->prepare("INSERT INTO inventario_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, motivo) VALUES (?, ?, 'entrada', ?, 'Inventario inicial')");
                $stmtKardex->execute([$producto_id, $usuario_id, $stock]);
            }

            // 3. Subir Imágenes a la Galería
            if (!empty($_FILES['imagenes']['name'][0])) {
                $storage_dir = dirname(__DIR__, 3) . '/storage/productos/' . $producto_id;
                if (!is_dir($storage_dir)) mkdir($storage_dir, 0777, true);

                $es_principal = 1;
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['imagenes']['error'][$key] === 0) {
                        $ext = strtolower(pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $filename = uniqid('img_') . '.' . $ext;
                            $filepath = $storage_dir . '/' . $filename;
                            if (move_uploaded_file($tmp_name, $filepath)) {
                                $ruta_db = 'storage/productos/' . $producto_id . '/' . $filename;
                                $stmtImg = $db->prepare("INSERT INTO inventario_imagenes (producto_id, ruta, es_principal) VALUES (?, ?, ?)");
                                $stmtImg->execute([$producto_id, $ruta_db, $es_principal]);
                                $es_principal = 0; // Solo la primera foto será la principal
                            }
                        }
                    }
                }
            }

            $db->commit();
            redirect(base_url('inventario?success=Producto agregado con su inventario inicial e imágenes'));
            
        } catch (\PDOException $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            redirect(base_url('inventario/nuevo?error=Error de base de datos. Es posible que el SKU ya exista.'));
        }
    }

    public function ver() {
        auth_require();
        require_permission('inventario.ver');
        $id = intval($_GET['id'] ?? 0);
        
        if (!$id) redirect(base_url('inventario?error=Producto no especificado'));
        
        $db = Database::getInstance();
        
        // Obtener datos del producto junto con categoría y proveedor
        $stmt = $db->prepare("SELECT i.*, c.nombre as categoria_nombre, p.nombre as proveedor_nombre 
                              FROM inventario i 
                              LEFT JOIN inventario_categorias c ON i.categoria_id = c.id 
                              LEFT JOIN inventario_proveedores p ON i.proveedor_id = p.id 
                              WHERE i.id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();
        
        if (!$producto) redirect(base_url('inventario?error=Producto no encontrado'));
        
        // Obtener imágenes
        $stmtImg = $db->prepare("SELECT * FROM inventario_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, id ASC");
        $stmtImg->execute([$id]);
        $imagenes = $stmtImg->fetchAll();
        
        $this->render('inventario', 'ver', [
            'titulo' => 'Detalle del Producto: ' . $producto['sku'],
            'producto' => $producto,
            'imagenes' => $imagenes
        ]);
    }

    public function editar() {
        auth_require();
        require_permission('inventario.crear');
        $id = intval($_GET['id'] ?? 0);
        $error = $_GET['error'] ?? null;
        
        if (!$id) redirect(base_url('inventario?error=Producto no especificado'));
        
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT * FROM inventario WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();
        
        if (!$producto) redirect(base_url('inventario?error=Producto no encontrado'));
        
        $categorias = $db->query("SELECT * FROM inventario_categorias ORDER BY nombre ASC")->fetchAll();
        $proveedores = $db->query("SELECT * FROM inventario_proveedores ORDER BY nombre ASC")->fetchAll();
        
        $stmtImg = $db->prepare("SELECT * FROM inventario_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, id ASC");
        $stmtImg->execute([$id]);
        $imagenes = $stmtImg->fetchAll();

        $this->render('inventario', 'editar', [
            'titulo' => 'Editar Producto: ' . $producto['sku'],
            'producto' => $producto,
            'categorias' => $categorias,
            'proveedores' => $proveedores,
            'imagenes' => $imagenes,
            'error' => $error
        ]);
    }

    public function postEditar() {
        auth_require();
        require_permission('inventario.crear');
        
        $id = intval($_POST['id'] ?? 0);
        if (!$id) redirect(base_url('inventario?error=Producto no especificado'));

        $sku = sanitize($_POST['sku'] ?? '');
        $nombre = sanitize($_POST['nombre'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $categoria_id = intval($_POST['categoria_id'] ?? 1);
        $proveedor_id = !empty($_POST['proveedor_id']) ? intval($_POST['proveedor_id']) : null;
        $precio = floatval($_POST['precio'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $stock_minimo = intval($_POST['stock_minimo'] ?? 5);
        $ubicacion = sanitize($_POST['ubicacion'] ?? '');
        $estado = sanitize($_POST['estado'] ?? 'activo');

        if (empty($sku) || empty($nombre)) {
            redirect(base_url("inventario/editar?id=$id&error=El SKU y el Nombre son obligatorios"));
        }

        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();

            // 1. Obtener stock anterior para el Kardex
            $stmtOld = $db->prepare("SELECT stock FROM inventario WHERE id = ?");
            $stmtOld->execute([$id]);
            $oldStock = $stmtOld->fetchColumn();

            // 2. Actualizar Producto
            $stmt = $db->prepare("UPDATE inventario SET sku = ?, nombre = ?, descripcion = ?, categoria_id = ?, proveedor_id = ?, precio = ?, stock = ?, stock_minimo = ?, ubicacion = ?, estado = ? WHERE id = ?");
            $stmt->execute([$sku, $nombre, $descripcion, $categoria_id, $proveedor_id, $precio, $stock, $stock_minimo, $ubicacion, $estado, $id]);
            
            // 3. Registrar ajuste en el Kardex si el stock cambió manualmente
            if ($oldStock !== false && $oldStock != $stock) {
                $diferencia = $stock - $oldStock;
                $tipoMov = $diferencia > 0 ? 'entrada' : 'ajuste';
                if ($diferencia < 0) $tipoMov = 'salida';
                $cantidadMov = abs($diferencia);
                $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
                $stmtKardex = $db->prepare("INSERT INTO inventario_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, motivo) VALUES (?, ?, ?, ?, 'Ajuste manual desde edición')");
                $stmtKardex->execute([$id, $usuario_id, $tipoMov, $cantidadMov]);
            }

            // 4. Eliminar imágenes seleccionadas
            if (!empty($_POST['eliminar_imagenes'])) {
                foreach ($_POST['eliminar_imagenes'] as $imgId) {
                    $stmtImg = $db->prepare("SELECT ruta FROM inventario_imagenes WHERE id = ? AND producto_id = ?");
                    $stmtImg->execute([$imgId, $id]);
                    if ($img = $stmtImg->fetch()) {
                        $filepath = dirname(__DIR__, 3) . '/' . $img['ruta'];
                        if (file_exists($filepath)) unlink($filepath);
                        $db->prepare("DELETE FROM inventario_imagenes WHERE id = ?")->execute([$imgId]);
                    }
                }
            }

            // 5. Subir nuevas imágenes
            if (!empty($_FILES['imagenes']['name'][0])) {
                $storage_dir = dirname(__DIR__, 3) . '/storage/productos/' . $id;
                if (!is_dir($storage_dir)) mkdir($storage_dir, 0777, true);

                $es_principal = 0; // Las nuevas imágenes no serán principales por defecto
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['imagenes']['error'][$key] === 0) {
                        $ext = strtolower(pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $filename = uniqid('img_') . '.' . $ext;
                            $filepath = $storage_dir . '/' . $filename;
                            if (move_uploaded_file($tmp_name, $filepath)) {
                                $ruta_db = 'storage/productos/' . $id . '/' . $filename;
                                $stmtImg = $db->prepare("INSERT INTO inventario_imagenes (producto_id, ruta, es_principal) VALUES (?, ?, ?)");
                                $stmtImg->execute([$id, $ruta_db, $es_principal]);
                            }
                        }
                    }
                }
            }

            $db->commit();
            redirect(base_url('inventario?success=Producto actualizado correctamente'));
            
        } catch (\PDOException $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            redirect(base_url("inventario/editar?id=$id&error=Error al actualizar. Verifique que no esté duplicando el código SKU."));
        }
    }

    // --- HOOKS Y ENDPOINTS PARA OTROS MÓDULOS ---

    /**
     * Hook para el panel derecho del chat de WhatsApp.
     * Provee un buscador de productos para que el asesor pueda enviar información rápidamente.
     */
    public function hookWhatsAppPanel($whatsapp_id) {
        if (!has_permission('inventario.ver')) return null;

        // Obtenemos los 3 productos más recientes para mostrar inicialmente
        $db = Database::getInstance();
        $productos_iniciales = $db->query("
            SELECT i.id, i.sku, i.nombre, i.precio, i.stock,
                   (SELECT ruta FROM inventario_imagenes img WHERE img.producto_id = i.id ORDER BY es_principal DESC, img.id ASC LIMIT 1) as imagen
            FROM inventario i WHERE i.estado = 'activo' ORDER BY i.id DESC LIMIT 3
        ")->fetchAll();

        $data = [
            'base_url' => base_url(),
            'placeholder_url' => base_url('storage/assets/placeholder.png'),
            'productos_iniciales' => $productos_iniciales
        ];

        // Usamos el método renderToString() del controlador base para capturar la vista como un string
        $html = $this->renderToString('inventario', 'hooks/whatsapp_panel', $data);
        
        return ['order' => 20, 'html' => $html]; // order 20 para que aparezca arriba
    }

    public function ajaxBuscarProductos() {
        auth_require();
        require_permission('inventario.ver');
        $q = sanitize($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            json_response(['productos' => []]);
            return;
        }
        $searchTerm = "%{$q}%";
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT i.id, i.sku, i.nombre, i.precio, i.stock,
                   (SELECT ruta FROM inventario_imagenes img WHERE img.producto_id = i.id ORDER BY es_principal DESC, img.id ASC LIMIT 1) as imagen
            FROM inventario i
            WHERE (i.sku LIKE ? OR i.nombre LIKE ?) AND i.estado = 'activo'
            LIMIT 5
        ");
        $stmt->execute([$searchTerm, $searchTerm]);
        $productos = $stmt->fetchAll();

        json_response(['productos' => $productos]);
    }

    // --- GESTIÓN DE INGRESOS DE MERCANCÍA ---

    public function listarIngresos() {
        auth_require();
        require_permission('inventario.crear');

        $db = Database::getInstance();
        $ingresos = $db->query("
            SELECT ing.id, ing.fecha_ingreso, ing.referencia_factura, ing.costo_total, p.nombre as proveedor_nombre, u.name as usuario_nombre
            FROM inventario_ingresos ing
            LEFT JOIN inventario_proveedores p ON ing.proveedor_id = p.id
            LEFT JOIN users u ON ing.usuario_id = u.id
            ORDER BY ing.id DESC
        ")->fetchAll();

        $this->render('inventario', 'ingresos/index', [
            'titulo' => 'Historial de Ingresos de Mercancía',
            'ingresos' => $ingresos
        ]);
    }

    public function nuevoIngreso() {
        auth_require();
        require_permission('inventario.crear');

        $db = Database::getInstance();
        $proveedores = $db->query("SELECT * FROM inventario_proveedores ORDER BY nombre ASC")->fetchAll();

        $this->render('inventario', 'ingresos/nuevo', [
            'titulo' => 'Registrar Ingreso de Mercancía',
            'proveedores' => $proveedores
        ]);
    }

    public function guardarIngreso() {
        auth_require();
        require_permission('inventario.crear');

        $proveedor_id = !empty($_POST['proveedor_id']) ? intval($_POST['proveedor_id']) : null;
        $referencia_factura = sanitize($_POST['referencia_factura'] ?? '');
        $notas = sanitize($_POST['notas'] ?? '');
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
        
        $productos = $_POST['productos'] ?? [];

        if (empty($productos)) {
            redirect(base_url('inventario/ingresos/nuevo?error=Debes agregar al menos un producto al ingreso.'));
        }

        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            $costo_total_ingreso = 0;
            foreach ($productos as $p) {
                $costo_total_ingreso += (intval($p['cantidad']) * floatval($p['costo']));
            }

            // 1. Crear el registro maestro del ingreso
            $stmtIngreso = $db->prepare("INSERT INTO inventario_ingresos (proveedor_id, usuario_id, referencia_factura, notas, costo_total) VALUES (?, ?, ?, ?, ?)");
            $stmtIngreso->execute([$proveedor_id, $usuario_id, $referencia_factura, $notas, $costo_total_ingreso]);
            $ingreso_id = $db->lastInsertId();

            // 2. Procesar cada producto del ingreso
            foreach ($productos as $p) {
                $producto_id = intval($p['id']);
                $cantidad = intval($p['cantidad']);
                $costo_unitario = floatval($p['costo']);

                if ($producto_id <= 0 || $cantidad <= 0) continue;

                // a. Insertar el detalle del ingreso
                $stmtDetalle = $db->prepare("INSERT INTO inventario_ingreso_detalles (ingreso_id, producto_id, cantidad, costo_unitario) VALUES (?, ?, ?, ?)");
                $stmtDetalle->execute([$ingreso_id, $producto_id, $cantidad, $costo_unitario]);

                // b. Actualizar el stock del producto
                $stmtStock = $db->prepare("UPDATE inventario SET stock = stock + ? WHERE id = ?");
                $stmtStock->execute([$cantidad, $producto_id]);

                // c. Registrar el movimiento en el Kardex
                $motivo = "Ingreso por compra (Factura: $referencia_factura)";
                $costo_total_mov = $cantidad * $costo_unitario;
                $stmtKardex = $db->prepare("INSERT INTO inventario_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, costo_unitario, costo_total, motivo) VALUES (?, ?, 'entrada', ?, ?, ?, ?)");
                $stmtKardex->execute([$producto_id, $usuario_id, $cantidad, $costo_unitario, $costo_total_mov, $motivo]);
            }

            $db->commit();
            redirect(base_url('inventario/ingresos?success=Ingreso de mercancía registrado correctamente. El stock ha sido actualizado.'));

        } catch (\PDOException $e) {
            if ($db->inTransaction()) $db->rollBack();
            // Para depuración:
            // redirect(base_url('inventario/ingresos/nuevo?error=' . urlencode($e->getMessage())));
            redirect(base_url('inventario/ingresos/nuevo?error=Error de base de datos al guardar el ingreso.'));
        }
    }

    public function verIngreso() {
        auth_require();
        require_permission('inventario.ver');
        $id = intval($_GET['id'] ?? 0);
        if (!$id) redirect(base_url('inventario/ingresos?error=Ingreso no especificado'));

        $db = Database::getInstance();
        
        // 1. Obtener datos del encabezado del ingreso
        $stmtIngreso = $db->prepare("
            SELECT ing.*, p.nombre as proveedor_nombre, u.name as usuario_nombre
            FROM inventario_ingresos ing
            LEFT JOIN inventario_proveedores p ON ing.proveedor_id = p.id
            LEFT JOIN users u ON ing.usuario_id = u.id
            WHERE ing.id = ?
        ");
        $stmtIngreso->execute([$id]);
        $ingreso = $stmtIngreso->fetch();

        if (!$ingreso) redirect(base_url('inventario/ingresos?error=Ingreso no encontrado'));

        // 2. Obtener los productos de ese ingreso
        $stmtDetalles = $db->prepare("
            SELECT det.*, prod.sku, prod.nombre as producto_nombre
            FROM inventario_ingreso_detalles det
            LEFT JOIN inventario prod ON det.producto_id = prod.id
            WHERE det.ingreso_id = ?
        ");
        $stmtDetalles->execute([$id]);
        $detalles = $stmtDetalles->fetchAll();

        $this->render('inventario', 'ingresos/ver', [
            'titulo' => 'Detalle de Ingreso #' . str_pad($ingreso['id'], 5, '0', STR_PAD_LEFT),
            'ingreso' => $ingreso,
            'detalles' => $detalles
        ]);
    }

    public function verKardex() {
        auth_require();
        require_permission('inventario.ver');

        $db = Database::getInstance();
        $producto_id = intval($_GET['producto_id'] ?? 0);
        $producto_filtro = null;

        $query = "
            SELECT 
                mov.id, 
                mov.fecha as fecha_registro, 
                mov.tipo_movimiento, 
                mov.cantidad, 
                mov.costo_unitario,
                mov.costo_total,
                mov.motivo,
                prod.id as producto_id,
                prod.sku,
                prod.nombre as producto_nombre,
                u.name as usuario_nombre
            FROM inventario_movimientos mov
            LEFT JOIN inventario prod ON mov.producto_id = prod.id
            LEFT JOIN users u ON mov.usuario_id = u.id
        ";

        if ($producto_id > 0) {
            $query .= " WHERE mov.producto_id = ? ORDER BY mov.id DESC";
            $stmt = $db->prepare($query);
            $stmt->execute([$producto_id]);
            $movimientos = $stmt->fetchAll();
        } else {
            $query .= " ORDER BY mov.id DESC LIMIT 200"; // Limitamos a 200 para no sobrecargar la vista general
            $movimientos = $db->query($query)->fetchAll();
        }

        $this->render('inventario', 'kardex/index', [
            'titulo' => 'Kardex - Historial de Movimientos',
            'movimientos' => $movimientos,
            'producto_id_filtro' => $producto_id
        ]);
    }

    public function verMovimiento() {
        auth_require();
        require_permission('inventario.ver');
        $id = intval($_GET['id'] ?? 0);
        if (!$id) redirect(base_url('inventario/kardex?error=Movimiento no especificado'));

        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                mov.*, 
                prod.sku,
                prod.nombre as producto_nombre,
                u.name as usuario_nombre
            FROM inventario_movimientos mov
            LEFT JOIN inventario prod ON mov.producto_id = prod.id
            LEFT JOIN users u ON mov.usuario_id = u.id
            WHERE mov.id = ?
        ");
        $stmt->execute([$id]);
        $movimiento = $stmt->fetch();

        if (!$movimiento) redirect(base_url('inventario/kardex?error=Movimiento no encontrado'));

        $this->render('inventario', 'kardex/ver', [
            'titulo' => 'Detalle de Movimiento #' . str_pad($movimiento['id'], 5, '0', STR_PAD_LEFT),
            'movimiento' => $movimiento
        ]);
    }
}