<?php

class MarketingController extends Controller {
    public function index() {
        auth_require();
        require_permission('marketing.ver');
        $db = Database::getInstance();
        
        try {
            $campanas = $db->query("SELECT * FROM marketing_campanas ORDER BY id DESC")->fetchAll();
        } catch (\PDOException $e) {
            $campanas = [];
        }
        
        $this->render('marketing', 'index', [
            'titulo' => 'Campañas y Pautas',
            'campanas' => $campanas
        ]);
    }

    public function nuevo() {
        auth_require();
        require_permission('marketing.crear');
        $db = Database::getInstance();
        $promociones = [];
        try {
            $promociones = $db->query("SELECT id, nombre, codigo_cupon FROM promociones WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        } catch (\PDOException $e) {}

        $this->render('marketing', 'nuevo', [
            'titulo' => 'Nueva Regla de Pauta',
            'promociones' => $promociones
        ]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('marketing.crear');
        $nombre = sanitize($_POST['nombre'] ?? '');
        $texto_disparador = sanitize($_POST['texto_disparador'] ?? '');
        $respuesta_automatica = sanitize($_POST['respuesta_automatica'] ?? '');
        $etiqueta_contacto = sanitize($_POST['etiqueta_contacto'] ?? '');
        $promocion_id = !empty($_POST['promocion_id']) ? intval($_POST['promocion_id']) : null;
        $activar_bot = isset($_POST['activar_bot']) ? 1 : 0;

        if (empty($nombre) || empty($texto_disparador)) {
            redirect(base_url('marketing/nuevo?error=El nombre y el texto disparador son obligatorios'));
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("INSERT INTO marketing_campanas (nombre, texto_disparador, respuesta_automatica, etiqueta_contacto, promocion_id, activar_bot) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $texto_disparador, $respuesta_automatica, $etiqueta_contacto, $promocion_id, $activar_bot]);
            redirect(base_url('marketing?success=Regla de pauta creada con éxito'));
        } catch (\PDOException $e) {
            redirect(base_url('marketing/nuevo?error=Error al guardar la campaña en la BD.'));
        }
    }

    public function editar() {
        auth_require();
        require_permission('marketing.crear');
        $id = intval($_GET['id'] ?? 0);
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT * FROM marketing_campanas WHERE id = ?");
        $stmt->execute([$id]);
        $campana = $stmt->fetch();
        
        if (!$campana) redirect(base_url('marketing?error=Campaña no encontrada'));
        
        $promociones = [];
        try {
            $promociones = $db->query("SELECT id, nombre, codigo_cupon FROM promociones WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        } catch (\PDOException $e) {}

        $this->render('marketing', 'editar', [
            'titulo' => 'Editar Regla de Pauta',
            'campana' => $campana,
            'promociones' => $promociones
        ]);
    }

    public function postEditar() {
        auth_require();
        require_permission('marketing.crear');
        $id = intval($_POST['id'] ?? 0);
        $nombre = sanitize($_POST['nombre'] ?? '');
        $texto_disparador = sanitize($_POST['texto_disparador'] ?? '');
        $respuesta_automatica = sanitize($_POST['respuesta_automatica'] ?? '');
        $etiqueta_contacto = sanitize($_POST['etiqueta_contacto'] ?? '');
        $promocion_id = !empty($_POST['promocion_id']) ? intval($_POST['promocion_id']) : null;
        $activar_bot = isset($_POST['activar_bot']) ? 1 : 0;

        if (!$id || empty($nombre) || empty($texto_disparador)) {
            redirect(base_url("marketing/editar?id=$id&error=Faltan datos obligatorios"));
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE marketing_campanas SET nombre=?, texto_disparador=?, respuesta_automatica=?, etiqueta_contacto=?, promocion_id=?, activar_bot=? WHERE id=?");
        $stmt->execute([$nombre, $texto_disparador, $respuesta_automatica, $etiqueta_contacto, $promocion_id, $activar_bot, $id]);
        
        redirect(base_url('marketing?success=Regla actualizada con éxito'));
    }

    public function cambiarEstado() {
        auth_require();
        require_permission('marketing.crear');
        $id = intval($_GET['id'] ?? 0);
        if ($id) { 
            Database::getInstance()->prepare("UPDATE marketing_campanas SET estado = IF(estado = 'activo', 'inactivo', 'activo') WHERE id = ?")->execute([$id]); 
        }
        redirect(base_url('marketing?success=Estado de campaña actualizado'));
    }
}
