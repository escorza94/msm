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

    /**
     * Renderiza una vista y la devuelve como una cadena de texto (string).
     * Ideal para componentes AJAX o para inyectar HTML en otras vistas.
     *
     * @param string $module El nombre del módulo donde está la vista.
     * @param string $view La ruta de la vista dentro de la carpeta Views del módulo.
     * @param array $data Datos para pasar a la vista.
     * @return string El HTML renderizado.
     */
    protected function renderToString($module, $view, $data = []) {
        extract($data);
        ob_start();
        $file = MODULES_PATH . "/{$module}/Views/{$view}.php";
        if (file_exists($file)) { require $file; }
        // No se usa die() para no cortar la ejecución en un hook
        return ob_get_clean();
    }
}
