<?php

class ConstructorController extends Controller {

    private function getSectionTypes() {
        $secciones = [];
        $path = MODULES_PATH . '/pagina_web/BuilderSections/';
        if (!is_dir($path)) return [];

        foreach (scandir($path) as $dir) {
            $schemaPath = $path . $dir . '/schema.json';
            if ($dir !== '.' && $dir !== '..' && is_dir($path . $dir) && file_exists($schemaPath)) {
                $schema = json_decode(file_get_contents($schemaPath), true);
                if ($schema) $secciones[$schema['tipo']] = $schema;
            }
        }
        return $secciones;
    }
    
    public function index() {
        auth_require();
        require_permission('pagina_web.ver');
        
        $db = Database::getInstance();
        
        // Obtener la página elegida o la principal por defecto
        $pagina_id = intval($_GET['pagina_id'] ?? 1);
        $pagina = $db->query("SELECT * FROM tienda_paginas WHERE id = $pagina_id")->fetch();
        if (!$pagina) die("Error: La página solicitada no existe.");
        
        // Obtener secciones asignadas a esta página ordenadas
        $secciones = $db->query("
            SELECT s.*, tps.orden 
            FROM tienda_secciones s
            JOIN tienda_pagina_secciones tps ON s.id = tps.seccion_id
            WHERE tps.pagina_id = {$pagina['id']}
            ORDER BY tps.orden ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $tipos_seccion = $this->getSectionTypes();

        $this->render('pagina_web', 'admin/constructor/index', [
            'titulo' => 'Constructor Visual (Layout Builder)',
            'pagina' => $pagina,
            'secciones' => $secciones,
            'tipos_seccion' => $tipos_seccion
        ]);
    }

    public function seccion() {
        auth_require();
        require_permission('pagina_web.crear');
        
        $id = intval($_GET['id'] ?? 0); // ID de la sección (si se está editando)
        $tipo = sanitize($_GET['tipo'] ?? 'carrusel_banners');
        $pagina_id = intval($_GET['pagina_id'] ?? 1);
        
        $db = Database::getInstance();
        $seccion = null;
        if ($id > 0) {
            $seccion = $db->query("SELECT * FROM tienda_secciones WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
            if ($seccion) {
                $seccion['config'] = json_decode($seccion['configuracion'], true);
                $tipo = $seccion['tipo'];
            }
        }
        
        // Cargar el schema de la sección para construir el formulario
        $schema_path = MODULES_PATH . "/pagina_web/BuilderSections/{$tipo}/schema.json";
        $schema = file_exists($schema_path) ? json_decode(file_get_contents($schema_path), true) : null;

        $colecciones_productos = $db->query("SELECT slug, nombre FROM tienda_colecciones WHERE estado = 'activo' AND tipo = 'productos' ORDER BY nombre ASC")->fetchAll();
        $colecciones_promociones = $db->query("SELECT slug, nombre FROM tienda_colecciones WHERE estado = 'activo' AND tipo = 'promociones' ORDER BY nombre ASC")->fetchAll();
        
        $this->render('pagina_web', 'admin/constructor/form_seccion', [
            'titulo' => $id > 0 ? 'Editar Sección' : 'Nueva Sección',
            'seccion' => $seccion,
            'tipo' => $tipo, // El tipo de sección (ej. grid_productos)
            'schema' => $schema, // El JSON con la definición de campos
            'colecciones_productos' => $colecciones_productos,
            'colecciones_promociones' => $colecciones_promociones,
            'pagina_id' => $pagina_id
        ]);
    }

    public function guardarSeccion() {
        auth_require();
        require_permission('pagina_web.crear');
        
        $db = Database::getInstance();
        
        $id = intval($_POST['id'] ?? 0);
        $pagina_id = intval($_POST['pagina_id'] ?? 1);
        $tipo = sanitize($_POST['tipo'] ?? '');
        $nombre_interno = sanitize($_POST['nombre_interno'] ?? 'Nueva Sección');
        $estado = sanitize($_POST['estado'] ?? 'activo');
        
        $config_data = $_POST['config'] ?? [];
        $processed_config = [];
        
        // --- LÓGICA DINÁMICA BASADA EN SCHEMA ---
        $schema_path = MODULES_PATH . "/pagina_web/BuilderSections/{$tipo}/schema.json";
        if (file_exists($schema_path)) {
            $schema = json_decode(file_get_contents($schema_path), true);
            foreach ($schema['campos'] as $campo) {
                $nombre_campo = $campo['nombre']; // ej: 'banners'
                if ($campo['tipo'] === 'repeater') { // <-- ESTE ES EL CASO DEL CARRUSEL
                    $items = [];
                    
                    // --- MEJORA: Conteo robusto de items en repeater ---
                    // Buscamos un campo de texto para contar, ya que los campos de imagen no están en $_POST.
                    $campo_para_contar = '';
                    foreach ($campo['campos_item'] as $sub_campo) {
                        if ($sub_campo['tipo'] !== 'imagen') { $campo_para_contar = $sub_campo['nombre']; break; }
                    }
                    if (empty($campo_para_contar) && !empty($campo['campos_item'])) $campo_para_contar = $campo['campos_item'][0]['nombre']; // Fallback por si solo hay imágenes
                    $count = isset($config_data[$nombre_campo][$campo_para_contar]) ? count($config_data[$nombre_campo][$campo_para_contar]) : 0;

                    for ($i = 0; $i < $count; $i++) {
                        $item_data = [];
                        foreach ($campo['campos_item'] as $sub_campo) {
                            $sub_nombre = $sub_campo['nombre']; // ej: 'titulo', 'enlace', 'imagen'
                            if ($sub_campo['tipo'] === 'imagen') {
                                $img_path = sanitize($config_data[$nombre_campo][$sub_nombre . '_existente'][$i] ?? '');
                                if (isset($_FILES['config']['name'][$nombre_campo][$sub_nombre][$i]) && $_FILES['config']['error'][$nombre_campo][$sub_nombre][$i] === UPLOAD_ERR_OK) {
                                    $upload_dir = BASE_PATH . '/storage/landing/';
                                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                                    $ext = strtolower(pathinfo($_FILES['config']['name'][$nombre_campo][$sub_nombre][$i], PATHINFO_EXTENSION));
                                    $new_name = uniqid(substr($sub_nombre, 0, 4) . '_') . '.' . $ext;
                                    if (move_uploaded_file($_FILES['config']['tmp_name'][$nombre_campo][$sub_nombre][$i], $upload_dir . $new_name)) {
                                        $img_path = 'storage/landing/' . $new_name;
                                    }
                                }
                                if (!empty($img_path)) $item_data[$sub_nombre] = $img_path;
                            } else {
                                if (isset($config_data[$nombre_campo][$sub_nombre][$i])) {
                                    $item_data[$sub_nombre] = sanitize($config_data[$nombre_campo][$sub_nombre][$i]);
                                }
                            }
                        }
                        if (!empty($item_data)) $items[] = $item_data;
                    }
                    $processed_config[$nombre_campo] = $items;
                } elseif ($campo['tipo'] === 'imagen') {
                    $img_path = sanitize($config_data[$nombre_campo . '_existente'] ?? '');
                    if (isset($_FILES['config']['name'][$nombre_campo]) && $_FILES['config']['error'][$nombre_campo] === UPLOAD_ERR_OK) {
                        $upload_dir = BASE_PATH . '/storage/landing/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                        $ext = strtolower(pathinfo($_FILES['config']['name'][$nombre_campo], PATHINFO_EXTENSION));
                        $new_name = uniqid('img_') . '.' . $ext;
                        if (move_uploaded_file($_FILES['config']['tmp_name'][$nombre_campo], $upload_dir . $new_name)) $img_path = 'storage/landing/' . $new_name;
                    }
                    if (!empty($img_path)) $processed_config[$nombre_campo] = $img_path;
                } elseif (isset($config_data[$nombre_campo])) { // Campos simples (texto, textarea, etc.)
                    $processed_config[$nombre_campo] = is_array($config_data[$nombre_campo]) ? $config_data[$nombre_campo] : sanitize($config_data[$nombre_campo]);
                }
            }
        } // --- FIN LÓGICA DINÁMICA ---

        $json_config = json_encode($processed_config, JSON_UNESCAPED_UNICODE);

        try {
            $db->beginTransaction();
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE tienda_secciones SET nombre_interno=?, configuracion=?, estado=? WHERE id=?");
                $stmt->execute([$nombre_interno, $json_config, $estado, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO tienda_secciones (nombre_interno, tipo, configuracion, estado) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre_interno, $tipo, $json_config, $estado]);
                $id = $db->lastInsertId();
                // Asignar a la página correspondiente
                $max_orden = $db->query("SELECT MAX(orden) FROM tienda_pagina_secciones WHERE pagina_id = $pagina_id")->fetchColumn() ?? 0;
                $db->prepare("INSERT INTO tienda_pagina_secciones (pagina_id, seccion_id, orden) VALUES (?, ?, ?)")->execute([$pagina_id, $id, $max_orden + 1]);
            }
            $db->commit();
            redirect(base_url("pagina_web/constructor?pagina_id=$pagina_id&success=Sección guardada correctamente"));
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            redirect(base_url("pagina_web/constructor/seccion?pagina_id=$pagina_id&id=$id&tipo=$tipo&error=Error al guardar"));
        }
    }

    public function eliminarSeccion() {
        auth_require(); require_permission('pagina_web.crear');
        $id = intval($_GET['id'] ?? 0);
        $pagina_id = intval($_GET['pagina_id'] ?? 1);
        if ($id) Database::getInstance()->prepare("DELETE FROM tienda_secciones WHERE id=?")->execute([$id]);
        redirect(base_url("pagina_web/constructor?pagina_id=$pagina_id&success=Sección eliminada"));
    }

    public function ordenar() {
        auth_require(); require_permission('pagina_web.crear');
        $data = json_decode(file_get_contents('php://input'), true);
        $pagina_id = intval($data['pagina_id'] ?? 1);
        
        if (isset($data['orden']) && is_array($data['orden'])) {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE tienda_pagina_secciones SET orden = ? WHERE seccion_id = ? AND pagina_id = ?");
            foreach ($data['orden'] as $index => $seccion_id) { $stmt->execute([$index + 1, intval($seccion_id), $pagina_id]); }
            json_response(['status' => 'success']);
        }
        json_response(['error' => 'Datos inválidos'], 400);
    }

    // --- HOOKS PARA EL COPILOTO DE IA (HERRAMIENTAS) ---
    public function hookIaCopilotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'pagina_web_crear_seccion_ia',
                    'description' => 'Crea una nueva sección (widget) para el constructor de páginas web a partir de una descripción en lenguaje natural. Genera los archivos schema.json y view.php necesarios.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'nombre_seccion' => ['type' => 'STRING', 'description' => 'El nombre legible para humanos de la sección. Ej: "Testimonios de Clientes", "Galería de Fotos del Equipo".'],
                            'tipo_seccion' => ['type' => 'STRING', 'description' => 'El identificador único en formato snake_case para la sección, que también será el nombre de la carpeta. Ej: "testimonios_clientes", "galeria_equipo".'],
                            'descripcion_seccion' => ['type' => 'STRING', 'description' => 'Una descripción detallada de cómo debe ser la sección, qué campos debe tener y cómo debe verse. Ej: "Una sección con un título, un subtítulo y una cuadrícula de 3 columnas. Cada columna debe tener una imagen redonda, el nombre de la persona, su cargo y un párrafo con su testimonio."']
                        ],
                        'required' => ['nombre_seccion', 'tipo_seccion', 'descripcion_seccion']
                    ]
                ]
            ]
        ];
    }

    public function executeIaCopilotTool($name, $args) {
        if ($name === 'pagina_web_crear_seccion_ia') {
            $nombre_seccion = sanitize($args['nombre_seccion'] ?? '');
            $tipo_seccion = sanitize(str_replace('-', '_', strtolower($args['tipo_seccion'] ?? '')));
            $descripcion_seccion = $args['descripcion_seccion'] ?? '';

            if (empty($nombre_seccion) || empty($tipo_seccion) || empty($descripcion_seccion)) {
                return ['error' => 'Faltan parámetros obligatorios: nombre_seccion, tipo_seccion y descripcion_seccion.'];
            }

            $sectionPath = MODULES_PATH . '/pagina_web/BuilderSections/' . $tipo_seccion;
            if (is_dir($sectionPath)) {
                return ['error' => "La sección con el tipo '{$tipo_seccion}' ya existe. Por favor, elige otro nombre de tipo."];
            }

            $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
            $apiKey = $sysConfig['GEMINI_API_KEY'] ?? '';
            if (empty($apiKey)) {
                return ['error' => 'La API Key de Gemini no está configurada en los Ajustes del Sistema.'];
            }

            // --- MEJORA: Prompt más específico para evitar errores de formato en el schema.json ---
            $prompt = "
                Tu tarea es generar el código para una nueva sección de un constructor de páginas web. Debes generar dos archivos: `schema.json` y `view.php`.

                Descripción de la sección solicitada:
                - Nombre: '$nombre_seccion'
                - Tipo (identificador): '$tipo_seccion'
                - Requerimientos: '$descripcion_seccion'

                Instrucciones para `schema.json`:
                - El campo 'tipo' debe ser '$tipo_seccion'.
                - El campo 'nombre' debe ser '$nombre_seccion'.
                - Elige un ícono de FontAwesome para el campo 'icono'.
                - Define los campos necesarios en el array 'campos'. CADA CAMPO DEBE SER UN OBJETO JSON CON LAS SIGUIENTES CLAVES: 'nombre' (el ID en snake_case), 'label' (texto para el usuario), 'tipo' y opcionalmente 'default'.
                - REGLA MUY IMPORTANTE: El identificador de cada campo DEBE usar la clave 'nombre', no 'id'.
                - REGLA PARA REPETIDORES: Si usas el tipo 'repeater', los campos que se repiten deben estar dentro de un array llamado 'campos_item', no 'campos'.
                - Los tipos de campo válidos son: 'texto', 'textarea', 'markdown', 'imagen', 'numero', 'select', 'coleccion_productos', 'coleccion_promociones', 'repeater'.

                Instrucciones para `view.php`:
                - Usa PHP y HTML con clases de Tailwind CSS.
                - La configuración de la sección estará disponible en la variable PHP `\$config`. Accede a los valores con `\$config['nombre_del_campo']`.
                - Sanitiza siempre la salida con `htmlspecialchars()`.
                - Si la sección necesita datos de la base de datos (como productos o promociones), el `StorefrontController` los inyectará en variables como `\$productos` o `\$promociones`. Tu vista solo debe renderizarlos.

                Responde únicamente con un bloque de código JSON que contenga dos claves: 'schema_json' y 'view_php', cada una con el código correspondiente como un string. No incluyas explicaciones adicionales.
            ";

            $payload = ["contents" => [["parts" => [["text" => $prompt]]]]];
            $geminiModel = $sysConfig['GEMINI_MODEL'] ?? 'gemini-1.5-flash';
            $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key=" . $apiKey);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $resData = json_decode($response, true);
            $generatedText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Limpiar el texto generado para que sea un JSON válido
            $jsonText = trim(str_replace(['```json', '```'], '', $generatedText));
            $generatedCode = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($generatedCode['schema_json']) || !isset($generatedCode['view_php'])) {
                return ['error' => 'La IA no pudo generar el código correctamente. Intenta ser más específico en la descripción.', 'raw_response' => $generatedText];
            }

            // Crear la carpeta y los archivos
            mkdir($sectionPath, 0777, true);
            file_put_contents($sectionPath . '/schema.json', $generatedCode['schema_json']);
            file_put_contents($sectionPath . '/view.php', $generatedCode['view_php']);

            return ['mensaje' => "¡Éxito! La sección '$nombre_seccion' ha sido creada. Ya puedes añadirla desde el constructor visual."];
        }
        return ['error' => 'Herramienta no soportada por el módulo Página Web'];
    }
}