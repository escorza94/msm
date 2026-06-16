<?php

class InventarioController extends Controller {
    public function index() {
        auth_require(); // Proteger ruta
        
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
}