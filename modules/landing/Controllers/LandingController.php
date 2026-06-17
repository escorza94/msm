<?php

class LandingController extends Controller {
    public function index() {
        // auth_require();
        $data = ['titulo' => 'Módulo landing'];
        $this->render('landing', 'index', $data);
    }
}
