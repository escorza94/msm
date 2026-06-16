<?php

class AjustesController extends Controller {
    public function index() {
        auth_require();
        require_permission('admin_core.ver');
        $config = require BASE_PATH . '/config.php';
        $success = $_GET['success'] ?? null;
        
        $this->render('admin_core', 'ajustes', [
            'titulo' => 'Ajustes Globales del Sistema',
            'config' => $config,
            'success' => $success
        ]);
    }

    public function guardar() {
        auth_require();
        require_permission('admin_core.ver');
        $config = require BASE_PATH . '/config.php';
        
        $config['APP_NAME'] = sanitize($_POST['app_name'] ?? $config['APP_NAME'] ?? 'Mueblería San Martín');
        $config['THEME_SIDEBAR_BG'] = sanitize($_POST['theme_sidebar_bg'] ?? $config['THEME_SIDEBAR_BG'] ?? '#111827');
        $config['THEME_PRIMARY'] = sanitize($_POST['theme_primary'] ?? $config['THEME_PRIMARY'] ?? '#4f46e5');
        
        $storage_dir = BASE_PATH . '/storage/assets';
        if (!is_dir($storage_dir)) @mkdir($storage_dir, 0777, true);

        if (!empty($_FILES['logo_file']['name']) && $_FILES['logo_file']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
                $filename = uniqid('logo_') . '.' . $ext;
                if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $storage_dir . '/' . $filename)) {
                    if (!empty($config['BUSINESS_LOGO']) && strpos($config['BUSINESS_LOGO'], 'storage/assets/') === 0) {
                        $old_path = BASE_PATH . '/' . $config['BUSINESS_LOGO'];
                        if (file_exists($old_path)) @unlink($old_path);
                    }
                    $config['BUSINESS_LOGO'] = 'storage/assets/' . $filename;
                }
            }
        } else {
            $config['BUSINESS_LOGO'] = sanitize($_POST['business_logo'] ?? $config['BUSINESS_LOGO'] ?? '');
        }

        $config['GOOGLE_MAPS_API_KEY'] = sanitize($_POST['google_maps_api_key'] ?? $config['GOOGLE_MAPS_API_KEY']);
        $config['GEMINI_API_KEY'] = sanitize($_POST['gemini_api_key'] ?? $config['GEMINI_API_KEY']);
        $config['GEMINI_MODEL'] = sanitize($_POST['gemini_model'] ?? $config['GEMINI_MODEL'] ?? 'gemini-1.5-flash');

        $contenido = "<?php\nreturn [\n";
        foreach ($config as $key => $value) {
            $safeValue = str_replace("'", "\\'", $value);
            $contenido .= "    '$key' => '$safeValue',\n";
        }
        $contenido .= "];\n";
        file_put_contents(BASE_PATH . '/config.php', $contenido);
        redirect(base_url('admin_core/ajustes?success=Ajustes globales actualizados correctamente'));
    }
}