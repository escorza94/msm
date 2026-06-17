<?php
session_start();
define('BASE_PATH', __DIR__);
define('MODULES_PATH', BASE_PATH . '/modules');

if (!file_exists(BASE_PATH . '/config.php') && file_exists(BASE_PATH . '/setup.php')) {
    require_once BASE_PATH . '/setup.php';
    exit;
}

require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/helpers.php';
 
class Router {
    public $routes = [];
    public function get($path, $handler) { $this->routes['GET'][$path] = $handler; }
    public function post($path, $handler) { $this->routes['POST'][$path] = $handler; }
    
    public function dispatch($uri, $method) {
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        // Solo se reemplaza el nombre del script si no es el directorio raíz ('/').
        // Esto evita que se eliminen todas las diagonales de la URI.
        if ($scriptName !== '/') {
            $uri = str_replace($scriptName, '', $uri);
        }
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        if (strpos($uri, '/index.php') === 0) { $uri = substr($uri, 10) ?: '/'; }

        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            if (is_callable($handler)) { call_user_func($handler); return; }
            
            list($controllerName, $methodName) = explode('@', $handler);
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $methodName)) {
                    $controller->$methodName();
                } else {
                    http_response_code(500); echo "<h2>500 - Método '$methodName' no existe</h2>";
                }
            } else {
                http_response_code(500); echo "<h2>500 - Controlador no encontrado</h2>";
            }
        } else {
            http_response_code(404); echo "<h2>404 - Ruta '$uri' No Encontrada</h2>";
        }
    }
}

$router = new Router();

spl_autoload_register(function ($class_name) {
    foreach (scandir(MODULES_PATH) as $module) {
        if ($module === '.' || $module === '..') continue;
        $cPath = MODULES_PATH . '/' . $module . '/Controllers/' . $class_name . '.php';
        if (file_exists($cPath)) { require_once $cPath; return; }
        $mPath = MODULES_PATH . '/' . $module . '/Models/' . $class_name . '.php';
        if (file_exists($mPath)) { require_once $mPath; return; }
    }
});

function loadModules($router) {
    if (!is_dir(MODULES_PATH)) return;
    foreach (scandir(MODULES_PATH) as $moduleName) {
        if ($moduleName === '.' || $moduleName === '..') continue;
        $modulePath = MODULES_PATH . '/' . $moduleName;
        if (is_dir($modulePath) && file_exists($modulePath . '/config.json')) {
            $config = json_decode(file_get_contents($modulePath . '/config.json'), true);
            if (isset($config['active']) && $config['active'] === true) {
                if (file_exists($modulePath . '/config.php')) {
                    require_once $modulePath . '/config.php';
                }
                if (file_exists($modulePath . '/routes.php')) {
                    require_once $modulePath . '/routes.php';
                }
            }
        }
    }
}

loadModules($router);
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
