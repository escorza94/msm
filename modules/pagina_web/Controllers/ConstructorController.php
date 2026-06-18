<?php

class ConstructorController extends Controller {
    
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

        $this->render('pagina_web', 'admin/constructor/index', [
            'titulo' => 'Constructor Visual (Layout Builder)',
            'pagina' => $pagina,
            'secciones' => $secciones
        ]);
    }

    public function seccion() {
        auth_require();
        require_permission('pagina_web.crear');
        
        $id = intval($_GET['id'] ?? 0);
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
        
        $colecciones_productos = $db->query("SELECT slug, nombre FROM tienda_colecciones WHERE estado = 'activo' AND tipo = 'productos' ORDER BY nombre ASC")->fetchAll();
        $colecciones_promociones = $db->query("SELECT slug, nombre FROM tienda_colecciones WHERE estado = 'activo' AND tipo = 'promociones' ORDER BY nombre ASC")->fetchAll();
        
        $this->render('pagina_web', 'admin/constructor/form_seccion', [
            'titulo' => $id > 0 ? 'Editar Sección' : 'Nueva Sección',
            'seccion' => $seccion,
            'tipo' => $tipo,
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
        
        $config = [];
        
        if ($tipo === 'carrusel_banners') {
            $titulos = $_POST['banner_titulo'] ?? [];
            $enlaces = $_POST['banner_enlace'] ?? [];
            $imagenes_existentes = $_POST['banner_imagen_existente'] ?? [];
            
            $banners = [];
            for ($i = 0; $i < count($titulos); $i++) {
                $img_path = $imagenes_existentes[$i] ?? '';
                
                // Procesar subida de archivo
                if (isset($_FILES['banner_imagen']['name'][$i]) && !empty($_FILES['banner_imagen']['name'][$i])) {
                    $file_tmp = $_FILES['banner_imagen']['tmp_name'][$i];
                    $file_name = $_FILES['banner_imagen']['name'][$i];
                    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $upload_dir = BASE_PATH . '/storage/landing/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                        
                        $new_name = uniqid('banner_') . '.' . $ext;
                        if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                            $img_path = 'storage/landing/' . $new_name;
                        }
                    }
                }
                
                if (!empty($img_path)) {
                    $banners[] = ['titulo' => sanitize($titulos[$i]), 'enlace' => sanitize($enlaces[$i]), 'imagen' => $img_path];
                }
            }
            $config['banners'] = $banners;
        } 
        elseif ($tipo === 'grid_productos') {
            $config['titulo_seccion'] = sanitize($_POST['titulo_seccion'] ?? '');
            $config['subtitulo'] = sanitize($_POST['subtitulo'] ?? '');
            $config['coleccion_slug'] = sanitize($_POST['coleccion_slug'] ?? '');
            $config['limite_mostrar'] = intval($_POST['limite_mostrar'] ?? 8);
        }
        elseif ($tipo === 'tarjetas_info') {
            $config['titulo_seccion'] = sanitize($_POST['titulo_seccion'] ?? '');
            $config['subtitulo'] = sanitize($_POST['subtitulo'] ?? '');
            $titulos = $_POST['tarjeta_titulo'] ?? [];
            $iconos = $_POST['tarjeta_icono'] ?? [];
            $descripciones = $_POST['tarjeta_descripcion'] ?? [];
            
            $tarjetas = [];
            for ($i = 0; $i < count($titulos); $i++) {
                if(!empty($titulos[$i])) $tarjetas[] = ['titulo' => sanitize($titulos[$i]), 'icono' => sanitize($iconos[$i]), 'descripcion' => trim($descripciones[$i])];
            }
            $config['tarjetas'] = $tarjetas;
        }
        elseif ($tipo === 'texto_libre') {
            $config['titulo_seccion'] = sanitize($_POST['titulo_seccion'] ?? '');
            $config['contenido'] = trim($_POST['contenido'] ?? '');
        }
        elseif ($tipo === 'imagen_texto') {
            $config['titulo_seccion'] = sanitize($_POST['titulo_seccion'] ?? '');
            $config['contenido'] = trim($_POST['contenido'] ?? '');
            $config['posicion_imagen'] = sanitize($_POST['posicion_imagen'] ?? 'izquierda');
            
            $img_path = $_POST['imagen_existente'] ?? '';
            if (isset($_FILES['imagen']['name']) && !empty($_FILES['imagen']['name'])) {
                $file_tmp = $_FILES['imagen']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $upload_dir = BASE_PATH . '/storage/landing/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $new_name = uniqid('img_') . '.' . $ext;
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                        $img_path = 'storage/landing/' . $new_name;
                    }
                }
            }
            $config['imagen'] = $img_path;
        }
        elseif ($tipo === 'grid_promociones') {
            $config['titulo_seccion'] = sanitize($_POST['titulo_seccion'] ?? '');
            $config['subtitulo'] = sanitize($_POST['subtitulo'] ?? '');
            $config['coleccion_slug'] = sanitize($_POST['coleccion_slug'] ?? '');
            $config['limite_mostrar'] = intval($_POST['limite_mostrar'] ?? 6);
        }

        $json_config = json_encode($config, JSON_UNESCAPED_UNICODE);

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
}