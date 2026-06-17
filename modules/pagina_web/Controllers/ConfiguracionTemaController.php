<?php

class ConfiguracionTemaController extends Controller {
    public function index() {
        auth_require();
        require_permission('pagina_web.ver');
        
        $db = Database::getInstance();
        $config_rows = $db->query("SELECT clave, valor FROM tienda_tema_config")->fetchAll();
        $config = [];
        foreach ($config_rows as $row) {
            $config[$row['clave']] = $row['valor'];
        }
        
        $paginas = $db->query("SELECT titulo, slug FROM tienda_paginas WHERE estado = 'publicado'")->fetchAll();

        $this->render('pagina_web', 'admin/configuracion/index', [
            'titulo' => 'Configuración del Tema y SEO',
            'config' => $config,
            'paginas' => $paginas
        ]);
    }

    public function guardar() {
        auth_require();
        require_permission('pagina_web.crear');
        
        $db = Database::getInstance();
        $campos = ['nombre_empresa', 'color_primario', 'seo_titulo', 'seo_descripcion', 'whatsapp_numero', 'facebook_url', 'instagram_url', 'tiktok_url', 'footer_texto'];
        
        // Procesar Enlaces del Menú
        $titulos_menu = $_POST['menu_titulo'] ?? [];
        $enlaces_menu = $_POST['menu_enlace'] ?? [];
        $menu_items = [];
        for ($i = 0; $i < count($titulos_menu); $i++) {
            if (!empty($titulos_menu[$i])) {
                $menu_items[] = ['titulo' => sanitize($titulos_menu[$i]), 'enlace' => sanitize($enlaces_menu[$i])];
            }
        }
        $menu_json = json_encode($menu_items, JSON_UNESCAPED_UNICODE);
        
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT INTO tienda_tema_config (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
            foreach ($campos as $clave) { if (isset($_POST[$clave])) { $stmt->execute([$clave, sanitize($_POST[$clave])]); } }
            $stmt->execute(['menu_enlaces', $menu_json]);
            $db->commit();
            redirect(base_url('pagina_web/configuracion?success=Configuración guardada correctamente'));
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            redirect(base_url('pagina_web/configuracion?error=Error al guardar la configuración'));
        }
    }
}