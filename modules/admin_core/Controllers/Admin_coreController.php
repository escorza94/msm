<?php
class Admin_coreController extends Controller {
    public function index() {
        auth_require(); // Proteger el módulo
        require_permission('admin_core.ver');

        $dbHost = config('DB_HOST', 'No definido');
        $dbName = config('DB_NAME', 'No definido');
        $dbUser = config('DB_USER', 'No definido');
        $dbPass = config('DB_PASS', '');
        $appUrl = config('APP_URL', 'http://localhost/');
        
        $dbStatus = false;
        $dbError = '';
        $pendingMigrations = [];
        try {
            $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
            $testConn = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $dbStatus = true;
            
            $testConn->exec("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
            $executed = $testConn->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
            $migDir = BASE_PATH . '/database/migrations';
            if (is_dir($migDir)) {
                foreach (scandir($migDir) as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql' && !in_array($file, $executed)) {
                        $pendingMigrations[] = $file;
                    }
                }
            }
        } catch (\PDOException $e) {
            $dbStatus = false;
            $dbError = $e->getMessage();
        }
        
        $modules = [];
        if (is_dir(MODULES_PATH)) {
            foreach (scandir(MODULES_PATH) as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $configPath = MODULES_PATH . '/' . $dir . '/config.json';
                    if (file_exists($configPath)) {
                        $modData = json_decode(file_get_contents($configPath), true);
                        $modData['dir'] = $dir;
                        $modules[] = $modData;
                    }
                }
            }
        }

        $data = [
            'titulo'  => 'Panel de Desarrollo | AED Core',
            'dbHost'  => $dbHost,
            'dbName'  => $dbName,
            'dbUser'  => $dbUser,
            'dbStatus'=> $dbStatus,
            'dbError' => $dbError,
            'pendingMigrations' => $pendingMigrations,
            'modules' => $modules,
            'appUrl'  => $appUrl
        ];
        
        $this->render('admin_core', 'index', $data);
    }

    public function updateConfig() {
        auth_require();
        require_permission('admin_core.ver');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $host = $_POST['db_host'] ?? '';
            $name = $_POST['db_name'] ?? '';
            $user = $_POST['db_user'] ?? '';
            $pass = $_POST['db_pass'] ?? '';
            $appUrl = rtrim($_POST['app_url'] ?? '', '/');

            $configCode = "<?php\nreturn [\n    'DB_HOST' => '$host',\n    'DB_NAME' => '$name',\n    'DB_USER' => '$user',\n    'DB_PASS' => '$pass',\n    'APP_URL' => '$appUrl'\n];\n";
            file_put_contents(BASE_PATH . '/config.php', $configCode);
            redirect(base_url('admin_core?success=1'));
        }
    }

    public function runMigrations() {
        auth_require();
        require_permission('admin_core.ver');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $config = require BASE_PATH . '/config.php';
            $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4";
            try {
                $conn = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $migDir = BASE_PATH . '/database/migrations';
                $executed = $conn->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
                foreach (scandir($migDir) as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql' && !in_array($file, $executed)) {
                        $sql = file_get_contents($migDir . '/' . $file);
                        if (trim($sql) !== '') $conn->exec($sql);
                        $stmt = $conn->prepare("INSERT INTO migrations (migration) VALUES (?)");
                        $stmt->execute([$file]);
                    }
                }
                redirect(base_url('admin_core?migrated=1'));
            } catch (\Exception $e) { die("Error ejecutando migración: " . $e->getMessage()); }
        }
    }

    public function updateModuleConfig() {
        auth_require();
        require_permission('admin_core.ver');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $moduleDir = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['module_dir'] ?? '');
            $configPath = MODULES_PATH . '/' . $moduleDir . '/config.json';

            if (!empty($moduleDir) && file_exists($configPath)) {
                $config = json_decode(file_get_contents($configPath), true);
                
                $config['name'] = sanitize($_POST['name'] ?? $config['name']);
                if (isset($_POST['description'])) $config['description'] = sanitize($_POST['description']);
                if (isset($_POST['icon'])) $config['icon'] = sanitize($_POST['icon']);
                $config['active'] = isset($_POST['active']) ? true : false;
                
                if (isset($_POST['perm_name'])) {
                    $perms = [];
                    $pNames = $_POST['perm_name'];
                    $pDescs = $_POST['perm_desc'] ?? [];
                    foreach ($pNames as $i => $pName) {
                        $name = sanitize($pName);
                        if (!empty($name)) {
                            $perms[] = ['name' => $name, 'description' => sanitize($pDescs[$i] ?? '')];
                        }
                    }
                    $config['permissions'] = $perms;
                } else {
                    $config['permissions'] = [];
                }

                file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                
                // Auto-sincronizar permisos en la base de datos
                $db = Database::getInstance();
                foreach ($config['permissions'] as $p) {
                    $pName = sanitize($p['name'] ?? ''); $pDesc = sanitize($p['description'] ?? '');
                    if ($pName) { try { $db->prepare("INSERT IGNORE INTO permissions (name, description) VALUES (?, ?)")->execute([$pName, $pDesc]); } catch (\Exception $e) {} }
                }
                
                redirect(base_url('admin_core?success=Configuración del módulo actualizada correctamente.'));
            } else {
                redirect(base_url('admin_core?error=Módulo no encontrado.'));
            }
        }
    }
}
