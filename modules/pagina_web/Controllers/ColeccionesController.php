<?php

class ColeccionesController extends Controller {
    
    public function index() {
        auth_require();
        require_permission('pagina_web.ver');
        
        $db = Database::getInstance();
        
        try {
            // Traemos las colecciones y contamos cuántos productos tiene cada una
            $colecciones = $db->query("
                SELECT c.*, COUNT(cp.producto_id) as total_productos 
                FROM tienda_colecciones c
                LEFT JOIN tienda_coleccion_productos cp ON c.id = cp.coleccion_id
                GROUP BY c.id
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
        
        $this->render('pagina_web', 'admin/colecciones/nuevo', [
            'titulo' => 'Crear Nueva Colección',
            'productos' => $productos
        ]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('pagina_web.crear');
        
        $nombre = sanitize($_POST['nombre'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        // Generamos un slug amigable para la URL (ej. "Comedores Rústicos" -> "comedores-rusticos")
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre))); 
        $productos_seleccionados = $_POST['productos'] ?? []; // Array de IDs de inventario

        if (empty($nombre)) {
            redirect(base_url('pagina_web/colecciones/nuevo?error=El nombre de la colección es obligatorio'));
        }

        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // 1. Insertamos la Colección
            $stmt = $db->prepare("INSERT INTO tienda_colecciones (nombre, slug, descripcion) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $slug, $descripcion]);
            $coleccion_id = $db->lastInsertId();
            
            // 2. Insertamos la relación con los productos elegidos
            if (!empty($productos_seleccionados) && is_array($productos_seleccionados)) {
                $stmtPivot = $db->prepare("INSERT INTO tienda_coleccion_productos (coleccion_id, producto_id) VALUES (?, ?)");
                foreach ($productos_seleccionados as $prod_id) {
                    $stmtPivot->execute([$coleccion_id, intval($prod_id)]);
                }
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
}