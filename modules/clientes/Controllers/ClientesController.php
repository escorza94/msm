<?php

class ClientesController extends Controller {
    public function index() {
        auth_require();
        require_permission('clientes.ver');
        
        $db = Database::getInstance();
        try {
            $clientes = $db->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll();
        } catch (\PDOException $e) {
            $clientes = [];
        }
        
        $this->render('clientes', 'index', [
            'titulo' => 'Cartera de Clientes',
            'clientes' => $clientes
        ]);
    }

    public function nuevo() {
        auth_require();
        require_permission('clientes.crear');
        $error = $_GET['error'] ?? null;
        
        $db = Database::getInstance();
        // Traer todos los contactos individuales de WhatsApp
        $contactos_wa = $db->query("SELECT whatsapp_id, nombre FROM wa_contactos WHERE tipo_chat = 'individual' ORDER BY nombre ASC")->fetchAll();
        
        $this->render('clientes', 'nuevo', [
            'titulo' => 'Nuevo Cliente',
            'error' => $error,
            'contactos_wa' => $contactos_wa
        ]);
    }

    public function postNuevo() {
        auth_require();
        require_permission('clientes.crear');
        
        $nombre = sanitize($_POST['nombre'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $correo = sanitize($_POST['correo'] ?? '');
        $rfc = sanitize($_POST['rfc'] ?? '');
        $direccion = sanitize($_POST['direccion'] ?? '');
        $whatsapp_id = sanitize($_POST['whatsapp_id'] ?? null);
        $latitud = !empty($_POST['latitud']) ? (float)$_POST['latitud'] : null;
        $longitud = !empty($_POST['longitud']) ? (float)$_POST['longitud'] : null;
        $enlace_maps = sanitize($_POST['enlace_maps'] ?? '');

        if (empty($nombre)) redirect(base_url('clientes/nuevo?error=El nombre completo es obligatorio'));

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO clientes (nombre, telefono, correo, rfc, direccion, whatsapp_id, latitud, longitud, enlace_maps) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$nombre, $telefono, $correo, $rfc, $direccion, $whatsapp_id, $latitud, $longitud, $enlace_maps])) {
            redirect(base_url('clientes?success=Cliente registrado correctamente'));
        } else {
            redirect(base_url('clientes/nuevo?error=Error al guardar en la base de datos'));
        }
    }
public function ver() {
    auth_require();
    require_permission('clientes.ver');
    $id = intval($_GET['id'] ?? 0);
    if (!$id) redirect(base_url('clientes?error=Cliente no especificado'));

    $db = Database::getInstance();
    
    // Obtener información del cliente
    $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) redirect(base_url('clientes?error=Cliente no encontrado'));

    // Obtener historial de ventas (compras)
    $stmtVentas = $db->prepare("SELECT id, total, estado_pago, monto_recibido, fecha_creacion FROM ventas WHERE cliente_id = ? AND tipo = 'venta' ORDER BY id DESC");
    $stmtVentas->execute([$id]);
    $ventas = $stmtVentas->fetchAll();

    // Obtener historial de envíos
    $stmtEnvios = $db->prepare("
        SELECT e.* 
        FROM logistica_envios e 
        JOIN ventas v ON e.venta_id = v.id 
        WHERE v.cliente_id = ? 
        ORDER BY e.id DESC
    ");
    $stmtEnvios->execute([$id]);
    $envios = $stmtEnvios->fetchAll();

    // Calcular el resumen financiero
    $resumen = ['total_comprado' => 0, 'total_pagado' => 0, 'deuda_pendiente' => 0];
    foreach ($ventas as $v) {
        $resumen['total_comprado'] += $v['total'];
        $resumen['total_pagado'] += $v['monto_recibido'];
    }
    // Evitar valores negativos si por error el monto recibido es mayor al total
    $resumen['deuda_pendiente'] = max(0, $resumen['total_comprado'] - $resumen['total_pagado']);

    $this->render('clientes', 'ver', [
        'titulo' => 'Resumen del Cliente',
        'cliente' => $cliente,
        'ventas' => $ventas,
        'envios' => $envios,
        'resumen' => $resumen
    ]);
}

    public function editar() {
        auth_require();
        require_permission('clientes.crear');
        $id = intval($_GET['id'] ?? 0);
        $error = $_GET['error'] ?? null;
        
        if (!$id) redirect(base_url('clientes?error=Cliente no especificado'));
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        $cliente = $stmt->fetch();
        
        if (!$cliente) redirect(base_url('clientes?error=Cliente no encontrado'));
        
        $contactos_wa = $db->query("SELECT whatsapp_id, nombre FROM wa_contactos WHERE tipo_chat = 'individual' ORDER BY nombre ASC")->fetchAll();
        
        $this->render('clientes', 'editar', [
            'titulo' => 'Editar Cliente: ' . htmlspecialchars($cliente['nombre']),
            'error' => $error,
            'cliente' => $cliente,
            'contactos_wa' => $contactos_wa
        ]);
    }

    public function postEditar() {
        auth_require();
        require_permission('clientes.crear');
        
        $id = intval($_POST['id'] ?? 0);
        if (!$id) redirect(base_url('clientes?error=Cliente no especificado'));

        $nombre = sanitize($_POST['nombre'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $correo = sanitize($_POST['correo'] ?? '');
        $rfc = sanitize($_POST['rfc'] ?? '');
        $direccion = sanitize($_POST['direccion'] ?? '');
        $whatsapp_id = sanitize($_POST['whatsapp_id'] ?? null);
        $latitud = !empty($_POST['latitud']) ? (float)$_POST['latitud'] : null;
        $longitud = !empty($_POST['longitud']) ? (float)$_POST['longitud'] : null;
        $enlace_maps = sanitize($_POST['enlace_maps'] ?? '');

        if (empty($nombre)) redirect(base_url("clientes/editar?id=$id&error=El nombre completo es obligatorio"));

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE clientes SET nombre = ?, telefono = ?, correo = ?, rfc = ?, direccion = ?, whatsapp_id = ?, latitud = ?, longitud = ?, enlace_maps = ? WHERE id = ?");
        
        if ($stmt->execute([$nombre, $telefono, $correo, $rfc, $direccion, $whatsapp_id, $latitud, $longitud, $enlace_maps, $id])) {
            redirect(base_url('clientes?success=Cliente actualizado correctamente'));
        } else {
            redirect(base_url("clientes/editar?id=$id&error=Error al actualizar en la base de datos"));
        }
    }

    // --- HOOK PARA EL PANEL DERECHO DE WHATSAPP ---
    public function hookWhatsAppPanel($whatsapp_id) {
        $db = Database::getInstance();
        $html = '';
        try {
            $stmt = $db->prepare("SELECT * FROM clientes WHERE whatsapp_id = ? LIMIT 1");
            $stmt->execute([$whatsapp_id]);
            if ($cliente = $stmt->fetch()) {
                $html .= '<div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm text-center mb-4">';
                $html .= '<div class="w-14 h-14 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center text-2xl mx-auto mb-2"><i class="fas fa-user-check"></i></div>';
                $html .= '<h4 class="font-bold text-gray-800 text-base leading-tight">' . htmlspecialchars($cliente['nombre']) . '</h4>';
                $html .= '<p class="text-[10px] text-gray-500 font-mono mt-1"><i class="fab fa-whatsapp text-green-500 mr-1"></i>' . htmlspecialchars($whatsapp_id) . '</p>';
                $html .= '</div>';
                $html .= '<div class="space-y-2 mb-4"><a href="' . base_url('clientes/ver?id=' . $cliente['id']) . '" target="_blank" class="block w-full p-2.5 bg-white hover:bg-blue-50 text-gray-700 hover:text-blue-700 text-sm font-bold rounded-lg transition border border-gray-100 shadow-sm flex items-center"><div class="w-7 h-7 rounded bg-blue-100 text-blue-600 flex items-center justify-center mr-3"><i class="fas fa-address-card"></i></div> Perfil e Historial</a></div>';
            } else {
                $html .= '<div class="bg-white p-5 rounded-xl border border-dashed border-gray-300 text-center mb-4">';
                $html .= '<div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center text-xl mx-auto mb-3"><i class="fas fa-user-times"></i></div>';
                $html .= '<h4 class="font-bold text-gray-800 text-sm mb-1">Contacto Desconocido</h4>';
                $html .= '<p class="text-xs text-gray-500 mb-4">Este número no está en el CRM.</p>';
                $html .= '<a href="' . base_url('clientes/nuevo') . '" target="_blank" class="inline-block w-full py-2 bg-blue-600 text-white font-bold text-xs rounded-lg shadow hover:bg-blue-700 transition"><i class="fas fa-user-plus mr-1"></i> Guardar / Vincular en CRM</a>';
                $html .= '</div>';
            }
        } catch (\Exception $e) {}
        return ['order' => 10, 'html' => $html]; // Order 10 = Hasta arriba
    }
}