<?php

class ProveedoresController extends Controller {
    public function index() {
        auth_require();
        require_permission('proveedores.ver');
        
        $db = Database::getInstance();
        try {
            $proveedores = $db->query("SELECT * FROM inventario_proveedores ORDER BY id DESC")->fetchAll();
        } catch (\PDOException $e) {
            $proveedores = [];
        }
        
        $this->render('proveedores', 'index', [
            'titulo' => 'Directorio de Proveedores',
            'proveedores' => $proveedores
        ]);
    }

    public function nuevo() {
        auth_require();
        require_permission('proveedores.crear');
        $error = $_GET['error'] ?? null;
        $this->render('proveedores', 'nuevo', [
            'titulo' => 'Nuevo Proveedor',
            'error' => $error
        ]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('proveedores.crear');
        
        $nombre = sanitize($_POST['nombre'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $direccion = sanitize($_POST['direccion'] ?? '');

        if (empty($nombre)) {
            redirect(base_url('proveedores/nuevo?error=El nombre de la empresa es obligatorio'));
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO inventario_proveedores (nombre, telefono, email, direccion) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $telefono, $email, $direccion])) {
            redirect(base_url('proveedores?success=Proveedor registrado correctamente'));
        } else {
            redirect(base_url('proveedores/nuevo?error=Error al guardar en la base de datos'));
        }
    }

    public function editar() {
        auth_require();
        require_permission('proveedores.crear');
        $id = intval($_GET['id'] ?? 0);
        $error = $_GET['error'] ?? null;
        
        if (!$id) redirect(base_url('proveedores?error=Proveedor no especificado'));
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM inventario_proveedores WHERE id = ?");
        $stmt->execute([$id]);
        $proveedor = $stmt->fetch();
        
        if (!$proveedor) redirect(base_url('proveedores?error=Proveedor no encontrado'));
        
        $this->render('proveedores', 'editar', [
            'titulo' => 'Editar Proveedor',
            'proveedor' => $proveedor,
            'error' => $error
        ]);
    }

    public function postEditar() {
        auth_require();
        require_permission('proveedores.crear');
        
        $id = intval($_POST['id'] ?? 0);
        if (!$id) redirect(base_url('proveedores?error=Proveedor no especificado'));

        $nombre = sanitize($_POST['nombre'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $direccion = sanitize($_POST['direccion'] ?? '');

        if (empty($nombre)) {
            redirect(base_url("proveedores/editar?id=$id&error=El nombre de la empresa es obligatorio"));
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE inventario_proveedores SET nombre = ?, telefono = ?, email = ?, direccion = ? WHERE id = ?");
        if ($stmt->execute([$nombre, $telefono, $email, $direccion, $id])) {
            redirect(base_url('proveedores?success=Proveedor actualizado correctamente'));
        } else {
            redirect(base_url("proveedores/editar?id=$id&error=Error al actualizar en la base de datos"));
        }
    }
}
