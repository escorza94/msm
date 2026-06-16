<?php
class Controller {
    protected function render($module, $view, $data = [], $layout = 'main') {
        extract($data);
        $file = MODULES_PATH . "/{$module}/Views/{$view}.php";
        if (file_exists($file)) {
            ob_start();
            require $file;
            $content = ob_get_clean();
            $layoutFile = BASE_PATH . "/layouts/{$layout}.php";
            if (file_exists($layoutFile)) { require $layoutFile; } else { echo $content; }
        } else {
            die("<h2 style='font-family:sans-serif;'>Error 500: Vista no encontrada ($file)</h2>");
        }
    }
}
