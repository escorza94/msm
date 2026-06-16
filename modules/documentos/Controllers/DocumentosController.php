<?php

class DocumentosController extends Controller {
    
    public function index() {
        auth_require();
        require_permission('documentos.ver');
        
        // Leer la configuración (si no existe, valores por defecto)
        $configPath = MODULES_PATH . '/documentos/configuracion.json';
        $configDoc = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [
            'ticket_ancho' => '80mm',
            'ticket_saludo' => '¡Gracias por su preferencia!',
            'ticket_pie' => 'Revise su mercancía antes de salir.',
            'imprimir_logo' => true
        ];

        $success = $_GET['success'] ?? null;
        
        $this->render('documentos', 'index', [
            'titulo' => 'Configuración de Impresión',
            'configDoc' => $configDoc,
            'success' => $success
        ]);
    }

    public function guardarConfiguracion() {
        auth_require();
        require_permission('documentos.ver');
        $configPath = MODULES_PATH . '/documentos/configuracion.json';
        
        $configDoc = [
            'ticket_ancho' => sanitize($_POST['ticket_ancho'] ?? '80mm'),
            'ticket_saludo' => sanitize($_POST['ticket_saludo'] ?? '¡Gracias por su preferencia!'),
            'ticket_pie' => sanitize($_POST['ticket_pie'] ?? 'Revise su mercancía antes de salir.'),
            'imprimir_logo' => isset($_POST['imprimir_logo']) ? true : false
        ];

        file_put_contents($configPath, json_encode($configDoc, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        redirect(base_url('documentos?success=Configuración guardada correctamente'));
    }

    public function ticket() {
        auth_require();
        require_permission('pos.ver'); // Si puede ver la venta, puede imprimir el ticket
        $id = intval($_GET['id'] ?? 0);
        if (!$id) die("Venta no especificada.");

        $db = Database::getInstance();
        
        // Obtener datos de la venta
        $stmt = $db->prepare("SELECT v.*, c.nombre as cliente_nombre, c.telefono as cliente_telefono, u.name as usuario_nombre FROM ventas v LEFT JOIN clientes c ON v.cliente_id = c.id LEFT JOIN users u ON v.usuario_id = u.id WHERE v.id = ?");
        $stmt->execute([$id]);
        $venta = $stmt->fetch();
        if (!$venta) die("Venta no encontrada.");

        // Obtener detalles de la venta
        $stmtDetalles = $db->prepare("SELECT vd.*, i.nombre as producto_nombre FROM ventas_detalles vd LEFT JOIN inventario i ON vd.producto_id = i.id WHERE vd.venta_id = ?");
        $stmtDetalles->execute([$id]);
        $detalles = $stmtDetalles->fetchAll();

        // Obtener abonos de la venta
        $stmtAbonos = $db->prepare("SELECT * FROM ventas_abonos WHERE venta_id = ? ORDER BY fecha_abono ASC");
        $stmtAbonos->execute([$id]);
        $abonos = $stmtAbonos->fetchAll();

        // Obtener la configuración del negocio y de los documentos
        $sysConfig = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];
        
        $configPath = MODULES_PATH . '/documentos/configuracion.json';
        $configDoc = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [
            'ticket_ancho' => '80mm',
            'ticket_saludo' => '¡Gracias por su preferencia!',
            'ticket_pie' => 'Revise su mercancía antes de salir.',
            'imprimir_logo' => true
        ];

        // NO usamos el Layout maestro, es una hoja en blanco lista para la impresora
        require_once MODULES_PATH . '/documentos/Views/ticket.php';
    }
}