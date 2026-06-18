<?php

class CategoriasController extends Controller {
    
    public function index() {
        auth_require();
        require_permission('inventario.ver');
        
        $db = Database::getInstance();
        $categorias = $db->query("SELECT * FROM inventario_categorias ORDER BY nombre ASC")->fetchAll();
        
        $this->render('inventario', 'categorias/index', [
            'titulo' => 'Gestión de Categorías',
            'categorias' => $categorias
        ]);
    }

    public function guardar() {
        auth_require();
        require_permission('inventario.crear');
        
        $id = intval($_POST['id'] ?? 0);
        $nombre = sanitize($_POST['nombre'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        
        if (empty($nombre)) {
            redirect(base_url('inventario/categorias?error=El nombre es obligatorio'));
        }

        $db = Database::getInstance();
        try {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE inventario_categorias SET nombre = ?, descripcion = ? WHERE id = ?");
                $stmt->execute([$nombre, $descripcion, $id]);
                $mensaje = "Categoría actualizada correctamente.";
            } else {
                $stmt = $db->prepare("INSERT INTO inventario_categorias (nombre, descripcion) VALUES (?, ?)");
                $stmt->execute([$nombre, $descripcion]);
                $mensaje = "Categoría creada correctamente.";
            }
            redirect(base_url("inventario/categorias?success=" . urlencode($mensaje)));
        } catch (\PDOException $e) {
            redirect(base_url('inventario/categorias?error=Error al guardar. Es posible que el nombre ya exista.'));
        }
    }

    public function eliminar() {
        auth_require();
        require_permission('inventario.crear');
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id) {
            $db = Database::getInstance();
            // Comprobar si hay productos usando esta categoría antes de eliminar
            $enUso = $db->query("SELECT COUNT(*) FROM inventario WHERE categoria_id = $id")->fetchColumn();
            
            if ($enUso > 0) {
                redirect(base_url('inventario/categorias?error=No se puede eliminar la categoría porque tiene productos asignados.'));
            } else {
                try {
                    $db->prepare("DELETE FROM inventario_categorias WHERE id = ?")->execute([$id]);
                    redirect(base_url('inventario/categorias?success=Categoría eliminada con éxito.'));
                } catch (\PDOException $e) {
                    redirect(base_url('inventario/categorias?error=Error de base de datos al eliminar.'));
                }
            }
        } else {
            redirect(base_url('inventario/categorias?error=ID no válido.'));
        }
    }
}