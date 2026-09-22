<?php
/**
 * GENERADOR PDF FACTURA
 * Archivo: /includes/goes_signer/pdf_generator.php
 * Fecha: 2026-01-08
 */

/* require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;

function generar_factura_pdf(string $html, string $codigoGeneracion): string
{
    $pdfDir = __DIR__ . '/../../temp/pdf/';
    if (!is_dir($pdfDir)) mkdir($pdfDir, 0755, true);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter', 'portrait');
    $dompdf->render();

    $file = $pdfDir . 'factura_' . $codigoGeneracion . '.pdf';
    file_put_contents($file, $dompdf->output());

    return $file;
}
 */

require_once __DIR__ . '/../../../../../vendor/autoload.php';

use Dompdf\Dompdf;

function generar_factura_pdf(string $html, string $codigoGeneracion): string
{
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $path = __DIR__ . '/../../../storage/pdf/';
    if (!is_dir($path)) mkdir($path, 0777, true);

    $file = $path . "factura_{$codigoGeneracion}.pdf";
    file_put_contents($file, $dompdf->output());

    return $file;
}
