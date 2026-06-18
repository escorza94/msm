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

        // --- MEJORA: Soporte para rutas dinámicas y estáticas ---
        foreach ($this->routes[$method] as $route => $handler) {
            // Convertir la ruta (ej. /producto/{slug}) en una expresión regular
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Extraer parámetros de la URL (ej. ['slug' => 'silla-moderna'])
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                    return;
                }

                list($controllerName, $methodName) = explode('@', $handler);
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        // Llamar al método del controlador pasándole los parámetros de la URL
                        call_user_func_array([$controller, $methodName], $params);
                        return;
                    }
                }
            }
        }

        // --- Lógica de fallback si ninguna ruta coincide ---
        // 1. Identificar si la ruta mal escrita era para el panel de administración (ERP)
            // 1. Identificar si la ruta mal escrita era para el panel de administración (ERP)
            $erp_prefixes = ['/pos', '/pagina_web', '/promociones', '/ia', '/notificaciones', '/inventario', '/clientes', '/finanzas', '/logistica', '/usuarios', '/auth', '/dashboard', '/api'];
            $is_erp = false;
            foreach ($erp_prefixes as $prefix) {
                if (strpos($uri, $prefix) === 0) { $is_erp = true; break; }
            }

            // 2. Si NO es del panel ERP, se la pasamos a la Tienda (Storefront) para que intente cargar la página o lance tu 404 visual
            if (!$is_erp && class_exists('StorefrontController')) {
                $_GET['slug'] = ltrim($uri, '/'); // Pasamos '/arribasdasdas' como 'arribasdasdas'
                $controller = new StorefrontController();
                if (method_exists($controller, 'pagina')) { $controller->pagina(); return; }
            }

            // 3. Si era una ruta del ERP (ej. /pos/rutafalsa), mostramos un error 404 limpio de backend
            http_response_code(404); 
            echo "<div style='font-family:sans-serif; text-align:center; padding:100px; background:#f9fafb; min-height:100vh;'><h2 style='font-size:30px; color:#ef4444;'>Error 404 - Panel Administrativo</h2><p style='color:#6b7280;'>La ruta <b>'$uri'</b> no existe o el módulo no está activo.</p><br><a href='javascript:history.back()' style='display:inline-block; padding:10px 20px; background:#3b82f6; color:#fff; text-decoration:none; border-radius:8px; font-weight:bold;'>Regresar</a></div>";
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
