<?php

require_once __DIR__ . '/pdf_generator.php';
require_once __DIR__ . '/../../pg_connection.php';

$config = obtener_config_empresa($dteData['emisor']['nit']);

$pdfPath = generar_factura_pdf(
    $dteData,
    $config,
    $qrPath
);
