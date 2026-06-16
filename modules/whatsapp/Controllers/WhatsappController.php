<?php
class WhatsappController extends Controller {
    public function index() {
        auth_require();
        require_permission('whatsapp.ver');
        $db = Database::getInstance();
        try {
            $total_contactos = $db->query("SELECT COUNT(*) FROM wa_contactos")->fetchColumn();
            $total_mensajes = $db->query("SELECT COUNT(*) FROM wa_mensajes")->fetchColumn();
        } catch (\PDOException $e) {
            die("Error BD: Ejecuta la migración de WhatsApp en Panel de Administración.");
        }
        $this->render('whatsapp', 'index', [
            'titulo' => 'Panel de Control - WhatsApp', 
            'total_contactos' => $total_contactos, 
            'total_mensajes' => $total_mensajes
        ]);
    }
    public function chat() {
        auth_require();
        require_permission('whatsapp.ver');
        $db = Database::getInstance();
        try {
            $contactos = $db->query("SELECT c.*, (SELECT contenido FROM wa_mensajes m WHERE m.contacto_id = c.id ORDER BY id DESC LIMIT 1) as ultimo_mensaje, (SELECT tipo FROM wa_mensajes m WHERE m.contacto_id = c.id ORDER BY id DESC LIMIT 1) as ultimo_tipo, (SELECT fecha_registro FROM wa_mensajes m WHERE m.contacto_id = c.id ORDER BY id DESC LIMIT 1) as fecha_ultimo FROM wa_contactos c ORDER BY fecha_ultimo DESC")->fetchAll();
        } catch (\PDOException $e) {
            die("Error BD: Ejecuta la migración de WhatsApp en Panel de Administración.");
        }
        $this->render('whatsapp', 'whatsapp', ['titulo' => 'Bandeja de Mensajes', 'contactos' => $contactos]);
    }
    public function contactos() {
        auth_require();
        require_permission('whatsapp.ver');
        $db = Database::getInstance();
        $contactos = $db->query("SELECT * FROM wa_contactos ORDER BY nombre ASC")->fetchAll();
        $this->render('whatsapp', 'contactos', ['titulo' => 'Directorio de Contactos', 'contactos' => $contactos]);
    }
    public function vincular() {
        auth_require();
        require_permission('whatsapp.ver');
        $this->render('whatsapp', 'vincular', ['titulo' => 'Vincular Dispositivo']);
    }
    public function obtenerMensajes() {
        auth_require();
        require_permission('whatsapp.ver');
        $id = sanitize($_GET['id'] ?? 0);
        $offset = (int)($_GET['offset'] ?? 0);
        $limit = 30; // Cargaremos de 30 en 30
        
        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("SELECT * FROM (SELECT m.*, u.name as nombre_usuario FROM wa_mensajes m LEFT JOIN users u ON m.usuario_id = u.id WHERE m.contacto_id = ? ORDER BY m.id DESC LIMIT $limit OFFSET $offset) sub ORDER BY id ASC");
            $stmt->execute([$id]);
            json_response(['status' => 'success', 'mensajes' => $stmt->fetchAll()]);
        } catch (\PDOException $e) {
            try {
                $stmt = $db->prepare("SELECT * FROM (SELECT * FROM wa_mensajes WHERE contacto_id = ? ORDER BY id DESC LIMIT $limit OFFSET $offset) sub ORDER BY id ASC");
                $stmt->execute([$id]);
                json_response(['status' => 'success', 'mensajes' => $stmt->fetchAll()]);
            } catch (\Exception $ex) {
                json_response(['error' => $ex->getMessage()], 500);
            }
        }
    }
    public function enviarMensaje() {
        auth_require();
        require_permission('whatsapp.ver');
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $numero = sanitize($data['numero'] ?? '');
        $mensaje = sanitize($data['mensaje'] ?? '');
        $archivo = $data['archivo'] ?? null;
        $nombreArchivo = sanitize($data['nombreArchivo'] ?? '');
        
        if (empty($numero) || (empty($mensaje) && empty($archivo))) json_response(['error' => 'Faltan datos'], 400);
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM wa_contactos WHERE whatsapp_id = ?");
        $stmt->execute([$numero]);
        if ($contacto = $stmt->fetch()) { $contacto_id = $contacto['id']; } 
        else {
            $tipo_chat = strpos($numero, '@g.us') !== false ? 'grupo' : ($numero === 'status@broadcast' ? 'estado' : 'individual');
            $stmt = $db->prepare("INSERT INTO wa_contactos (whatsapp_id, tipo_chat, nombre) VALUES (?, ?, ?)");
            $stmt->execute([$numero, $tipo_chat, explode('@', $numero)[0]]);
            $contacto_id = $db->lastInsertId();
        }
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
        
        $contenidoDb = $mensaje;
        $tipo = 'texto';
        
        // Si hay archivo, guardarlo en nuestro CRM para carga rápida
        if (!empty($archivo) && strpos($archivo, 'data:') === 0) {
            $parts = explode(',', $archivo, 2);
            if (count($parts) == 2) {
                $meta = $parts[0];
                preg_match('/data:([a-zA-Z0-9-]+\/[a-zA-Z0-9-\+.]+)/', $meta, $matches);
                $mime = $matches[1] ?? 'application/octet-stream';
                $ext = explode('/', $mime)[1] ?? 'bin';
                $ext = str_replace(['jpeg', 'ogg; codecs=opus'], ['jpg', 'ogg'], $ext);
                if (strpos($ext, ';') !== false) $ext = explode(';', $ext)[0];

                $storage_dir = dirname(__DIR__, 3) . '/storage';
                if (!is_dir($storage_dir)) mkdir($storage_dir, 0777, true);
                $filename = uniqid('wa_') . '.' . $ext;
                file_put_contents($storage_dir . '/' . $filename, base64_decode($parts[1]));

                $contenidoDb = 'storage/' . $filename;
                if (strpos($mime, 'image/') === 0) $tipo = 'imagen';
                elseif (strpos($mime, 'audio/') === 0 || $mime === 'application/ogg') $tipo = 'audio';
                elseif (strpos($mime, 'video/') === 0) $tipo = 'video';
                else $tipo = 'archivo';
            }
        }

        $stmt = $db->prepare("INSERT INTO wa_mensajes (contacto_id, usuario_id, direccion, tipo, contenido, estado) VALUES (?, ?, 'saliente', ?, ?, 'enviado')");
        if (!empty($archivo) && !empty($mensaje)) { $stmt->execute([$contacto_id, $usuario_id, $tipo, $contenidoDb]); $stmt->execute([$contacto_id, $usuario_id, 'texto', $mensaje]); } 
        else { $stmt->execute([$contacto_id, $usuario_id, $tipo, !empty($archivo) ? $contenidoDb : $mensaje]); }
        
        // Apagar IA automáticamente cuando un humano interviene
        $db->prepare("UPDATE wa_contactos SET bot_activo = 0 WHERE id = ?")->execute([$contacto_id]);

        $ch = curl_init('http://localhost:3000/api/enviar');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['numero' => $numero, 'mensaje' => $mensaje, 'archivo' => $archivo, 'nombreArchivo' => $nombreArchivo]));
        $response = curl_exec($ch);
        curl_close($ch);
        json_response(['status' => 'success', 'node_response' => json_decode($response)]);
    }
    public function enviarArchivo() { auth_require(); require_permission('whatsapp.ver'); json_response(['status' => 'success']); }
    public function webhookIncoming() {
        $rawData = file_get_contents('php://input');
        
        // LOG DE DEPURACIÓN - WEBHOOK
        $storage_dir = dirname(__DIR__, 3) . '/storage';
        if (!is_dir($storage_dir)) mkdir($storage_dir, 0777, true);
        file_put_contents($storage_dir . '/debug_webhook.txt', "[" . date('Y-m-d H:i:s') . "] RAW DATA:\n" . $rawData . "\n\n", FILE_APPEND);

        $data = json_decode($rawData, true);
        if (!$data || !isset($data['whatsapp_id'])) json_response(['error' => 'Datos inválidos'], 400);
        
        $whatsapp_id = sanitize($data['whatsapp_id']); 
        $mensaje = sanitize($data['mensaje'] ?? '');
        $archivo = $data['archivo'] ?? null;
        $nombre = sanitize($data['nombre'] ?? explode('@', $whatsapp_id)[0]); // Recupera el nombre real
        $tipo = sanitize($data['tipo'] ?? 'texto');
        $direccion = sanitize($data['direccion'] ?? 'entrante');

        $contenidoDb = null;
        // Si es un archivo, lo extraemos del Base64 y lo guardamos en /storage/
        if (!empty($archivo) && strpos($archivo, 'data:') === 0) {
            $parts = explode(',', $archivo, 2);
            if (count($parts) == 2) {
                $meta = $parts[0];
                $fileData = base64_decode($parts[1]);
                
                preg_match('/data:([a-zA-Z0-9-]+\/[a-zA-Z0-9-\+.]+)/', $meta, $matches);
                $mime = $matches[1] ?? 'application/octet-stream';
                $ext = explode('/', $mime)[1] ?? 'bin';
                
                $ext = str_replace(['jpeg', 'ogg; codecs=opus'], ['jpg', 'ogg'], $ext);
                if (strpos($ext, ';') !== false) $ext = explode(';', $ext)[0];

                $storage_dir = dirname(__DIR__, 3) . '/storage';
                if (!is_dir($storage_dir)) mkdir($storage_dir, 0777, true);
                
                $filename = uniqid('wa_') . '.' . $ext;
                file_put_contents($storage_dir . '/' . $filename, $fileData);
                
                $contenidoDb = 'storage/' . $filename; // Guardamos SOLO la ruta relativa
            }
        }
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM wa_contactos WHERE whatsapp_id = ?"); $stmt->execute([$whatsapp_id]);
        if ($contacto = $stmt->fetch()) { $contacto_id = $contacto['id']; } 
        else {
            $tipo_chat = strpos($whatsapp_id, '@g.us') !== false ? 'grupo' : ($whatsapp_id === 'status@broadcast' ? 'estado' : 'individual');
            $stmt = $db->prepare("INSERT INTO wa_contactos (whatsapp_id, tipo_chat, nombre, etiqueta) VALUES (?, ?, ?, ?)");
            $stmt->execute([$whatsapp_id, $tipo_chat, $nombre, 'nuevo']);
            $contacto_id = $db->lastInsertId();
        }
        $estado = $direccion === 'saliente' ? 'enviado' : 'recibido';
        $stmt = $db->prepare("INSERT INTO wa_mensajes (contacto_id, direccion, tipo, contenido, estado) VALUES (?, ?, ?, ?, ?)");
        
        if ($contenidoDb) { $stmt->execute([$contacto_id, $direccion, $tipo, $contenidoDb, $estado]); }
        if (!empty($mensaje)) { $stmt->execute([$contacto_id, $direccion, 'texto', $mensaje, $estado]); }
        if (!$contenidoDb && empty($mensaje)) { $stmt->execute([$contacto_id, $direccion, 'texto', '', $estado]); }

        // --- INTERCEPCIÓN DE MARKETING (TRIGGERS DE PAUTAS) ---
        $es_campana = false;
        if ($direccion === 'entrante' && $tipo === 'texto' && !empty($mensaje)) {
            try {
                // Buscamos coincidencia exacta o ignorando mayúsculas/espacios
                $stmtCampana = $db->prepare("SELECT * FROM marketing_campanas WHERE estado = 'activo' AND LOWER(TRIM(texto_disparador)) = LOWER(TRIM(?)) LIMIT 1");
                $stmtCampana->execute([$mensaje]);
                $campana = $stmtCampana->fetch();
                
                if ($campana) {
                    $es_campana = true;
                    // 1. Enviar auto-respuesta de marketing
                    if (!empty($campana['respuesta_automatica'])) {
                        $stmtOut = $db->prepare("INSERT INTO wa_mensajes (contacto_id, direccion, tipo, contenido, estado, es_bot) VALUES (?, 'saliente', 'texto', ?, 'enviado', 1)");
                        $stmtOut->execute([$contacto_id, $campana['respuesta_automatica']]);

                        $chWa = curl_init('http://localhost:3000/api/enviar');
                        curl_setopt($chWa, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($chWa, CURLOPT_POST, true);
                        curl_setopt($chWa, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                        curl_setopt($chWa, CURLOPT_POSTFIELDS, json_encode(['numero' => $whatsapp_id, 'mensaje' => $campana['respuesta_automatica']]));
                        curl_exec($chWa);
                        curl_close($chWa);
                    }
                    
                    // 2. Aplicar etiqueta y estado del bot según campaña
                    $updates = [];
                    $params = [];
                    if (!empty($campana['etiqueta_contacto'])) { $updates[] = "etiqueta = ?"; $params[] = $campana['etiqueta_contacto']; }
                    $updates[] = "bot_activo = ?"; $params[] = $campana['activar_bot'] ? 1 : 0;
                    
                    if (count($updates) > 0) {
                        $params[] = $contacto_id;
                        $db->prepare("UPDATE wa_contactos SET " . implode(", ", $updates) . " WHERE id = ?")->execute($params);
                    }
                }
            } catch (\PDOException $e) {} // Silencioso si la tabla de marketing aún no se ha migrado
        }

        // --- INTEGRACIÓN DE INTELIGENCIA ARTIFICIAL (BOT) ---
        if (!$es_campana && $direccion === 'entrante' && $tipo === 'texto' && !empty($mensaje)) {
            // Verificar si el bot está activo para este contacto
            $botActivo = $db->query("SELECT bot_activo FROM wa_contactos WHERE id = $contacto_id")->fetchColumn();
            if ($botActivo == 1) {
                $this->procesarRespuestaIA($contacto_id, $whatsapp_id, $mensaje);
            }
        }

        json_response(['status' => 'ok', 'contacto_id' => $contacto_id]);
    }

    private function procesarRespuestaIA($contacto_id, $numero_whatsapp, $mensaje_actual) {
        $db = Database::getInstance();
        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        $apiKey = $sysConfig['GEMINI_API_KEY'] ?? '';
        
        if (empty($apiKey)) return;

        // 1. Obtener historial reciente (Últimos 10 mensajes para contexto)
        $historialDB = $db->query("SELECT direccion, contenido FROM wa_mensajes WHERE contacto_id = $contacto_id AND tipo = 'texto' ORDER BY id DESC LIMIT 10")->fetchAll();
        $historialDB = array_reverse($historialDB);
        
        $historialGemini = [];
        foreach ($historialDB as $msg) {
            if ($msg === end($historialDB)) continue; // Ignorar el último porque se pasa explícitamente
            $role = $msg['direccion'] === 'entrante' ? 'user' : 'model';
            $historialGemini[] = ["role" => $role, "parts" => [["text" => $msg['contenido']]]];
        }
        $historialGemini[] = ["role" => "user", "parts" => [["text" => $mensaje_actual]]];

        // 2. Cargar contexto RAG (Base de Conocimiento y Catálogo)
        require_once MODULES_PATH . '/ia/Models/RagService.php';
        $rag = new RagService();
        $systemInstruction = $rag->getSystemPrompt($contacto_id);

        $toolsConfigPath = MODULES_PATH . '/ia/tools_config.json';
        $toolsConfig = file_exists($toolsConfigPath) ? json_decode(file_get_contents($toolsConfigPath), true) : [];

        // 3. Escanear módulos para herramientas PÚBLICAS de WhatsApp (hookWaBotTools)
        $toolsDeclarations = [];
        $registeredTools = [];
        
        if (is_dir(MODULES_PATH)) {
            foreach (scandir(MODULES_PATH) as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $configPath = MODULES_PATH . '/' . $dir . '/config.json';
                    if (file_exists($configPath)) {
                        $config = json_decode(file_get_contents($configPath), true);
                        if (isset($config['active']) && $config['active']) {
                            $controllerName = ucfirst($config['name']) . 'Controller';
                            if (class_exists($controllerName)) {
                                $controller = new $controllerName();
                                if (method_exists($controller, 'hookWaBotTools')) {
                                    $tools = $controller->hookWaBotTools();
                                    if (is_array($tools)) {
                                        foreach ($tools as $tool) {
                                            $toolName = $tool['declaration']['name'];
                                            if (isset($toolsConfig[$toolName]) && $toolsConfig[$toolName] === false) continue;
                                            $toolsDeclarations[] = $tool['declaration'];
                                            $registeredTools[$toolName] = $controller;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // 4. Preparar payload
        $payload = [
            "system_instruction" => ["parts" => [["text" => $systemInstruction]]],
            "contents" => $historialGemini
        ];
        if (!empty($toolsDeclarations)) {
            $payload["tools"] = [["functionDeclarations" => $toolsDeclarations]];
        }

        // 5. Ejecutar cURL a Gemini (Razonamiento en Cadena)
        $geminiModel = $sysConfig['GEMINI_MODEL'] ?? 'gemini-1.5-flash';
        $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key=" . $apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $respuestaTexto = '';
        $maxCiclos = 5;
        $cicloActual = 0;

        while ($cicloActual < $maxCiclos) {
            $cicloActual++;
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // LOG DE DEPURACIÓN - GEMINI API
            file_put_contents(dirname(__DIR__, 3) . '/storage/debug_gemini.txt', "[" . date('Y-m-d H:i:s') . "] CICLO: $cicloActual | HTTP CODE: $httpCode | RESPONSE:\n" . $response . "\n\n", FILE_APPEND);

            if ($httpCode != 200) { break; } // Salir silenciosamente si falla la API en background
            
            $resData = json_decode($response, true);
            $partes = $resData['candidates'][0]['content']['parts'] ?? [];

            $functionCalls = [];
            foreach ($partes as &$p) {
                if (isset($p['functionCall'])) {
                    if (isset($p['functionCall']['args']) && empty($p['functionCall']['args'])) {
                        $p['functionCall']['args'] = new \stdClass();
                    }
                    $functionCalls[] = $p['functionCall'];
                }
            }
            unset($p);

            if (!empty($functionCalls)) {
                $functionResponses = [];
                
                foreach ($functionCalls as $fCall) {
                    $funcName = $fCall['name'];
                    $funcArgs = $fCall['args'] ?? [];
                    $resultadoData = ["error" => "Herramienta no encontrada"];

                    if (isset($registeredTools[$funcName]) && method_exists($registeredTools[$funcName], 'executeWaBotTool')) {
                        try {
                            $resultadoData = $registeredTools[$funcName]->executeWaBotTool($funcName, $funcArgs, $contacto_id);
                        } catch (\Exception $e) {
                            $resultadoData = ["error" => $e->getMessage()];
                        }
                    }

                    $functionResponses[] = [
                        "functionResponse" => [
                            "name" => $funcName,
                            "response" => ["name" => $funcName, "content" => empty($resultadoData) ? new \stdClass() : $resultadoData]
                        ]
                    ];
                }

                $payload['contents'][] = ["role" => "model", "parts" => $partes];
                $payload['contents'][] = ["role" => "function", "parts" => $functionResponses];
                continue; // Vuelve a ejecutar curl (Razonamiento en cadena)
            } else {
                // Fin de razonamiento, obtener texto final
                foreach ($partes as $p) {
                    if (isset($p['text']) && !empty(trim($p['text']))) {
                        $respuestaTexto .= $p['text'];
                    }
                }
                break;
            }
        }
        curl_close($ch);

        // 6. Enviar la respuesta construida al cliente
        if (!empty($respuestaTexto)) {
            $stmt = $db->prepare("INSERT INTO wa_mensajes (contacto_id, direccion, tipo, contenido, estado, es_bot) VALUES (?, 'saliente', 'texto', ?, 'enviado', 1)");
            $success = $stmt->execute([$contacto_id, $respuestaTexto]);
            
            if (!$success) {
                // Fallback ultra estricto: Remover cualquier emoji o caracter fuera del estándar UTF-8 básico
                $textoLimpio = preg_replace('/[^\x{0000}-\x{FFFF}]/u', '', $respuestaTexto);
                
                // Si la regex falla, asegurarnos de que no intente guardar NULL en la BD
                if ($textoLimpio === null || trim($textoLimpio) === '') {
                    $textoLimpio = "(El bot envió un mensaje con formatos no soportados por la BD)";
                }
                
                $success2 = $stmt->execute([$contacto_id, $textoLimpio]);
                
                // Si AÚN falla, guardar el error exacto que MySQL está devolviendo
                if (!$success2) {
                    $errorInfo = $stmt->errorInfo();
                    file_put_contents(dirname(__DIR__, 3) . '/storage/debug_bd_whatsapp.txt', "[" . date('Y-m-d H:i:s') . "] ERROR SQL: " . json_encode($errorInfo) . "\n\n", FILE_APPEND);
                }
            }

            $chWa = curl_init('http://localhost:3000/api/enviar');
            curl_setopt($chWa, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chWa, CURLOPT_POST, true);
            curl_setopt($chWa, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($chWa, CURLOPT_POSTFIELDS, json_encode(['numero' => $numero_whatsapp, 'mensaje' => $respuestaTexto]));
            curl_exec($chWa);
            curl_close($chWa);
        }
    }

    public function configuracion() {
        auth_require();
        require_permission('whatsapp.ver');
        
        $configPath = MODULES_PATH . '/whatsapp/config.json';
        $config = json_decode(file_get_contents($configPath), true);
        $success = $_GET['success'] ?? null;
        
        $this->render('whatsapp', 'configuracion', ['titulo' => 'Configuración de WhatsApp', 'config' => $config, 'success' => $success]);
    }

    public function guardarConfiguracion() {
        auth_require();
        require_permission('whatsapp.ver');
        
        $configPath = MODULES_PATH . '/whatsapp/config.json';
        $config = json_decode(file_get_contents($configPath), true);
        
        // Actualizamos el ícono con lo que venga del formulario
        $config['icon'] = sanitize($_POST['icon'] ?? $config['icon']);
        
        file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        redirect(base_url('whatsapp/configuracion?success=1'));
    }

    // Endpoint para prender/apagar el bot manualmente desde el chat en la vista web
    public function toggleBot() {
        auth_require();
        require_permission('whatsapp.ver');
        $data = json_decode(file_get_contents('php://input'), true);
        $contacto_id = intval($data['contacto_id'] ?? 0);
        $estado = intval($data['estado'] ?? 0);
        if ($contacto_id) {
            Database::getInstance()->prepare("UPDATE wa_contactos SET bot_activo = ? WHERE id = ?")->execute([$estado, $contacto_id]);
            json_response(['status' => 'success']);
        }
        json_response(['error' => 'ID inválido'], 400);
    }

    // Endpoint AJAX para renderizar la 3ra columna inteligente
    public function obtenerPanelCrm() {
        auth_require();
        require_permission('whatsapp.ver');
        $whatsapp_id = sanitize($_GET['whatsapp_id'] ?? '');
        if (!$whatsapp_id) { json_response(['html' => '<div class="text-center p-4 text-gray-500">ID no válido</div>']); }

        $widgets = [];
        
        // Escanear todos los módulos en busca de la función hookWhatsAppPanel
        if (is_dir(MODULES_PATH)) {
            foreach (scandir(MODULES_PATH) as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $configPath = MODULES_PATH . '/' . $dir . '/config.json';
                    if (file_exists($configPath)) {
                        $config = json_decode(file_get_contents($configPath), true);
                        if (isset($config['active']) && $config['active']) {
                            $controllerName = ucfirst($config['name']) . 'Controller';
                            if (class_exists($controllerName)) {
                                $controller = new $controllerName();
                                if (method_exists($controller, 'hookWhatsAppPanel')) {
                                    $res = $controller->hookWhatsAppPanel($whatsapp_id);
                                    if (is_array($res) && isset($res['html'])) { $widgets[] = $res; }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Ordenar los bloques según la prioridad definida por cada módulo
        usort($widgets, function($a, $b) { return ($a['order'] ?? 50) <=> ($b['order'] ?? 50); });

        $html = '';
        foreach ($widgets as $w) { $html .= $w['html']; }
        
        if (empty($html)) { $html = '<div class="text-center p-4 text-gray-500 text-xs">No hay herramientas disponibles.</div>'; }

        json_response(['html' => $html]);
    }

    // --- HOOKS PARA EL BOT DE WHATSAPP PÚBLICO (CLIENTES) ---
    public function hookWaBotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'solicitar_humano',
                    'description' => 'Ejecuta esta función si el cliente pide explícitamente hablar con un humano o asesor, o si se enoja.',
                    'parameters' => ['type' => 'OBJECT', 'properties' => ['motivo' => ['type' => 'STRING']]]
                ]
            ]
        ];
    }

    public function executeWaBotTool($name, $args, $contacto_id = null) {
        if ($name === 'solicitar_humano') {
            $db = Database::getInstance();
            $db->prepare("UPDATE wa_contactos SET bot_activo = 0 WHERE id = ?")->execute([$contacto_id]);
            
            try {
                $clienteInfo = $db->prepare("SELECT nombre, whatsapp_id FROM wa_contactos WHERE id = ?");
                $clienteInfo->execute([$contacto_id]);
                $cliente = $clienteInfo->fetch();
                
                $configNotifPath = MODULES_PATH . '/notificaciones/config.json';
                $configNotif = file_exists($configNotifPath) ? json_decode(file_get_contents($configNotifPath), true) : [];
                $push = $configNotif['alertas']['handoff_admin_push'] ?? true;
                $wa = $configNotif['alertas']['handoff_admin_wa'] ?? true;
                
                if ($push || $wa) {
                    $msgHandoff = "El cliente *" . ($cliente['nombre'] ?? 'Desconocido') . "* ha solicitado hablar con un humano.\nTeléfono: " . ($cliente['whatsapp_id'] ?? '') . "\n_La IA se apagó automáticamente._";
                    if (function_exists('notificar_rol')) notificar_rol(1, "🚨 IA: Asistencia Requerida", $msgHandoff, "whatsapp/chat", $wa);
                }
            } catch (\Exception $e) {}
            
            return ["mensaje" => "Se ha desactivado el bot. Un humano responderá en breve. Despídete del cliente amablemente."];
        }
        return ["error" => "Herramienta no encontrada"];
    }
}
