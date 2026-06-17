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

$router->get('/', function() {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AED Framework | Instalado</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-gray-50 text-gray-800 font-sans flex items-center justify-center min-h-screen">
        <div class="max-w-2xl w-full bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-3xl mx-auto shadow-md mb-4"><i class="fas fa-cube"></i></div>
                <h1 class="text-3xl font-bold text-gray-900">¡AED Framework Instalado!</h1>
                <p class="text-gray-500 mt-2">Tu arquitectura HMVC está configurada y lista para la acción.</p>
            </div>
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-rocket text-indigo-500 mr-2"></i>Próximos Pasos Recomendados:</h2>
                <ul class="space-y-4 text-sm text-gray-600">
                    <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i> <div><b>1. Instalar Panel de Control:</b><br>Ejecuta <code class="bg-gray-200 text-pink-600 px-1 rounded font-mono">aed install module admin_core</code></div></li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i> <div><b>2. Instalar Autenticación:</b><br>Ejecuta <code class="bg-gray-200 text-pink-600 px-1 rounded font-mono">aed install module usuarios</code></div></li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i> <div><b>3. Crear Módulos Propios:</b><br>Ejecuta <code class="bg-gray-200 text-pink-600 px-1 rounded font-mono">aed install module &lt;nombre&gt;</code></div></li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i> <div><b>4. Crear Migraciones:</b><br>Ejecuta <code class="bg-gray-200 text-pink-600 px-1 rounded font-mono">aed make:migration &lt;nombre&gt;</code></div></li>
                </ul>
            </div>
            <p class="text-center text-xs text-gray-400">Revisa el <code class="text-gray-500 font-mono">MANUAL_USUARIO_AED.md</code> para más detalles.</p>
        </div>
    </body>
    </html>
    <?php
});

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
