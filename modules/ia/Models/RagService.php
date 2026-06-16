<?php

class RagService {
    public function getSystemPrompt($contacto_id = null) {
        $db = Database::getInstance();
        
        $configPath = MODULES_PATH . '/ia/prompt_config.json';
        $config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];
        
        $personalidad = $config['personalidad'] ?? "Eres 'Martín', el asistente virtual experto en ventas para Mueblería San Martín. Eres muy amable, usas emojis y te enfocas en cerrar la venta rápido. Usa siempre formato Markdown para darle estilo a tus respuestas (negritas, viñetas).";
        
        $prompt = $personalidad . "\n\n";
        
        // 1. Inyectar Base de Conocimiento (FAQ) - (Fallback temporal)
        try {
            $faqs = $db->query("SELECT titulo, contenido FROM help_articulos WHERE estado = 'activo' AND tipo = 'publico'")->fetchAll();
            if (!empty($faqs)) {
                $prompt .= "📚 **BASE DE CONOCIMIENTO (FAQ):**\n";
                foreach ($faqs as $f) {
                    $prompt .= "**Pregunta:** {$f['titulo']}\n**Respuesta:** {$f['contenido']}\n\n";
                }
            }
        } catch (\Exception $e) {}

        // 2. Inyectar Contexto Dinámico de Módulos (Desacoplado)
        if (is_dir(MODULES_PATH)) {
            foreach (scandir(MODULES_PATH) as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $modConfigPath = MODULES_PATH . '/' . $dir . '/config.json';
                    if (file_exists($modConfigPath)) {
                        $modConfig = json_decode(file_get_contents($modConfigPath), true);
                        if (isset($modConfig['active']) && $modConfig['active']) {
                            $controllerName = ucfirst($modConfig['name']) . 'Controller';
                            if (class_exists($controllerName)) {
                                $controller = new $controllerName();
                                if (method_exists($controller, 'hookIaRagContext')) {
                                    $contextoModulo = $controller->hookIaRagContext($contacto_id);
                                    if (!empty($contextoModulo)) $prompt .= $contextoModulo . "\n\n";
                                }
                            }
                        }
                    }
                }
            }
        }

        $prompt .= "⚠️ **REGLAS OBLIGATORIAS:**\n";
        $reglas = $config['reglas'] ?? "1. TU OBJETIVO PRINCIPAL ES CERRAR LA VENTA. Guía siempre al cliente hacia la compra y utiliza la herramienta 'crear_venta_mercado_pago' cuando confirme su pedido.\n2. Nunca inventes precios ni productos. Usa EXCLUSIVAMENTE las herramientas (functions) que se te han proporcionado para realizar acciones.\n3. Si el cliente se molesta, pide un asesor, o SI NO SABES QUÉ HACER O CÓMO RESPONDER, ejecuta INMEDIATAMENTE la herramienta 'solicitar_humano' para derivarlo. No adivines.\n4. Tus mensajes deben ser cortos, directos y legibles para leer en celular (WhatsApp).\n5. Si el cliente pregunta por entregas, infórmale que sí contamos con envío a domicilio a toda la ciudad.";
        $prompt .= $reglas . "\n";

        return $prompt;
    }
}