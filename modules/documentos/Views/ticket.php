<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?= $venta['id'] ?></title>
    <style>
        @page { margin: 0; }
        body { 
            margin: 0; 
            padding: 2mm; 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 12px; 
            color: #000; 
            background: white; 
            width: <?= htmlspecialchars($configDoc['ticket_ancho'] ?? '80mm') ?>;
            box-sizing: border-box;
        }
        
        .ticket-header { text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .ticket-header h2 { font-size: 16px; margin: 0 0 5px 0; font-weight: bold; text-transform: uppercase; }
        .ticket-header p { margin: 2px 0; font-size: 12px; }
        .ticket-logo { max-width: 80%; height: auto; margin-bottom: 5px; }
        
        .ticket-info { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; font-size: 12px; }
        .ticket-info p { margin: 2px 0; }
        
        .ticket-table { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; border-collapse: collapse; width: 100%; }
        .ticket-table th { text-align: left; border-bottom: 1px solid #000; font-size: 11px; padding-bottom: 2px; }
        .ticket-table td { padding: 3px 0; font-size: 11px; vertical-align: top; }
        .ticket-table .center { text-align: center; }
        .ticket-table .right { text-align: right; }
        
        .ticket-totales { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .totales-row { display: flex; justify-content: space-between; margin: 2px 0; font-size: 12px; }
        .total-final { font-size: 14px; font-weight: bold; margin-top: 5px; border-top: 1px solid #000; padding-top: 5px; }

        .ticket-footer { text-align: center; font-size: 11px; margin-top: 10px; }
        .ticket-footer p { margin: 2px 0; }
        
        /* Auto-imprimir al cargar y ocultar la alerta */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">
    
    <div class="no-print" style="margin-bottom:10px; text-align:center; padding:10px; background:#f0f0f0; border:1px solid #ccc;">
        Imprimiendo ticket... Si no aparece el cuadro, <button onclick="window.print()">clic aquí</button>.<br>
        <small>Puede cerrar esta pestaña después de imprimir.</small>
    </div>

    <div class="ticket-header">
        <?php if(!empty($configDoc['imprimir_logo']) && !empty($sysConfig['BUSINESS_LOGO'])): ?>
            <img src="<?= (strpos($sysConfig['BUSINESS_LOGO'], 'http') === 0 ? '' : base_url()) . htmlspecialchars($sysConfig['BUSINESS_LOGO']) ?>" class="ticket-logo">
        <?php endif; ?>
        <h2><?= htmlspecialchars($sysConfig['APP_NAME'] ?? 'Mueblería San Martín') ?></h2>
        <p>Ticket de <?= $venta['tipo'] === 'cotizacion' ? 'Cotización' : 'Venta' ?></p>
        <p>Folio: #<?= str_pad($venta['id'], 5, '0', STR_PAD_LEFT) ?></p>
        <p>Fecha: <?= date('d/m/Y h:i A', strtotime($venta['fecha_creacion'])) ?></p>
    </div>
    
    <div class="ticket-info">
        <p>Atendió: <?= htmlspecialchars($venta['usuario_nombre'] ?? 'Sistema') ?></p>
        <p>Cliente: <?= htmlspecialchars($venta['cliente_nombre'] ?? 'Público en General') ?></p>
    </div>

    <table class="ticket-table">
        <thead><tr><th class="center" style="width: 15%">CANT</th><th style="width: 55%">ARTÍCULO</th><th class="right" style="width: 30%">IMPORTE</th></tr></thead>
        <tbody>
            <?php foreach($detalles as $d): ?>
                <tr><td class="center"><?= $d['cantidad'] ?></td><td><?= htmlspecialchars($d['producto_nombre']) ?></td><td class="right">$<?= number_format($d['subtotal'], 2) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="ticket-totales">
        <?php 
            $notas_limpias = $venta['notas_internas'] ?? '';
            $promos_aplicadas = '';
            // Extraer automáticamente las promociones si existen
            if (strpos($notas_limpias, '[PROMOCIONES APLICADAS]') !== false) {
                $partes = explode('[PROMOCIONES APLICADAS]', $notas_limpias);
                $promos_aplicadas = trim($partes[1]);
            }
        ?>
        <div class="totales-row"><span>Subtotal:</span> <span>$<?= number_format($venta['subtotal'], 2) ?></span></div>
        <?php if($venta['costo_envio'] > 0): ?><div class="totales-row"><span>Envío:</span> <span>+$<?= number_format($venta['costo_envio'], 2) ?></span></div><?php endif; ?>
        <?php if($venta['descuento'] > 0): ?>
            <div class="totales-row"><span>Descuento:</span> <span>-$<?= number_format($venta['descuento'], 2) ?></span></div>
            <?php if(!empty($promos_aplicadas)): ?>
                <div style="font-size: 10px; text-align: right; margin-bottom: 2px; font-style: italic; line-height: 1.2; padding-left: 20%;"><?= nl2br(htmlspecialchars($promos_aplicadas)) ?></div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="totales-row total-final"><span>TOTAL:</span> <span>$<?= number_format($venta['total'], 2) ?></span></div>
    </div>

    <?php if($venta['tipo'] === 'venta'): ?>
    <div class="ticket-info">
        <?php
            $monto_recibido = $venta['monto_recibido'] ?? 0;
            $restante = max(0, $venta['total'] - $monto_recibido);
            $suma_abonos = isset($abonos) ? array_sum(array_column($abonos, 'monto')) : 0;
            $enganche = max(0, $monto_recibido - $suma_abonos);
        ?>
        <div class="totales-row"><span>Pago Inicial/Enganche:</span> <span>$<?= number_format($enganche, 2) ?></span></div>
        <?php if(isset($abonos) && count($abonos) > 0): ?>
            <?php foreach($abonos as $a): ?>
                <div class="totales-row"><span>Abono (<?= date('d/m/y', strtotime($a['fecha_abono'])) ?>):</span> <span>+$<?= number_format($a['monto'], 2) ?></span></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if(isset($venta['cambio']) && $venta['cambio'] > 0): ?>
            <div class="totales-row"><span>Cambio Devuelto:</span> <span>$<?= number_format($venta['cambio'], 2) ?></span></div>
        <?php endif; ?>
        <div class="totales-row total-final"><span>RESTA POR PAGAR:</span> <span>$<?= number_format($restante, 2) ?></span></div>
        <p style="text-align:center; margin-top:8px;">Estado: <b><?= strtoupper($venta['estado_pago']) ?></b></p>
    </div>
    <?php endif; ?>

    <div class="ticket-footer"><p><?= htmlspecialchars($configDoc['ticket_saludo'] ?? '') ?></p><p><?= htmlspecialchars($configDoc['ticket_pie'] ?? '') ?></p></div>

</body>
</html>