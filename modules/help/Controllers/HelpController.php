<?php

class HelpController extends Controller {
    
    public function index() {
        auth_require();
        require_permission('help.ver');
        $db = Database::getInstance();
        
        try {
            $articulos = $db->query("
                SELECT a.*, c.nombre as categoria_nombre 
                FROM help_articulos a 
                LEFT JOIN help_categorias c ON a.categoria_id = c.id 
                ORDER BY a.fecha_actualizacion DESC
            ")->fetchAll();
            
            $categorias = $db->query("SELECT * FROM help_categorias ORDER BY nombre ASC")->fetchAll();
        } catch (\PDOException $e) {
            $articulos = [];
            $categorias = [];
        }
        
        $this->render('help', 'index', [
            'titulo' => 'Base de Conocimiento (Help)',
            'articulos' => $articulos,
            'categorias' => $categorias
        ]);
    }

    public function nuevo() {
        auth_require();
        require_permission('help.crear');
        $db = Database::getInstance();
        $categorias = $db->query("SELECT * FROM help_categorias WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        
        $this->render('help', 'nuevo', [
            'titulo' => 'Nuevo Artículo',
            'categorias' => $categorias
        ]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('help.crear');
        $titulo = sanitize($_POST['titulo'] ?? '');
        $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
        $tipo = in_array($_POST['tipo'] ?? '', ['publico', 'interno']) ? $_POST['tipo'] : 'publico';
        $contenido = sanitize($_POST['contenido'] ?? '');

        if (empty($titulo) || empty($contenido)) {
            redirect(base_url('help/nuevo?error=El título y el contenido son obligatorios'));
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("INSERT INTO help_articulos (categoria_id, titulo, contenido, tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$categoria_id, $titulo, $contenido, $tipo]);
            redirect(base_url('help?success=Artículo creado con éxito'));
        } catch (\PDOException $e) {
            redirect(base_url('help/nuevo?error=Error al guardar el artículo'));
        }
    }

    public function editar() {
        auth_require();
        require_permission('help.crear');
        $id = intval($_GET['id'] ?? 0);
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT * FROM help_articulos WHERE id = ?");
        $stmt->execute([$id]);
        $articulo = $stmt->fetch();
        
        if (!$articulo) redirect(base_url('help?error=Artículo no encontrado'));
        
        $categorias = $db->query("SELECT * FROM help_categorias WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();
        
        $this->render('help', 'editar', [
            'titulo' => 'Editar Artículo',
            'articulo' => $articulo,
            'categorias' => $categorias
        ]);
    }

    public function postEditar() {
        auth_require();
        require_permission('help.crear');
        $id = intval($_POST['id'] ?? 0);
        $titulo = sanitize($_POST['titulo'] ?? '');
        $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
        $tipo = in_array($_POST['tipo'] ?? '', ['publico', 'interno']) ? $_POST['tipo'] : 'publico';
        $contenido = sanitize($_POST['contenido'] ?? '');

        if (!$id || empty($titulo) || empty($contenido)) {
            redirect(base_url("help/editar?id=$id&error=Datos incompletos"));
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE help_articulos SET categoria_id=?, titulo=?, contenido=?, tipo=? WHERE id=?");
        $stmt->execute([$categoria_id, $titulo, $contenido, $tipo, $id]);
        
        redirect(base_url('help?success=Artículo actualizado con éxito'));
    }

    public function ver() {
        auth_require();
        require_permission('help.ver');
        $id = intval($_GET['id'] ?? 0);
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT a.*, c.nombre as categoria_nombre FROM help_articulos a LEFT JOIN help_categorias c ON a.categoria_id = c.id WHERE a.id = ?");
        $stmt->execute([$id]);
        $articulo = $stmt->fetch();
        
        if (!$articulo) redirect(base_url('help?error=Artículo no encontrado'));
        
        $this->render('help', 'ver', ['titulo' => $articulo['titulo'], 'articulo' => $articulo]);
    }

    public function postCategoria() {
        auth_require();
        require_permission('help.crear');
        $nombre = sanitize($_POST['nombre'] ?? '');
        if (!empty($nombre)) {
            Database::getInstance()->prepare("INSERT INTO help_categorias (nombre) VALUES (?)")->execute([$nombre]);
            redirect(base_url('help?success=Categoría creada'));
        }
        redirect(base_url('help?error=Nombre de categoría inválido'));
    }
    
    public function cambiarEstado() {
        auth_require();
        require_permission('help.crear');
        $id = intval($_GET['id'] ?? 0);
        if ($id) { Database::getInstance()->prepare("UPDATE help_articulos SET estado = IF(estado = 'activo', 'inactivo', 'activo') WHERE id = ?")->execute([$id]); }
        redirect(base_url('help?success=Estado actualizado'));
    }

    // --- HOOK PARA EL PANEL DERECHO DE WHATSAPP ---
    public function hookWhatsAppPanel($whatsapp_id) {
        $html = '<div class="space-y-2 mt-2">';
        $html .= '<p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 pl-1">Soporte</p>';
        $html .= '<a href="' . base_url('help') . '" target="_blank" class="block w-full p-2.5 bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-700 text-sm font-bold rounded-lg transition border border-gray-100 shadow-sm flex items-center"><div class="w-7 h-7 rounded bg-amber-100 text-amber-600 flex items-center justify-center mr-3"><i class="fas fa-book-open"></i></div> Enviar Respuestas (FAQ)</a>';
        $html .= '</div>';
        return ['order' => 90, 'html' => $html]; // Order 90 = Al fondo
    }

    // --- HOOKS PARA EL COPILOTO DE IA (HERRAMIENTAS) ---
    public function hookIaCopilotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'help_buscar_articulos',
                    'description' => 'Busca artículos, guías, políticas y preguntas frecuentes en la base de conocimiento interna del CRM.',
                    'parameters' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'query' => ['type' => 'STRING', 'description' => 'Palabras clave para buscar en los títulos o contenido de los artículos (Ej. devoluciones, fletes, garantía).']
                        ],
                        'required' => ['query']
                    ]
                ]
            ]
        ];
    }

    public function executeIaCopilotTool($name, $args) {
        if ($name === 'help_buscar_articulos') {
            $query = sanitize($args['query'] ?? '');
            if (empty($query)) return ['error' => 'Se requiere un término de búsqueda.'];
            $db = Database::getInstance();
            $searchTerm = "%{$query}%";
            $stmt = $db->prepare("SELECT titulo, contenido, tipo FROM help_articulos WHERE estado = 'activo' AND (titulo LIKE ? OR contenido LIKE ?) LIMIT 5");
            $stmt->execute([$searchTerm, $searchTerm]);
            $resultados = $stmt->fetchAll();
            return empty($resultados) ? ['mensaje' => "No se encontraron artículos que coincidan con '{$query}'."] : ['articulos_encontrados' => $resultados];
        }
        return ['error' => 'Herramienta no soportada por el módulo Help'];
    }
}