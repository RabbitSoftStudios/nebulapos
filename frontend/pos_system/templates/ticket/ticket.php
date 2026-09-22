<?php
/**
 * Sistema POS - Plantilla de Ticket
 * ===================================
 * Template para impresión de tickets térmicos
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 * * @param array $sale Datos de la venta
 * @param array $config Configuración del sistema
 */

// Incluir constantes para usar APP_NAME, DTE_EMISOR_NIT, etc.
require_once __DIR__ . '/../includes/config.php'; 
require_once __DIR__ . '/../config/constants.php';

// Configuración por defecto
$defaultConfig = [
    'width' => 80,
    'logo' => '',
    'company' => APP_NAME,
    'address' => '',
    'phone' => '',
    'tax_id' => DTE_EMISOR_NIT,
    'footer' => 'Gracias por su compra'
];
$config = array_merge($defaultConfig, $config ?? []);

// Sanity check para datos de venta
$sale = $sale ?? [
    'items' => [], 
    'totals' => ['subtotal' => 0, 'tax' => 0, 'discount' => 0, 'total' => 0],
    'payment' => ['method' => 'cash', 'cash_received' => 0, 'change' => 0],
    'timestamp' => time(),
    'id' => 'DEMO-000000',
    'customer' => null,
    'register' => '001',
    'dte' => null
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta</title>
    <style>
        @media print {
            @page {
                margin: 0;
                size: <?= $config['width'] ?>mm auto;
            }
            body {
                font-family: 'Courier New', monospace;
                font-size: 12px;
                width: <?= $config['width'] ?>mm;
                margin: 0;
                padding: 5mm;
            }
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: <?= $config['width'] ?>mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
        }
        
        .ticket-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .company-logo {
            max-width: 100%;
            max-height: 50px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 1.1em;
            text-transform: uppercase;
        }
        
        .company-info {
            font-size: 0.9em;
            margin-top: 2px;
        }
        
        .sale-info {
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        
        .sale-row {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .items-table th, .items-table td {
            font-size: 0.9em;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .items-table th {
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid #000;
        }
        
        .items-table .item-qty { text-align: right; width: 10%; }
        .items-table .item-price { text-align: right; width: 25%; }
        .items-table .item-total { text-align: right; width: 25%; font-weight: bold; }
        
        .totals-section {
            padding-top: 10px;
            border-top: 1px dashed #000;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-weight: bold;
        }
        
        .total-row.grand-total {
            font-size: 1.2em;
            margin-top: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .payment-info {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px dashed #000;
            font-size: 0.9em;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .change-row {
            color: #008000;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }

        .barcode {
            text-align: center;
            margin: 15px 0;
        }
        
        .dte-info {
            margin: 10px 0;
            padding: 10px;
            background: #f0f8ff;
            border: 1px dashed #007bff;
            border-radius: 5px;
            font-size: 10px;
            text-align: center;
        }
        
        .qr-code {
            text-align: center;
            margin: 15px 0;
        }
        
        .qr-code img {
            max-width: 150px;
            height: auto;
        }
        
        .terms {
            font-size: 9px;
            color: #666;
            margin-top: 10px;
            text-align: justify;
        }
        
        .divider {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        
        .no-wrap { 
            white-space: nowrap; 
        }
    </style>
</head>
<body>
    <div class="ticket-header">
        <?php if (!empty($config['logo'])): ?>
            <img src="<?= htmlspecialchars($config['logo']) ?>" alt="Logo" class="company-logo">
        <?php endif; ?>
        <div class="company-name"><?= htmlspecialchars($config['company']) ?></div>
        <?php if (!empty($config['address'])): ?>
            <div class="company-info"><?= htmlspecialchars($config['address']) ?></div>
        <?php endif; ?>
        <?php if (!empty($config['phone'])): ?>
            <div class="company-info">Tel: <?= htmlspecialchars($config['phone']) ?></div>
        <?php endif; ?>
        <?php if (!empty($config['tax_id'])): ?>
            <div class="company-info">NIT: <?= htmlspecialchars($config['tax_id']) ?></div>
        <?php endif; ?>
        <div class="company-info">
            <?= date('d/m/Y H:i:s', $sale['timestamp'] ?? time()) ?> | Caja: <?= $sale['register'] ?? '001' ?>
        </div>
    </div>
    
    <div class="sale-info">
        <div class="sale-row">
            <span>Ticket No.:</span>
            <span class="no-wrap"><?= htmlspecialchars($sale['id'] ?? 'N/A') ?></span>
        </div>
        <?php if ($sale['customer']): ?>
            <div class="sale-row">
                <span>Cliente:</span>
                <span class="no-wrap"><?= htmlspecialchars($sale['customer']['name'] ?? 'Consumidor Final') ?></span>
            </div>
            <div class="sale-row">
                <span>Doc. Tributario:</span>
                <span class="no-wrap"><?= htmlspecialchars($sale['customer']['documentNumber'] ?? 'N/A') ?></span>
            </div>
        <?php else: ?>
            <div class="sale-row">
                <span>Cliente:</span>
                <span class="no-wrap">Consumidor Final</span>
            </div>
        <?php endif; ?>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="item-qty">Cant.</th>
                <th class="item-price">Precio</th>
                <th class="item-total">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sale['items'] ?? [] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td class="item-qty"><?= number_format($item['quantity'], 0) ?></td>
                    <td class="item-price">$<?= number_format($item['price'], 2) ?></td>
                    <td class="item-total">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="totals-section">
        <div class="total-row">
            <span>SUBTOTAL:</span>
            <span>$<?= number_format($sale['totals']['subtotal'] ?? 0, 2) ?></span>
        </div>
        <div class="total-row">
            <span>IVA (<?= (IVA_RATE * 100) ?>%):</span>
            <span>$<?= number_format($sale['totals']['tax'] ?? 0, 2) ?></span>
        </div>
        <?php if (($sale['totals']['discount'] ?? 0) > 0): ?>
        <div class="total-row">
            <span>DESCUENTO:</span>
            <span>-$<?= number_format($sale['totals']['discount'] ?? 0, 2) ?></span>
        </div>
        <?php endif; ?>
        <div class="total-row grand-total">
            <span>TOTAL A PAGAR:</span>
            <span>$<?= number_format($sale['totals']['total'] ?? 0, 2) ?></span>
        </div>
    </div>
    
    <div class="payment-info">
        <div class="payment-row">
            <span>Método de pago:</span>
            <span>
                <?= match($sale['payment']['method'] ?? 'cash') {
                    'cash' => 'EFECTIVO',
                    'card' => 'TARJETA',
                    'transfer' => 'TRANSFERENCIA',
                    'mixed' => 'MIXTO',
                    default => strtoupper($sale['payment']['method'])
                } ?>
            </span>
        </div>
        <?php if (($sale['payment']['method'] ?? 'cash') === 'cash'): ?>
            <div class="payment-row">
                <span>Efectivo recibido:</span>
                <span>$<?= number_format($sale['payment']['cash_received'] ?? 0, 2) ?></span>
            </div>
            <?php if (($sale['payment']['change'] ?? 0) > 0): ?>
                <div class="payment-row change-row">
                    <span>Cambio:</span>
                    <span>$<?= number_format($sale['payment']['change'] ?? 0, 2) ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($sale['payment']['reference'])): ?>
            <div class="payment-row">
                <span>Referencia:</span>
                <span><?= htmlspecialchars($sale['payment']['reference']) ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($sale['dte']): ?>
    <div class="dte-info">
        <div class="company-name">DOCUMENTO TRIBUTARIO ELECTRÓNICO</div>
        <div>TIPO DTE: <?= htmlspecialchars($sale['dte']['identificacion']['tipoDte'] ?? 'N/A') ?></div>
        <div>N° CONTROL: <?= htmlspecialchars($sale['dte']['identificacion']['numeroControl'] ?? 'N/A') ?></div>
        <div>CÓDIGO GENERACIÓN: <?= htmlspecialchars($sale['dte']['identificacion']['codigoGeneracion'] ?? 'N/A') ?></div>
        <div>SELLADO POR DGII: SÍ</div>
    </div>
    <div class="qr-code">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($sale['dte']['link_validacion'] ?? 'DTE-PENDIENTE') ?>" alt="QR Code">
    </div>
    <?php endif; ?>
    
    <div class="footer">
        <p><?= htmlspecialchars($config['footer']) ?></p>
        <p>Sistema POS v<?= APP_VERSION ?></p>
    </div>
</body>
</html>
