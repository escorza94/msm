<?php

class FinanzasController extends Controller {
    public function index() {
        auth_require();
        require_permission('finanzas.ver');
        $db = Database::getInstance();

        // 1. Obtener cuentas y calcular su saldo actual dinámicamente
        $cuentas = $db->query("
            SELECT c.*,
                c.saldo_inicial +
                COALESCE((SELECT SUM(monto) FROM finanzas_movimientos WHERE cuenta_id = c.id AND tipo = 'ingreso'), 0) -
                COALESCE((SELECT SUM(monto) FROM finanzas_movimientos WHERE cuenta_id = c.id AND tipo = 'egreso'), 0) AS saldo_actual
            FROM finanzas_cuentas c
            WHERE c.estado = 'activo'
        ")->fetchAll();

        $total_general = array_sum(array_column($cuentas, 'saldo_actual'));

        // 2. Últimos movimientos generales
        $movimientos = $db->query("
            SELECT m.*, c.nombre as cuenta_nombre, cat.nombre as categoria_nombre, u.name as usuario_nombre
            FROM finanzas_movimientos m
            LEFT JOIN finanzas_cuentas c ON m.cuenta_id = c.id
            LEFT JOIN finanzas_categorias cat ON m.categoria_id = cat.id
            LEFT JOIN users u ON m.usuario_id = u.id
            ORDER BY m.fecha_movimiento DESC, m.id DESC
            LIMIT 50
        ")->fetchAll();

        // 3. Catálogo de categorías para el formulario
        $categorias = $db->query("SELECT * FROM finanzas_categorias WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll();

        $this->render('finanzas', 'index', [
            'titulo' => 'Tablero Financiero (Libro Mayor)',
            'cuentas' => $cuentas,
            'total_general' => $total_general,
            'movimientos' => $movimientos,
            'categorias' => $categorias
        ]);
    }

    public function guardarMovimiento() {
        auth_require();
        require_permission('finanzas.crear');
        
        $tipo = $_POST['tipo'] === 'egreso' ? 'egreso' : 'ingreso';
        $cuenta_id = intval($_POST['cuenta_id'] ?? 0);
        $monto = floatval($_POST['monto'] ?? 0);
        $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
        $concepto = sanitize($_POST['concepto'] ?? '');
        $metodo_pago = sanitize($_POST['metodo_pago'] ?? 'Efectivo');
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;

        if (!$cuenta_id || $monto <= 0 || empty($concepto)) {
            redirect(base_url('finanzas?error=Datos inválidos para el movimiento'));
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("INSERT INTO finanzas_movimientos (cuenta_id, usuario_id, tipo, monto, categoria_id, concepto, metodo_pago, origen_tipo) VALUES (?, ?, ?, ?, ?, ?, ?, 'manual')");
            $stmt->execute([$cuenta_id, $usuario_id, $tipo, $monto, $categoria_id, $concepto, $metodo_pago]);
            
            redirect(base_url('finanzas?success=' . ucfirst($tipo) . ' registrado correctamente'));
        } catch (\PDOException $e) {
            redirect(base_url('finanzas?error=Error al guardar el movimiento'));
        }
    }

    public function guardarTraspaso() {
        auth_require();
        require_permission('finanzas.crear');
        
        $cuenta_origen = intval($_POST['cuenta_origen'] ?? 0);
        $cuenta_destino = intval($_POST['cuenta_destino'] ?? 0);
        $monto = floatval($_POST['monto'] ?? 0);
        $concepto = sanitize($_POST['concepto'] ?? 'Traspaso interno');
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;

        if (!$cuenta_origen || !$cuenta_destino || $cuenta_origen == $cuenta_destino || $monto <= 0) {
            redirect(base_url('finanzas?error=Datos inválidos para el traspaso'));
        }

        $grupo_transaccion = uniqid('tras_'); // Vinculará el egreso y el ingreso
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // 1. Registrar Egreso (Salida de la cuenta origen)
            $stmtOut = $db->prepare("INSERT INTO finanzas_movimientos (cuenta_id, usuario_id, tipo, monto, concepto, origen_tipo, grupo_transaccion) VALUES (?, ?, 'egreso', ?, ?, 'traspaso', ?)");
            $stmtOut->execute([$cuenta_origen, $usuario_id, $monto, "Traspaso enviado a cuenta #$cuenta_destino - $concepto", $grupo_transaccion]);
            
            // 2. Registrar Ingreso (Entrada a la cuenta destino)
            $stmtIn = $db->prepare("INSERT INTO finanzas_movimientos (cuenta_id, usuario_id, tipo, monto, concepto, origen_tipo, grupo_transaccion) VALUES (?, ?, 'ingreso', ?, ?, 'traspaso', ?)");
            $stmtIn->execute([$cuenta_destino, $usuario_id, $monto, "Traspaso recibido de cuenta #$cuenta_origen - $concepto", $grupo_transaccion]);
            
            $db->commit();
            redirect(base_url('finanzas?success=Traspaso realizado con éxito'));
        } catch (\Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            redirect(base_url('finanzas?error=Error al procesar el traspaso'));
        }
    }

    public function guardarCuenta() {
        auth_require();
        require_permission('finanzas.crear');
        
        $nombre = sanitize($_POST['nombre'] ?? '');
        $tipo = sanitize($_POST['tipo'] ?? 'efectivo');
        $saldo_inicial = floatval($_POST['saldo_inicial'] ?? 0);

        if (empty($nombre)) {
            redirect(base_url('finanzas?error=El nombre de la cuenta es obligatorio'));
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("INSERT INTO finanzas_cuentas (nombre, tipo, saldo_inicial) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $tipo, $saldo_inicial]);
            redirect(base_url('finanzas?success=Cuenta creada correctamente'));
        } catch (\PDOException $e) {
            redirect(base_url('finanzas?error=Error al crear la cuenta'));
        }
    }

    public function cuentasPorCobrar() {
        auth_require();
        require_permission('finanzas.ver');
        $db = Database::getInstance();
        
        $deudas = $db->query("
            SELECT v.id, v.total, v.monto_recibido, (v.total - v.monto_recibido) as restante, v.estado_pago, v.fecha_creacion, c.nombre as cliente_nombre, c.telefono
            FROM ventas v
            LEFT JOIN clientes c ON v.cliente_id = c.id
            WHERE v.estado_pago IN ('pendiente', 'parcial') AND v.tipo = 'venta'
            ORDER BY v.fecha_creacion ASC
        ")->fetchAll();

        $cuentas = $db->query("SELECT id, nombre, tipo FROM finanzas_cuentas WHERE estado = 'activo'")->fetchAll();

        $this->render('finanzas', 'cuentas_por_cobrar', [
            'titulo' => 'Cuentas por Cobrar (Deudas)',
            'deudas' => $deudas,
            'cuentas' => $cuentas
        ]);
    }

    public function guardarAbono() {
        auth_require();
        require_permission('finanzas.crear');
        $venta_id = intval($_POST['venta_id'] ?? 0);
        $monto = floatval($_POST['monto'] ?? 0);
        $cuenta_id = intval($_POST['cuenta_id'] ?? 0);
        $metodo_pago = sanitize($_POST['metodo_pago'] ?? 'Efectivo');
        $redirect_to = $_POST['redirect_to'] ?? 'finanzas/cobrar';
        $usuario_id = $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
        
        // Detectamos si la ruta ya tiene parámetros GET para adjuntar correctamente los mensajes (uso de ? o &)
        $url_base = base_url($redirect_to . (strpos($redirect_to, '?') !== false ? '&' : '?'));
        $url_error = $url_base . 'error=';
        $url_success = $url_base . 'success=';
        
        if (!$venta_id || $monto <= 0 || !$cuenta_id) redirect($url_error . 'Datos inválidos');
        
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT total, monto_recibido FROM ventas WHERE id = ? FOR UPDATE"); $stmt->execute([$venta_id]); $venta = $stmt->fetch();
            if (!$venta) throw new Exception("Venta no encontrada");
            
            $restante = $venta['total'] - $venta['monto_recibido'];
            $abono_real = min($monto, $restante); // Para evitar que paguen más de la cuenta
            if ($abono_real <= 0) throw new Exception("La venta ya está pagada");
            
            $stmtAbono = $db->prepare("INSERT INTO ventas_abonos (venta_id, usuario_id, cuenta_id, monto, metodo_pago) VALUES (?, ?, ?, ?, ?)"); $stmtAbono->execute([$venta_id, $usuario_id, $cuenta_id, $abono_real, $metodo_pago]);
            $concepto = "Abono a Venta folio #" . str_pad($venta_id, 5, '0', STR_PAD_LEFT);
            $stmtFin = $db->prepare("INSERT INTO finanzas_movimientos (cuenta_id, usuario_id, tipo, monto, categoria_id, concepto, metodo_pago, origen_tipo, origen_id) VALUES (?, ?, 'ingreso', ?, 2, ?, ?, 'abono', ?)"); $stmtFin->execute([$cuenta_id, $usuario_id, $abono_real, $concepto, $metodo_pago, $venta_id]);
            $nuevo_recibido = $venta['monto_recibido'] + $abono_real; $nuevo_estado = ($nuevo_recibido >= $venta['total']) ? 'pagado' : 'parcial';
            $stmtUpd = $db->prepare("UPDATE ventas SET monto_recibido = ?, estado_pago = ? WHERE id = ?"); $stmtUpd->execute([$nuevo_recibido, $nuevo_estado, $venta_id]);
            
            $db->commit(); redirect($url_success . 'Abono registrado con éxito por $' . number_format($abono_real, 2));
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack(); redirect($url_error . urlencode($e->getMessage()));
        }
    }

    public function verMovimiento() {
        auth_require();
        require_permission('finanzas.ver');
        $id = intval($_GET['id'] ?? 0);
        if (!$id) redirect(base_url('finanzas?error=Movimiento no especificado'));

        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT m.*, c.nombre as cuenta_nombre, cat.nombre as categoria_nombre, u.name as usuario_nombre
            FROM finanzas_movimientos m
            LEFT JOIN finanzas_cuentas c ON m.cuenta_id = c.id
            LEFT JOIN finanzas_categorias cat ON m.categoria_id = cat.id
            LEFT JOIN users u ON m.usuario_id = u.id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        $movimiento = $stmt->fetch();

        if (!$movimiento) redirect(base_url('finanzas?error=Movimiento no encontrado'));

        $this->render('finanzas', 'ver_movimiento', [
            'titulo' => 'Detalle del Movimiento #' . str_pad($movimiento['id'], 5, '0', STR_PAD_LEFT),
            'movimiento' => $movimiento
        ]);
    }

    // --- HOOKS PARA EL COPILOTO DE IA (HERRAMIENTAS) ---
    public function hookIaCopilotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'finanzas_consultar_saldos',
                    'description' => 'Consulta el saldo actual de todas las cajas y cuentas bancarias registradas en finanzas.',
                    'parameters' => ['type' => 'OBJECT', 'properties' => ['dummy' => ['type' => 'STRING', 'description' => 'Parámetro vacío.']]]
                ]
            ],
            [
                'declaration' => [
                    'name' => 'finanzas_resumen_cuentas_por_cobrar',
                    'description' => 'Obtiene un resumen del dinero total que nos deben los clientes (cuentas por cobrar) y cuántas notas o ventas tienen saldo pendiente.',
                    'parameters' => ['type' => 'OBJECT', 'properties' => ['dummy' => ['type' => 'STRING', 'description' => 'Parámetro vacío.']]]
                ]
            ]
        ];
    }

    public function executeIaCopilotTool($name, $args) {
        $db = Database::getInstance();
        if ($name === 'finanzas_consultar_saldos') {
            $cuentas = $db->query("SELECT c.nombre, c.tipo, c.saldo_inicial + COALESCE((SELECT SUM(monto) FROM finanzas_movimientos WHERE cuenta_id = c.id AND tipo = 'ingreso'), 0) - COALESCE((SELECT SUM(monto) FROM finanzas_movimientos WHERE cuenta_id = c.id AND tipo = 'egreso'), 0) AS saldo_actual FROM finanzas_cuentas c WHERE c.estado = 'activo'")->fetchAll(PDO::FETCH_ASSOC);
            return empty($cuentas) ? ['mensaje' => 'No hay cuentas registradas.'] : ['cuentas' => $cuentas];
        }
        if ($name === 'finanzas_resumen_cuentas_por_cobrar') {
            $res = $db->query("SELECT COUNT(id) as total_notas, SUM(total - monto_recibido) as deuda_total FROM ventas WHERE estado_pago IN ('pendiente', 'parcial') AND tipo = 'venta'")->fetch(PDO::FETCH_ASSOC);
            return ['notas_pendientes' => intval($res['total_notas']), 'deuda_total_acumulada' => floatval($res['deuda_total'])];
        }
        return ['error' => 'Herramienta no soportada por Finanzas'];
    }

    // --- HOOKS PARA EL BOT DE WHATSAPP PÚBLICO (CLIENTES) ---
    public function hookWaBotTools() {
        return [
            [
                'declaration' => [
                    'name' => 'crear_venta_mercado_pago',
                    'description' => 'Genera una orden de venta cuando el cliente decide comprar y quiere pagar.',
                    'parameters' => ['type' => 'OBJECT', 'properties' => ['productos' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']]]]
                ]
            ]
        ];
    }

    public function executeWaBotTool($name, $args, $contacto_id = null) {
        if ($name === 'crear_venta_mercado_pago') {
            return ["mensaje" => "Integración con MercadoPago en proceso..."];
        }
        return ['error' => 'Herramienta no soportada por Finanzas'];
    }
}
