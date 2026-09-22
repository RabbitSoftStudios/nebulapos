<?php

use Dompdf\Dompdf;
use Dompdf\Options;

function generar_factura_pdf(array $dte, array $config, string $qrPath): string
{
    $options = new Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);

    $template = match ($config['formato_factura']) {
        'CARTA_MODERNA' => 'factura_carta_moderna.php',
        'CARTA_SIMPLE'  => 'factura_carta_simple.php',
        default         => 'factura_carta_clasica.php',
    };

    ob_start();
    require __DIR__ . "/../templates/factura/{$template}";
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter', 'portrait');
    $dompdf->render();

    $path = __DIR__ . "/../../storage/pdfs/";
    if (!is_dir($path)) mkdir($path, 0777, true);

    $file = $path . "factura_{$dte['identificacion']['codigoGeneracion']}.pdf";
    file_put_contents($file, $dompdf->output());

    return $file;
}
