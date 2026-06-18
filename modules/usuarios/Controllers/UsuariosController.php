<?php
class UsuariosController extends Controller {
    public function login() {
        if (is_logged_in()) redirect(base_url('admin_core'));
        $error = $_GET['error'] ?? null;
        $this->render('usuarios', 'login', ['titulo' => 'Iniciar Sesión', 'error' => $error], 'blank');
    }

    public function postLogin() {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $db = Database::getInstance();
        
        $count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count == 0) {
            $hash = password_hash('admin', PASSWORD_DEFAULT);
            $db->exec("INSERT INTO users (role_id, name, email, password) VALUES (1, 'Administrador', 'admin@aed.com', '$hash')");
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role_id'] = $user['role_id'];
            
            try {
                $stmtRoles = $db->prepare("SELECT role_id FROM user_roles WHERE user_id = ?");
                $stmtRoles->execute([$user['id']]);
                $userRoles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);
                if (empty($userRoles) && !empty($user['role_id'])) $userRoles = [$user['role_id']];
                $_SESSION['roles'] = $userRoles;
                
                if (!empty($userRoles)) {
                    $in = str_repeat('?,', count($userRoles) - 1) . '?';
                    $stmtPerm = $db->prepare("SELECT DISTINCT p.name FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id WHERE rp.role_id IN ($in)");
                    $stmtPerm->execute($userRoles);
                    $_SESSION['permisos'] = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);
                } else { $_SESSION['permisos'] = []; }
            } catch (\Exception $e) {
                $_SESSION['roles'] = [$user['role_id']];
                $_SESSION['permisos'] = []; 
            }

            redirect(base_url('admin_core'));
        } else {
            redirect(base_url('usuarios/login?error=1'));
        }
    }

    public function logout() {
        session_destroy();
        redirect(base_url('usuarios/login'));
    }
    public function index() {
        auth_require(); // Protege la ruta para usuarios logueados
        require_permission('usuarios.ver');
        
        $db = Database::getInstance();
        
        try {
            $usuarios = $db->query("
                SELECT u.*, 
                       (SELECT GROUP_CONCAT(r.name SEPARATOR ', ') FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = u.id) as roles_nombres
                FROM users u 
                ORDER BY u.id DESC
            ")->fetchAll();
        } catch (\PDOException $e) {
            $usuarios = [];
        }
        
        $this->render('usuarios', 'index', [
            'titulo' => 'Gestión de Usuarios',
            'usuarios' => $usuarios
        ]);
    }

    // --- CRUD ADMINISTRATIVO ---
    
    public function nuevo() {
        auth_require();
        require_permission('usuarios.ver');
        $db = Database::getInstance();
        $contactos_wa = [];
        try {
            $contactos_wa = $db->query("SELECT whatsapp_id, nombre FROM wa_contactos WHERE tipo_chat = 'individual' ORDER BY nombre ASC")->fetchAll();
        } catch (\Exception $e) {}
        
        $roles_db = [];
        try {
            $roles_db = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();
        } catch (\Exception $e) {}

        $error = $_GET['error'] ?? null;
        $this->render('usuarios', 'nuevo', ['titulo' => 'Nuevo Usuario', 'error' => $error, 'contactos_wa' => $contactos_wa, 'roles_db' => $roles_db]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('usuarios.ver');
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $whatsapp_id = sanitize($_POST['whatsapp_id'] ?? null);
        $roles_asignados = isset($_POST['roles']) && is_array($_POST['roles']) ? $_POST['roles'] : [];

        if(empty($name) || empty($email) || empty($password)) {
            redirect(base_url('usuarios/nuevo?error=Campos vacíos'));
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) { redirect(base_url('usuarios/nuevo?error=El correo ya está registrado')); }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $main_role = !empty($roles_asignados) ? intval($roles_asignados[0]) : 2;

        try {
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT INTO users (role_id, name, email, password, whatsapp_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$main_role, $name, $email, $hash, $whatsapp_id]);
            $nuevo_id = $db->lastInsertId();
            
            if (!empty($roles_asignados)) {
                $stmtRoles = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                foreach ($roles_asignados as $r_id) {
                    $stmtRoles->execute([$nuevo_id, intval($r_id)]);
                }
            }
            
            $db->commit();
            redirect(base_url('usuarios?success=Usuario creado exitosamente.'));
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            redirect(base_url('usuarios/nuevo?error=Error al crear usuario'));
        }
    }

    public function editar() {
        auth_require();
        require_permission('usuarios.ver');
        $id = intval($_GET['id'] ?? 0);
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        if(!$usuario = $stmt->fetch()) redirect(base_url('usuarios?error=Usuario no encontrado'));
        
        $contactos_wa = [];
        try {
            $contactos_wa = $db->query("SELECT whatsapp_id, nombre FROM wa_contactos WHERE tipo_chat = 'individual' ORDER BY nombre ASC")->fetchAll();
        } catch (\Exception $e) {}
        
        $roles_db = [];
        $user_roles = [];
        try {
            $roles_db = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();
            $stmtR = $db->prepare("SELECT role_id FROM user_roles WHERE user_id = ?"); $stmtR->execute([$id]);
            $user_roles = $stmtR->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {}
        if (empty($user_roles) && !empty($usuario['role_id'])) $user_roles = [$usuario['role_id']];

        $error = $_GET['error'] ?? null;
        $this->render('usuarios', 'editar', ['titulo' => 'Editar Usuario', 'usuario' => $usuario, 'error' => $error, 'contactos_wa' => $contactos_wa, 'roles_db' => $roles_db, 'user_roles' => $user_roles]);
    }

    public function postEditar() {
        auth_require();
        require_permission('usuarios.ver');
        $id = intval($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $whatsapp_id = sanitize($_POST['whatsapp_id'] ?? null);
        $roles_asignados = isset($_POST['roles']) && is_array($_POST['roles']) ? $_POST['roles'] : [];

        if(empty($name) || empty($email)) redirect(base_url("usuarios/editar?id=$id&error=El nombre y correo son obligatorios"));

        $db = Database::getInstance();
        $main_role = !empty($roles_asignados) ? intval($roles_asignados[0]) : 2;

        try {
            $db->beginTransaction();
            if(!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET role_id = ?, name = ?, email = ?, password = ?, whatsapp_id = ? WHERE id = ?")->execute([$main_role, $name, $email, $hash, $whatsapp_id, $id]);
            } else {
                $db->prepare("UPDATE users SET role_id = ?, name = ?, email = ?, whatsapp_id = ? WHERE id = ?")->execute([$main_role, $name, $email, $whatsapp_id, $id]);
            }
            
            $db->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$id]);
            if (!empty($roles_asignados)) {
                $stmtRoles = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                foreach ($roles_asignados as $r_id) {
                    $stmtRoles->execute([$id, intval($r_id)]);
                }
            }
            
            $db->commit();
            redirect(base_url('usuarios?success=Usuario actualizado exitosamente'));
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            redirect(base_url("usuarios/editar?id=$id&error=Error al actualizar usuario"));
        }
    }

    public function registro() {
        if (is_logged_in()) redirect(base_url('admin_core'));
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;
        $this->render('usuarios', 'registro', ['titulo' => 'Crear Cuenta', 'error' => $error, 'success' => $success], 'blank');
    }

    public function postRegistro() {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role_id = $_POST['role_id'] ?? 2; 
        $whatsapp_id = sanitize($_POST['whatsapp_id'] ?? null);

        if(empty($name) || empty($email) || empty($password)) {
            redirect(base_url('usuarios/registro?error=Campos vacíos'));
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) {
            redirect(base_url('usuarios/registro?error=El correo ya está registrado'));
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (role_id, name, email, password, whatsapp_id) VALUES (?, ?, ?, ?, ?)");
        if($stmt->execute([$role_id, $name, $email, $hash, $whatsapp_id])) {
            $nuevo_id = $db->lastInsertId();
            try { $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$nuevo_id, $role_id]); } catch (\Exception $e) {}
            redirect(base_url('usuarios/registro?success=Cuenta creada exitosamente. Inicia sesión.'));
        } else {
            redirect(base_url('usuarios/registro?error=Error al crear cuenta'));
        }
    }

    public function configuracion() {
        auth_require();
        require_permission('usuarios.ver');
        $this->render('usuarios', 'configuracion', ['titulo' => 'Ajustes de Usuarios']);
    }

    public function perfil() {
        auth_require();
        $db = Database::getInstance();
        
        $id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'];
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        
        $contactos_wa = [];
        try {
            $contactos_wa = $db->query("SELECT whatsapp_id, nombre FROM wa_contactos WHERE tipo_chat = 'individual' ORDER BY nombre ASC")->fetchAll();
        } catch (\Exception $e) {}

        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;

        $this->render('usuarios', 'perfil', ['titulo' => 'Mi Perfil', 'usuario' => $usuario, 'contactos_wa' => $contactos_wa, 'error' => $error, 'success' => $success]);
    }

    public function postPerfil() {
        auth_require();
        $id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'];
        
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $whatsapp_id = sanitize($_POST['whatsapp_id'] ?? null);

        if(empty($name) || empty($email)) {
            redirect(base_url('usuarios/perfil?error=El nombre y el correo son obligatorios'));
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if($stmt->fetch()) {
            redirect(base_url('usuarios/perfil?error=El correo ya está en uso por otra cuenta'));
        }

        if(!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db->prepare("UPDATE users SET name = ?, email = ?, password = ?, whatsapp_id = ? WHERE id = ?")->execute([$name, $email, $hash, $whatsapp_id, $id]);
        } else {
            $db->prepare("UPDATE users SET name = ?, email = ?, whatsapp_id = ? WHERE id = ?")->execute([$name, $email, $whatsapp_id, $id]);
        }
        
        $_SESSION['user_name'] = $name; // Actualizamos el nombre en sesión
        redirect(base_url('usuarios/perfil?success=Perfil actualizado exitosamente'));
    }
}
