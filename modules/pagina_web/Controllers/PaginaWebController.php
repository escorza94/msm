<?php

class PaginaWebController extends Controller {
    public function index() {
        auth_require();
        require_permission('pagina_web.ver');
        $this->render('pagina_web', 'index', ['titulo' => 'Panel de Tienda Online']);
    }
}