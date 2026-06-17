<?php
class PaginasController extends Controller {
    public function index() {
        auth_require(); require_permission('pagina_web.ver');
        $db = Database::getInstance();
        $paginas = $db->query("SELECT * FROM tienda_paginas ORDER BY id ASC")->fetchAll();
        $this->render('pagina_web', 'admin/paginas/index', ['titulo' => 'Gestor de Páginas', 'paginas' => $paginas]);
    }

    public function guardar() {
        auth_require(); require_permission('pagina_web.crear');
        $db = Database::getInstance();
        $id = intval($_POST['id'] ?? 0);
        $titulo = sanitize($_POST['titulo'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        if(empty($slug)) $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
        
        try {
            if ($id > 0) { $db->prepare("UPDATE tienda_paginas SET titulo=?, slug=? WHERE id=?")->execute([$titulo, $slug, $id]); } 
            else { $db->prepare("INSERT INTO tienda_paginas (titulo, slug) VALUES (?, ?)")->execute([$titulo, $slug]); }
            redirect(base_url('pagina_web/paginas?success=Página guardada'));
        } catch (\Exception $e) {
            redirect(base_url('pagina_web/paginas?error=Error al guardar. El slug podría estar repetido.'));
        }
    }

    public function eliminar() {
        auth_require(); require_permission('pagina_web.crear');
        $id = intval($_GET['id'] ?? 0);
        if ($id === 1) redirect(base_url('pagina_web/paginas?error=No puedes eliminar la página principal (Inicio)'));
        Database::getInstance()->prepare("DELETE FROM tienda_paginas WHERE id=?")->execute([$id]);
        redirect(base_url('pagina_web/paginas?success=Página eliminada'));
    }

    public function cambiarEstado() {
        auth_require(); require_permission('pagina_web.crear');
        $id = intval($_GET['id'] ?? 0);
        if ($id) Database::getInstance()->prepare("UPDATE tienda_paginas SET estado = IF(estado = 'publicado', 'borrador', 'publicado') WHERE id = ?")->execute([$id]);
        redirect(base_url('pagina_web/paginas?success=Estado actualizado'));
    }
}