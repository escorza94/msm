<?php

class ColeccionesController extends Controller {
    
    public function index() {
        auth_require();
        require_permission('pagina_web.ver');
        
        $db = Database::getInstance();
        
        try {
            // Traemos las colecciones y contamos cuántos productos tiene cada una
            // MODIFICACIÓN: Ahora solo cuenta productos activos y con stock para evitar confusiones.
            $colecciones = $db->query("
                SELECT c.id, c.nombre, c.slug, c.estado, c.descripcion, c.tipo,
                       COUNT(DISTINCT i.id) as total_productos_visibles,
                       COUNT(DISTINCT cpro.promocion_id) as total_promociones
                FROM tienda_colecciones c
                LEFT JOIN tienda_coleccion_productos cp ON c.id = cp.coleccion_id AND c.tipo = 'productos'
                LEFT JOIN inventario i ON cp.producto_id = i.id AND i.estado = 'activo' AND i.stock > 0
                LEFT JOIN tienda_coleccion_promociones cpro ON c.id = cpro.coleccion_id AND c.tipo = 'promociones'
                GROUP BY c.id, c.nombre, c.slug, c.estado, c.descripcion, c.tipo
                ORDER BY c.id DESC
            ")->fetchAll();
        } catch (\PDOException $e) {
            $colecciones = [];
        }
        
        $this->render('pagina_web', 'admin/colecciones/index', [
            'titulo' => 'Colecciones de Productos',
            'colecciones' => $colecciones
        ]);
    }

    public function nuevo() {
        auth_require();
        require_permission('pagina_web.crear');
        
        $db = Database::getInstance();
        // Traemos los productos activos para que el usuario pueda seleccionarlos
        $productos = $db->query("SELECT id, nombre, sku, precio FROM inventario WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        $promociones = $db->query("SELECT id, nombre, tipo, valor FROM promociones WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        
        $this->render('pagina_web', 'admin/colecciones/nuevo', [
            'titulo' => 'Crear Nueva Colección',
            'productos' => $productos,
            'promociones' => $promociones
        ]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('pagina_web.crear');
        
        $nombre = sanitize($_POST['nombre'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $tipo = sanitize($_POST['tipo'] ?? 'productos');
        // Generamos un slug amigable para la URL (ej. "Comedores Rústicos" -> "comedores-rusticos")
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre))); 
        $productos_seleccionados = $_POST['productos'] ?? []; // Array de IDs de inventario
        $promociones_seleccionadas = $_POST['promociones'] ?? []; 

        if (empty($nombre)) {
            redirect(base_url('pagina_web/colecciones/nuevo?error=El nombre de la colección es obligatorio'));
        }

        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // 1. Insertamos la Colección
            $stmt = $db->prepare("INSERT INTO tienda_colecciones (nombre, slug, descripcion, tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $slug, $descripcion, $tipo]);
            $coleccion_id = $db->lastInsertId();
            
            // 2. Insertamos la relación dependiendo del tipo
            if ($tipo === 'productos' && !empty($productos_seleccionados) && is_array($productos_seleccionados)) {
                $stmtPivot = $db->prepare("INSERT INTO tienda_coleccion_productos (coleccion_id, producto_id) VALUES (?, ?)");
                foreach ($productos_seleccionados as $prod_id) { $stmtPivot->execute([$coleccion_id, intval($prod_id)]); }
            } elseif ($tipo === 'promociones' && !empty($promociones_seleccionadas) && is_array($promociones_seleccionadas)) {
                $stmtPivot = $db->prepare("INSERT INTO tienda_coleccion_promociones (coleccion_id, promocion_id) VALUES (?, ?)");
                foreach ($promociones_seleccionadas as $promo_id) { $stmtPivot->execute([$coleccion_id, intval($promo_id)]); }
            }
            
            $db->commit();
            redirect(base_url('pagina_web/colecciones?success=Colección creada con éxito'));
            
        } catch (\PDOException $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            redirect(base_url('pagina_web/colecciones/nuevo?error=Error al guardar. Verifica que el nombre no esté repetido.'));
        }
    }
    
    public function cambiarEstado() {
        auth_require();
        require_permission('pagina_web.crear');
        $id = intval($_GET['id'] ?? 0);
        
        if ($id) {
            $db = Database::getInstance();
            $db->prepare("UPDATE tienda_colecciones SET estado = IF(estado = 'activo', 'inactivo', 'activo') WHERE id = ?")->execute([$id]);
        }
        redirect(base_url('pagina_web/colecciones?success=Estado actualizado'));
    }

    public function editar() {
        auth_require();
        require_permission('pagina_web.crear');
        $id = intval($_GET['id'] ?? 0);
        $db = Database::getInstance();
        $coleccion = $db->query("SELECT * FROM tienda_colecciones WHERE id = $id")->fetch();
        if (!$coleccion) redirect(base_url('pagina_web/colecciones?error=Colección no encontrada'));

        $productos = $db->query("SELECT id, nombre, sku, precio FROM inventario WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        $productos_seleccionados = $db->query("SELECT producto_id FROM tienda_coleccion_productos WHERE coleccion_id = $id")->fetchAll(PDO::FETCH_COLUMN);
        
        $promociones = $db->query("SELECT id, nombre, tipo, valor FROM promociones WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        $promociones_seleccionadas = $db->query("SELECT promocion_id FROM tienda_coleccion_promociones WHERE coleccion_id = $id")->fetchAll(PDO::FETCH_COLUMN);

        $this->render('pagina_web', 'admin/colecciones/editar', [
            'titulo' => 'Editar Colección',
            'coleccion' => $coleccion,
            'productos' => $productos,
            'productos_seleccionados' => $productos_seleccionados,
            'promociones' => $promociones,
            'promociones_seleccionadas' => $promociones_seleccionadas
        ]);
    }

    public function postEditar() {
        auth_require();
        require_permission('pagina_web.crear');
        $id = intval($_POST['id'] ?? 0);
        $nombre = sanitize($_POST['nombre'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        if(empty($slug)) $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre))); 
        $tipo = sanitize($_POST['tipo'] ?? 'productos');
        $productos_seleccionados = $_POST['productos'] ?? [];
        $promociones_seleccionadas = $_POST['promociones'] ?? [];

        if (!$id || empty($nombre)) redirect(base_url("pagina_web/colecciones/editar?id=$id&error=El nombre es obligatorio"));

        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $db->prepare("UPDATE tienda_colecciones SET nombre=?, slug=?, descripcion=?, tipo=? WHERE id=?")->execute([$nombre, $slug, $descripcion, $tipo, $id]);
            
            $db->prepare("DELETE FROM tienda_coleccion_productos WHERE coleccion_id=?")->execute([$id]);
            $db->prepare("DELETE FROM tienda_coleccion_promociones WHERE coleccion_id=?")->execute([$id]);
            
            if ($tipo === 'productos' && !empty($productos_seleccionados) && is_array($productos_seleccionados)) {
                $stmtPivot = $db->prepare("INSERT INTO tienda_coleccion_productos (coleccion_id, producto_id) VALUES (?, ?)");
                foreach ($productos_seleccionados as $prod_id) { $stmtPivot->execute([$id, intval($prod_id)]); }
            } elseif ($tipo === 'promociones' && !empty($promociones_seleccionadas) && is_array($promociones_seleccionadas)) {
                $stmtPivot = $db->prepare("INSERT INTO tienda_coleccion_promociones (coleccion_id, promocion_id) VALUES (?, ?)");
                foreach ($promociones_seleccionadas as $promo_id) { $stmtPivot->execute([$id, intval($promo_id)]); }
            }
            $db->commit();
            redirect(base_url('pagina_web/colecciones?success=Colección actualizada'));
        } catch (\PDOException $e) {
            if ($db->inTransaction()) $db->rollBack();
            redirect(base_url("pagina_web/colecciones/editar?id=$id&error=Error al guardar. Verifica que el slug no esté repetido."));
        }
    }

    public function eliminar() {
        auth_require();
        require_permission('pagina_web.crear');
        $id = intval($_GET['id'] ?? 0);
        if ($id === 1) redirect(base_url('pagina_web/colecciones?error=No puedes eliminar la colección principal (Destacados)'));
        Database::getInstance()->prepare("DELETE FROM tienda_colecciones WHERE id=?")->execute([$id]);
        redirect(base_url('pagina_web/colecciones?success=Colección eliminada'));
    }
}