<?php

class RolesController extends Controller {
    public function index() {
        auth_require();
        // Solo administradores
        if ($_SESSION['role_id'] != 1 && !has_permission('roles.ver')) {
            redirect(base_url('dashboard?error=' . urlencode('Acceso denegado a Roles.')));
        }

        $db = Database::getInstance();
        $roles = [];
        try {
            $roles = $db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
        } catch (\PDOException $e) {}
        
        $this->render('usuarios', 'roles_index', [
            'titulo' => 'Matriz de Permisos (RBAC)',
            'roles' => $roles
        ]);
    }

    public function editar() {
        auth_require();
        if ($_SESSION['role_id'] != 1 && !has_permission('roles.editar')) {
            redirect(base_url('dashboard?error=' . urlencode('Acceso denegado a Permisos.')));
        }

        $id = intval($_GET['id'] ?? 0);
        $db = Database::getInstance();
        
        $rol = $db->prepare("SELECT * FROM roles WHERE id = ?");
        $rol->execute([$id]);
        $rol = $rol->fetch();
        
        if (!$rol) redirect(base_url('usuarios/roles?error=Rol no encontrado'));

        $permisos = [];
        $role_permissions = [];
        try {
            $permisos_raw = $db->query("SELECT * FROM permissions ORDER BY name ASC")->fetchAll();
            
            // 1. Limpiar duplicados por si la BD no tiene restricción UNIQUE
            $permisos_unicos = [];
            foreach ($permisos_raw as $p) {
                if (!isset($permisos_unicos[$p['name']])) {
                    $permisos_unicos[$p['name']] = $p;
                }
            }
            $permisos = array_values($permisos_unicos);

            $stmtRp = $db->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
            $stmtRp->execute([$id]);
            $role_permissions_raw = $stmtRp->fetchAll(PDO::FETCH_COLUMN);
            
            // 2. Asegurar que si el rol tenía un ID duplicado, se marque correctamente en la vista
            foreach ($permisos_raw as $p) {
                if (in_array($p['id'], $role_permissions_raw)) {
                    $role_permissions[] = $permisos_unicos[$p['name']]['id'];
                }
            }
            $role_permissions = array_unique($role_permissions);
        } catch (\PDOException $e) {}

        // Agrupar permisos por prefijo (ej: "pos.ver" -> "Pos")
        $permisosAgrupados = [];
        foreach ($permisos as $p) {
            $partes = explode('.', $p['name']);
            $grupo = ucfirst($partes[0] ?? 'Generales');
            $permisosAgrupados[$grupo][] = $p;
        }

        $this->render('usuarios', 'roles_editar', ['titulo' => 'Editar Permisos: ' . htmlspecialchars($rol['name']), 'rol' => $rol, 'permisosAgrupados' => $permisosAgrupados, 'role_permissions' => $role_permissions]);
    }

    public function guardar() {
        auth_require();
        if ($_SESSION['role_id'] != 1 && !has_permission('roles.editar')) redirect(base_url('dashboard?error=Acceso denegado.'));

        $role_id = intval($_POST['role_id'] ?? 0);
        $permisosSeleccionados = $_POST['permisos'] ?? [];

        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $db->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$role_id]);
            if (!empty($permisosSeleccionados)) { $stmt = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)"); foreach ($permisosSeleccionados as $perm_id) { $stmt->execute([$role_id, intval($perm_id)]); } }
            $db->commit();
            // Si se editó su propio rol, actualizar la sesión en tiempo real
            if ($_SESSION['role_id'] == $role_id) { $stmtPerm = $db->prepare("SELECT p.name FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id WHERE rp.role_id = ?"); $stmtPerm->execute([$role_id]); $_SESSION['permisos'] = $stmtPerm->fetchAll(PDO::FETCH_COLUMN); }
            redirect(base_url('usuarios/roles?success=Permisos actualizados correctamente.'));
        } catch (\Exception $e) { if ($db->inTransaction()) $db->rollBack(); redirect(base_url('usuarios/roles/editar?id='.$role_id.'&error=Error al guardar permisos.')); }
    }

    public function guardarPermiso() {
        auth_require();
        if ($_SESSION['role_id'] != 1) redirect(base_url('dashboard?error=Solo el SuperAdmin puede crear permisos.'));

        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');

        if (empty($name)) redirect(base_url('usuarios/roles?error=El nombre del permiso es obligatorio'));

        $db = Database::getInstance();
        try {
            // Verificar si ya existe para evitar duplicados manualmente
            $stmtCheck = $db->prepare("SELECT id FROM permissions WHERE name = ?");
            $stmtCheck->execute([$name]);
            if ($stmtCheck->fetch()) {
                redirect(base_url('usuarios/roles?error=El permiso ya existe.'));
            }
            
            $db->prepare("INSERT INTO permissions (name, description) VALUES (?, ?)")->execute([$name, $description]);
            redirect(base_url('usuarios/roles?success=Permiso creado correctamente.'));
        } catch (\PDOException $e) {
            redirect(base_url('usuarios/roles?error=Error al crear el permiso.'));
        }
    }

    public function guardarRol() {
        auth_require();
        if ($_SESSION['role_id'] != 1) redirect(base_url('dashboard?error=Solo el SuperAdmin puede gestionar roles.'));

        $id = intval($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');

        if (empty($name)) redirect(base_url('usuarios/roles?error=El nombre del rol es obligatorio'));

        $db = Database::getInstance();
        try {
            if ($id > 0) {
                $db->prepare("UPDATE roles SET name = ?, description = ? WHERE id = ?")->execute([$name, $description, $id]);
                $msg = "Rol actualizado correctamente";
            } else {
                $db->prepare("INSERT INTO roles (name, description) VALUES (?, ?)")->execute([$name, $description]);
                $msg = "Nuevo Rol creado";
            }
            redirect(base_url('usuarios/roles?success=' . urlencode($msg)));
        } catch (\PDOException $e) {
            redirect(base_url('usuarios/roles?error=Error al guardar el rol.'));
        }
    }

    public function eliminarRol() {
        auth_require();
        if ($_SESSION['role_id'] != 1) redirect(base_url('dashboard?error=Acceso denegado.'));
        $id = intval($_GET['id'] ?? 0);
        if ($id > 1) { // Prevenir borrar el rol SuperAdmin
            Database::getInstance()->prepare("DELETE FROM roles WHERE id = ?")->execute([$id]);
        }
        redirect(base_url('usuarios/roles?success=Rol eliminado'));
    }

    public function sincronizarPermisos() {
        auth_require();
        if ($_SESSION['role_id'] != 1) redirect(base_url('dashboard?error=Acceso denegado.'));

        $db = Database::getInstance();
        $nuevos = 0;

        if (is_dir(MODULES_PATH)) {
            foreach (scandir(MODULES_PATH) as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $configPath = MODULES_PATH . '/' . $dir . '/config.json';
                    if (file_exists($configPath)) {
                        $config = json_decode(file_get_contents($configPath), true);
                        if (isset($config['permissions']) && is_array($config['permissions'])) {
                            foreach ($config['permissions'] as $p) {
                                $name = sanitize($p['name'] ?? ''); $desc = sanitize($p['description'] ?? '');
                                if ($name) { 
                                    try { 
                                        $stmtCheck = $db->prepare("SELECT id FROM permissions WHERE name = ?");
                                        $stmtCheck->execute([$name]);
                                        if (!$stmtCheck->fetch()) {
                                            $db->prepare("INSERT INTO permissions (name, description) VALUES (?, ?)")->execute([$name, $desc]); 
                                            $nuevos++; 
                                        }
                                    } catch (\Exception $e) {} 
                                }
                            }
                        }
                    }
                }
            }
        }
        redirect(base_url('usuarios/roles?success=Sincronización completa. ' . $nuevos . ' permisos nuevos agregados.'));
    }
}