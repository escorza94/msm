<?php

class IaController extends Controller {
    public function index() {
        auth_require();
        require_permission('ia.ver');
        $data = ['titulo' => 'Panel de Inteligencia Artificial'];
        $this->render('ia', 'index', $data);
    }

    // Helper para leer configuración de herramientas
    private function getToolsConfig() {
        $path = MODULES_PATH . '/ia/tools_config.json';
        return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }

    // Helper para guardar configuración de herramientas
    private function saveToolsConfig($config) {
        $path = MODULES_PATH . '/ia/tools_config.json';
        file_put_contents($path, json_encode($config, JSON_PRETTY_PRINT));
    }

    public function herramientas() {
        auth_require();
        require_permission('ia.ver');

        $herramientasCopiloto = [];
        $herramientasWhatsApp = [];

        $toolsConfig = $this->getToolsConfig();

        // Escanear todos los módulos para extraer herramientas registradas
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
                                // Extraer Herramientas del Copiloto Interno
                                if (method_exists($controller, 'hookIaCopilotTools')) {
                                    $tools = $controller->hookIaCopilotTools();
                                    if (is_array($tools)) {
                                        foreach ($tools as $tool) {
                                            $toolName = $tool['declaration']['name'];
                                            $estado = isset($toolsConfig[$toolName]) ? $toolsConfig[$toolName] : true;
                                            $herramientasCopiloto[] = ['modulo' => ucfirst($config['name']), 'nombre' => $toolName, 'descripcion' => $tool['declaration']['description'], 'estado' => $estado];
                                        }
                                    }
                                }
                                // Extraer Herramientas de WhatsApp Público
                                if (method_exists($controller, 'hookWaBotTools')) {
                                    $tools = $controller->hookWaBotTools();
                                    if (is_array($tools)) {
                                        foreach ($tools as $tool) {
                                            $toolName = $tool['declaration']['name'];
                                            $estado = isset($toolsConfig[$toolName]) ? $toolsConfig[$toolName] : true;
                                            $herramientasWhatsApp[] = ['modulo' => ucfirst($config['name']), 'nombre' => $toolName, 'descripcion' => $tool['declaration']['description'], 'estado' => $estado];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $data = ['titulo' => 'Herramientas de IA (Functions)', 'herramientasCopiloto' => $herramientasCopiloto, 'herramientasWhatsApp' => $herramientasWhatsApp];
        $this->render('ia', 'herramientas', $data);
    }

    public function toggleHerramienta() {
        auth_require();
        require_permission('ia.ver');
        $data = json_decode(file_get_contents('php://input'), true);
        $nombre = sanitize($data['nombre'] ?? '');
        $estado = isset($data['estado']) ? (bool)$data['estado'] : false;

        if ($nombre) {
            $toolsConfig = $this->getToolsConfig();
            $toolsConfig[$nombre] = $estado;
            $this->saveToolsConfig($toolsConfig);
            json_response(['status' => 'success']);
        }
        json_response(['error' => 'Nombre inválido'], 400);
    }

    public function prompts() {
        auth_require();
        require_permission('ia.ver');
        
        $configPath = MODULES_PATH . '/ia/prompt_config.json';
        $config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [
            'personalidad' => "Eres 'Martín', el asistente virtual experto en ventas para Mueblería San Martín. Eres muy amable, usas emojis y te enfocas en cerrar la venta rápido. Usa siempre formato Markdown para darle estilo a tus respuestas (negritas, viñetas).",
            'reglas' => "1. TU OBJETIVO PRINCIPAL ES CERRAR LA VENTA. Guía siempre al cliente hacia la compra y utiliza la herramienta 'crear_venta_mercado_pago' cuando confirme su pedido.\n2. Nunca inventes precios ni productos. Usa EXCLUSIVAMENTE las herramientas (functions) que se te han proporcionado para realizar acciones.\n3. Si el cliente se molesta, pide un asesor, o SI NO SABES QUÉ HACER O CÓMO RESPONDER, ejecuta INMEDIATAMENTE la herramienta 'solicitar_humano' para derivarlo. No adivines.\n4. Tus mensajes deben ser cortos, directos y legibles para leer en celular (WhatsApp).\n5. Si el cliente pregunta por entregas, infórmale que sí contamos con envío a domicilio a toda la ciudad."
        ];

        $this->render('ia', 'prompts', ['titulo' => 'Editor de Prompts y Personalidad', 'config' => $config]);
    }

    public function guardarPrompts() {
        auth_require();
        require_permission('ia.ver');
        
        $personalidad = strip_tags($_POST['personalidad'] ?? '');
        $reglas = strip_tags($_POST['reglas'] ?? '');
        $config = ['personalidad' => $personalidad, 'reglas' => $reglas];
        
        $configPath = MODULES_PATH . '/ia/prompt_config.json';
        file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        redirect(base_url('ia/prompts?success=Configuración guardada correctamente'));
    }

    public function generarPromptConIA() {
        auth_require();
        require_permission('ia.ver');
        $data = json_decode(file_get_contents('php://input'), true);
        $descripcion = sanitize($data['descripcion'] ?? '');
        
        if (empty($descripcion)) {
            json_response(['error' => 'La descripción no puede estar vacía'], 400);
        }

        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        $apiKey = $sysConfig['GEMINI_API_KEY'] ?? '';
        
        if(empty($apiKey)) {
            json_response(['error' => 'La API Key de Gemini no está configurada.'], 500);
        }

        $systemInstruction = "Eres un experto en Prompt Engineering. Tu objetivo es crear el 'System Prompt' (Instrucciones de sistema) para un asistente virtual experto en ventas (WhatsApp) basado en la descripción que el usuario te dé.\nDebes devolver la respuesta ESTRICTAMENTE en formato JSON válido con dos claves:\n1. 'personalidad': Un párrafo describiendo la identidad, tono, nombre y estilo de respuesta del bot.\n2. 'reglas': Una lista numerada de reglas estrictas y operativas que el bot no debe romper (Ej. Nunca inventar precios, solicitar humano si el cliente se enoja, mensajes cortos).\nNO incluyas Markdown en la respuesta general, SOLO el JSON crudo.";

        $payload = [
            "system_instruction" => ["parts" => [["text" => $systemInstruction]]],
            "contents" => [
                ["role" => "user", "parts" => [["text" => "Descripción del negocio y cómo quiero que sea el bot: " . $descripcion]]]
            ]
        ];

        $geminiModel = $sysConfig['GEMINI_MODEL'] ?? 'gemini-1.5-flash';
        $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key=" . $apiKey;

        $ch = curl_init($geminiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if($httpCode != 200) { json_response(['error' => 'Error de Gemini API', 'details' => json_decode($response)], 500); }

        $resData = json_decode($response, true);
        $textoIA = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        // Limpiar posible formato markdown de json
        $textoIA = str_replace(['```json', '```'], '', $textoIA);
        $textoIA = trim($textoIA);

        $jsonParsed = json_decode($textoIA, true);
        if (!$jsonParsed) {
            json_response(['error' => 'La IA no devolvió un JSON válido', 'raw' => $textoIA], 500);
        }

        json_response(['status' => 'success', 'data' => $jsonParsed]);
    }

    // Endpoint interno para comunicarse con la API de Gemini
    public function chatAPI() {
        $data = json_decode(file_get_contents('php://input'), true);
        $mensajeCliente = $data['mensaje'] ?? '';
        $historial = $data['historial'] ?? []; // [{role: 'user'|'model', parts: [{text: '...'}]}]
        
        if(empty($mensajeCliente)) {
            json_response(['error' => 'Mensaje vacío'], 400);
        }

        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        $apiKey = $sysConfig['GEMINI_API_KEY'] ?? '';
        
        if(empty($apiKey)) {
            json_response(['error' => 'La API Key de Gemini no está configurada en los ajustes del sistema.'], 500);
        }

        // Instanciar el servicio RAG para inyectar el catálogo
        require_once MODULES_PATH . '/ia/Models/RagService.php';
        $rag = new RagService();
        $systemInstruction = $rag->getSystemPrompt(); // Contexto sin ID de contacto externo

        // Añadir el mensaje actual al historial
        $historial[] = ['role' => 'user', 'parts' => [['text' => $mensajeCliente]]];

        // Preparar el payload para la API v1beta (gemini-1.5-flash es rápido y barato/gratis)
        $payload = [
            "system_instruction" => ["parts" => [["text" => $systemInstruction]]],
            "contents" => $historial
        ];

        $geminiModel = $sysConfig['GEMINI_MODEL'] ?? 'gemini-1.5-flash';
        $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key=" . $apiKey;

        $ch = curl_init($geminiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if($httpCode != 200) { json_response(['error' => 'Error de Gemini API', 'details' => json_decode($response)], 500); }

        $resData = json_decode($response, true);
        $partes = $resData['candidates'][0]['content']['parts'] ?? [];
        
        $functionCall = null;
        $textoIA = '';
        foreach ($partes as $p) {
            if (isset($p['functionCall'])) $functionCall = $p['functionCall'];
            if (isset($p['text'])) $textoIA = $p['text'];
        }
        
        if ($functionCall) {
            $funcName = $functionCall['name'];
            if ($funcName === 'solicitar_humano') {
                $respuestaIA = "*(Acción)*: El bot ha apagado su auto-respuesta para transferirte a un asesor humano.";
            } elseif ($funcName === 'crear_venta_mercado_pago') {
                $respuestaIA = "*(Acción)*: El bot intentará conectar con MercadoPago para generar el Link.";
            }
        } else {
            $respuestaIA = $textoIA;
        }

        $historial[] = ['role' => 'model', 'parts' => [['text' => $respuestaIA]]];
        
        json_response(['status' => 'success', 'respuesta' => $respuestaIA, 'historial_actualizado' => $historial]);
    }

    // --- ENDPOINTS PARA EL COPILOTO INTERNO (EMPLEADOS) ---

    public function historialInterno() {
        auth_require();
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? 0;
        $db = Database::getInstance();
        
        // Auto-crear tabla si no existe para almacenar la memoria de cada usuario del CRM
        $db->exec("CREATE TABLE IF NOT EXISTS ia_conversaciones_internas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            role ENUM('user', 'model') NOT NULL,
            mensaje TEXT NOT NULL,
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $historial = $db->query("SELECT role, mensaje FROM ia_conversaciones_internas WHERE usuario_id = $usuario_id ORDER BY id ASC")->fetchAll();
        json_response(['status' => 'success', 'historial' => $historial]);
    }

    public function chatInterno() {
        auth_require();
        $data = json_decode(file_get_contents('php://input'), true);
        $mensajeUsuario = $data['mensaje'] ?? '';
        
        if(empty($mensajeUsuario)) json_response(['error' => 'Mensaje vacío'], 400);

        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? 0;
        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        $apiKey = $sysConfig['GEMINI_API_KEY'] ?? '';
        
        if(empty($apiKey)) json_response(['error' => 'La API Key de Gemini no está configurada.'], 500);

        $db = Database::getInstance();
        // 1. Guardar mensaje del usuario
        $stmt = $db->prepare("INSERT INTO ia_conversaciones_internas (usuario_id, role, mensaje) VALUES (?, 'user', ?)");
        $stmt->execute([$usuario_id, $mensajeUsuario]);

        // 2. Obtener historial previo para dar contexto (últimos 20 mensajes)
        $historialDB = $db->query("SELECT role, mensaje FROM (SELECT id, role, mensaje FROM ia_conversaciones_internas WHERE usuario_id = $usuario_id ORDER BY id DESC LIMIT 20) sub ORDER BY id ASC")->fetchAll();
        
        $historialGemini = [];
        foreach ($historialDB as $msg) { $historialGemini[] = ["role" => $msg['role'], "parts" => [["text" => $msg['mensaje']]]]; }

        // 3. Escanear todos los módulos en busca de herramientas (Tools) para el Copiloto
        $toolsDeclarations = [];
        $registeredTools = [];
        $modulosActivos = []; // Novedad: Recolectar rutas para la IA
        $toolsConfig = $this->getToolsConfig(); // Solución: Cargar configuración para el Copiloto
        if (is_dir(MODULES_PATH)) {
            foreach (scandir(MODULES_PATH) as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $configPath = MODULES_PATH . '/' . $dir . '/config.json';
                    if (file_exists($configPath)) {
                        $config = json_decode(file_get_contents($configPath), true);
                        if (isset($config['active']) && $config['active']) {
                            $modulosActivos[] = "- Módulo " . ucfirst($config['name']) . ": " . base_url($config['name']);
                            $controllerName = ucfirst($config['name']) . 'Controller';
                            if (class_exists($controllerName)) {
                                $controller = new $controllerName();
                                if (method_exists($controller, 'hookIaCopilotTools')) {
                                    $tools = $controller->hookIaCopilotTools();
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
        $listaModulos = implode("\n", $modulosActivos);

        // 4. Prompt de Sistema EXCLUSIVO para empleados
        $systemInstruction = "Eres el copiloto virtual interno del CRM. Tu tarea es asistir a los empleados (asesores y administradores). Ayúdalos a redactar mensajes persuasivos para clientes, explicar funcionalidades, corregir ortografía, dar ideas de ventas o resumir información. Sé profesional, amigable y muy conciso. Usa siempre formato Markdown para estructurar tus respuestas con negritas, listas o viñetas.\n\n"
                           . "RUTAS DEL SISTEMA:\n"
                           . "Si recomiendas al usuario ir a un módulo, puedes usar estos enlaces directos:\n"
                           . $listaModulos . "\n\n"
                           . "REGLA OBLIGATORIA DE SUGERENCIAS:\n"
                           . "Al final de tu respuesta, SIEMPRE sugiere 2 o 3 preguntas cortas de seguimiento que el usuario podría hacerte basadas en la conversación actual y tus herramientas disponibles.\n"
                           . "Escribe cada sugerencia en una nueva línea exactamente con este formato: [SUGERENCIA]: Escribe la pregunta aquí";

        $payload = ["system_instruction" => ["parts" => [["text" => $systemInstruction]]], "contents" => $historialGemini];
        if (!empty($toolsDeclarations)) {
            $payload["tools"] = [["functionDeclarations" => $toolsDeclarations]];
        }

        $geminiModel = $sysConfig['GEMINI_MODEL'] ?? 'gemini-1.5-flash';
        $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key=" . $apiKey;
        
        $ch = curl_init($geminiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evita errores de SSL en XAMPP local
        
        $respuestaIA = '';
        $maxCiclos = 5; // Límite de seguridad
        $cicloActual = 0;

        while ($cicloActual < $maxCiclos) {
            $cicloActual++;
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if($httpCode != 200) { curl_close($ch); json_response(['error' => 'Error de Gemini API (Ciclo '.$cicloActual.')', 'details' => json_decode($response)], 500); }
            $resData = json_decode($response, true);
            $partes = $resData['candidates'][0]['content']['parts'] ?? [];

            // 5. Extraer TODAS las llamadas a funciones (Soporta múltiples herramientas a la vez o en cadena)
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
                    if (isset($registeredTools[$funcName]) && method_exists($registeredTools[$funcName], 'executeIaCopilotTool')) {
                        try {
                            $resultadoData = $registeredTools[$funcName]->executeIaCopilotTool($funcName, $funcArgs);
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

                // Agregar la decisión del modelo y nuestras respuestas al payload para que vuelva a pensar
                $payload['contents'][] = ["role" => "model", "parts" => $partes];
                $payload['contents'][] = ["role" => "function", "parts" => $functionResponses];
                
                continue; // Vuelve a ejecutar curl con el nuevo payload
            } else {
                // Fin del razonamiento, armar respuesta final
                foreach ($partes as $p) {
                    if (isset($p['text']) && !empty(trim($p['text']))) {
                        $respuestaIA .= $p['text'];
                    }
                }
                break; // Romper el ciclo
            }
        }
        curl_close($ch);

        if (empty(trim($respuestaIA))) { json_response(['error' => 'Gemini no devolvió texto o alcanzó el límite de herramientas (Safety/Loop).', 'details' => $resData ?? []], 500); }

        // 6. Guardar la respuesta generada por la IA en la BD
        $db->prepare("INSERT INTO ia_conversaciones_internas (usuario_id, role, mensaje) VALUES (?, 'model', ?)")->execute([$usuario_id, $respuestaIA]);
        json_response(['status' => 'success', 'respuesta' => $respuestaIA]);
    }
}
