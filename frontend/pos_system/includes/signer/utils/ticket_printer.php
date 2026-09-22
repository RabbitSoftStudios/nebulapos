<?php
/**
 * TICKET HTML POS
 * Archivo: /includes/goes_signer/ticket_printer.php
 * Fecha: 2026-01-08
 */

function generar_ticket_html(array $dte, string $qrPath): string
{
    ob_start();
    // Extraer datos del DTE
    $emisor = $dte['emisor']['nombre'] ?? 'Emisor Desconocido';
    $nit = $dte['emisor']['nit'] ?? '';
    $fecha = ($dte['identificacion']['fecEmi'] ?? '') . ' ' . ($dte['identificacion']['horEmi'] ?? '');
    $items = $dte['cuerpoDocumento'] ?? [];
    $total = $dte['resumen']['totalPagar'] ?? 0;
    $codigoGen = $dte['identificacion']['codigoGeneracion'] ?? '';
    ?>
    <html>
    <head>
        <style>
            body { font-family: monospace; font-size: 12px; }
            .center { text-align: center; }
            .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        </style>
    </head>
    <body>
        <div class="center">
            <strong><?= htmlspecialchars($emisor) ?></strong><br>
            NIT: <?= $nit ?><br>
            <?= $fecha ?>
        </div>

        <div class="line"></div>

        <?php foreach ($items as $item): ?>
            <?= htmlspecialchars($item['descripcion'] ?? 'Item') ?> x<?= $item['cantidad'] ?? 0 ?><br>
            <?php
                $descuento = $item['montoDescuento'] ?? 0;
                $totalRow = 0;
                
                if ($descuento > 0) {
                     $totalRow = ($item['ventaNoSuj'] ?? 0) + ($item['ventaExenta'] ?? 0) + ($item['ventaGravada'] ?? 0);
                } else {
                     $totalRow = ($item['precioUni'] ?? 0) * ($item['cantidad'] ?? 0);
                }
            ?>
            $<?= number_format($totalRow, 2) ?><br>
        <?php endforeach; ?>

        <div class="line"></div>

        TOTAL: $<?= number_format($total, 2) ?><br>

        <div class="center">
            <img src="<?= $qrPath ?>" width="120"><br>
            <?= $codigoGen ?>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}
