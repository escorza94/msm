<?php

class NotificacionesController extends Controller {
    
    public function index() {
        auth_require();
        $db = Database::getInstance();
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['id'];
        
        // Obtener historial completo para la vista Inbox
        $notificaciones = [];
        try {
            $notificaciones = $db->query("SELECT * FROM notificaciones WHERE usuario_id = $usuario_id ORDER BY id DESC LIMIT 50")->fetchAll();
        } catch (\PDOException $e) {}

        $this->render('notificaciones', 'index', [
            'titulo' => 'Bandeja de Notificaciones',
            'notificaciones' => $notificaciones
        ]);
    }

    // --- Endpoints AJAX para la Campana (Top Bar) ---
    public function obtenerNoLeidas() {
        auth_require();
        $db = Database::getInstance();
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['id'];
        
        try {
            $notificaciones = $db->query("SELECT id, titulo, mensaje, enlace, fecha_creacion FROM notificaciones WHERE usuario_id = $usuario_id AND leida = 0 ORDER BY id DESC LIMIT 10")->fetchAll();
            json_response(['status' => 'success', 'notificaciones' => $notificaciones]);
        } catch (\Exception $e) {
            json_response(['status' => 'error'], 500);
        }
    }

    public function marcarLeida() {
        auth_require();
        $data = json_decode(file_get_contents('php://input'), true);
        $id = intval($data['id'] ?? 0);
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['id'];
        
        if ($id) {
            Database::getInstance()->prepare("UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?")->execute([$id, $usuario_id]);
        } else {
            Database::getInstance()->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_id = ? AND leida = 0")->execute([$usuario_id]);
        }
        
        json_response(['status' => 'success']);
    }

    public function marcarNoLeida() {
        auth_require();
        $data = json_decode(file_get_contents('php://input'), true);
        $id = intval($data['id'] ?? 0);
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['id'];
        
        if ($id) {
            Database::getInstance()->prepare("UPDATE notificaciones SET leida = 0 WHERE id = ? AND usuario_id = ?")->execute([$id, $usuario_id]);
        }
        
        json_response(['status' => 'success']);
    }
}
