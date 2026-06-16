<?php
function config($key, $default = null) {
    static $config = null;
    if ($config === null && file_exists(BASE_PATH . '/config.php')) { $config = require BASE_PATH . '/config.php'; }
    return isset($config[$key]) ? $config[$key] : $default;
}
function redirect($url) { header("Location: " . $url); exit; }
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
function base_url($path = '') {
    $url = config('APP_URL');
    if ($url) return rtrim($url, '/') . '/' . ltrim($path, '/');
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $script = $script === '/' ? '' : $script;
    return $protocol . '://' . $host . $script . '/' . ltrim($path, '/');
}
function is_logged_in() { return isset($_SESSION['user_id']); }
function auth_require() { if (!is_logged_in()) { redirect(base_url('usuarios/login')); } }
function sanitize($input) { return htmlspecialchars(strip_tags(trim($input))); }

function has_permission($permiso) {
    $roles = $_SESSION['roles'] ?? [];
    if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) return true;
    if (in_array(1, $roles)) return true; // Superadministrador
    
    $permisos = $_SESSION['permisos'] ?? [];
    return in_array($permiso, $permisos);
}

function require_permission($permiso) {
    if (!has_permission($permiso)) {
        redirect(base_url('dashboard?error=' . urlencode('Acceso denegado: Se requiere el permiso [' . $permiso . '].')));
    }
}

function enviar_notificacion($usuario_id, $titulo, $mensaje, $enlace = '', $enviar_wa = false) {
    $db = Database::getInstance();
    try {
        $db->prepare("INSERT INTO notificaciones (usuario_id, titulo, mensaje, enlace) VALUES (?, ?, ?, ?)")
           ->execute([$usuario_id, $titulo, $mensaje, $enlace]);
        
        if ($enviar_wa) {
            $stmt = $db->prepare("SELECT whatsapp_id FROM users WHERE id = ? AND whatsapp_id IS NOT NULL AND whatsapp_id != ''");
            $stmt->execute([$usuario_id]);
            if ($wa = $stmt->fetchColumn()) {
                $chA = curl_init('http://localhost:3000/api/enviar');
                curl_setopt($chA, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chA, CURLOPT_POST, true);
                curl_setopt($chA, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($chA, CURLOPT_POSTFIELDS, json_encode(['numero' => $wa, 'mensaje' => "🔔 *$titulo*\n$mensaje\n\nVer detalle: " . base_url($enlace)]));
                curl_exec($chA);
                curl_close($chA);
            }
        }
    } catch (\Exception $e) {}
}

function notificar_rol($role_id, $titulo, $mensaje, $enlace = '', $enviar_wa = false) {
    $db = Database::getInstance();
    try {
        $usuarios = $db->query("SELECT u.id FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id WHERE u.role_id = $role_id OR ur.role_id = $role_id")->fetchAll(PDO::FETCH_COLUMN);
        $usuarios = array_unique($usuarios);
        foreach ($usuarios as $uid) {
            enviar_notificacion($uid, $titulo, $mensaje, $enlace, $enviar_wa);
        }
    } catch (\Exception $e) {}
}
