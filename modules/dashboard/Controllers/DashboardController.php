<?php

class DashboardController extends Controller {
    
    public function index() {
        auth_require();
        $db = Database::getInstance();
        
        // Inicializar variables por defecto
        $ventasHoy = 0; 
        $ventasMes = 0; 
        $porCobrar = 0; 
        $entregasActivas = 0;
        $ultimasVentas = []; 
        $alertasStock = [];

        try {
            // Indicadores Generales
            $ventasHoy = $db->query("SELECT SUM(total) FROM ventas WHERE DATE(fecha_creacion) = CURDATE() AND tipo = 'venta'")->fetchColumn() ?: 0;
            $ventasMes = $db->query("SELECT SUM(total) FROM ventas WHERE MONTH(fecha_creacion) = MONTH(CURDATE()) AND YEAR(fecha_creacion) = YEAR(CURDATE()) AND tipo = 'venta'")->fetchColumn() ?: 0;
            $porCobrar = $db->query("SELECT SUM(total - monto_recibido) FROM ventas WHERE estado_pago IN ('pendiente', 'parcial') AND tipo = 'venta'")->fetchColumn() ?: 0;
            $entregasActivas = $db->query("SELECT COUNT(*) FROM logistica_envios WHERE estado IN ('pendiente', 'en_ruta')")->fetchColumn() ?: 0;
            
            // Últimas 6 Ventas para la tabla rápida
            $ultimasVentas = $db->query("SELECT v.id, v.total, v.fecha_creacion, c.nombre as cliente_nombre FROM ventas v LEFT JOIN clientes c ON v.cliente_id = c.id WHERE v.tipo = 'venta' ORDER BY v.id DESC LIMIT 6")->fetchAll();
            
            // Alertas de inventario (Stock menor o igual a 3)
            $alertasStock = $db->query("SELECT sku, nombre, stock, precio FROM inventario WHERE stock <= 3 AND estado = 'activo' ORDER BY stock ASC LIMIT 6")->fetchAll();
            
        } catch (\PDOException $e) { } // Por si alguna tabla está vacía o en migración

        $configGlobal = file_exists(BASE_PATH . '/config.php') ? require BASE_PATH . '/config.php' : [];

        $this->render('dashboard', 'index', [
            'titulo' => 'Dashboard Comercial',
            'ventasHoy' => $ventasHoy,
            'ventasMes' => $ventasMes,
            'porCobrar' => $porCobrar,
            'entregasActivas' => $entregasActivas,
            'ultimasVentas' => $ultimasVentas,
            'alertasStock' => $alertasStock,
            'configGlobal' => $configGlobal
        ]);
    }
}