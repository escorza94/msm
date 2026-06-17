<?php

class Pagina_webController extends Controller {
    public function index() {
        auth_require();
        require_permission('pagina_web.ver');
        $data = ['titulo' => 'Panel Tienda Online (CMS)'];
        $this->render('pagina_web', 'index', $data);
    }
}
