<?php
/**
 * QR MAKER – MH EL SALVADOR
 * Archivo: /includes/goes_signer/qr_maker.php
 * Fecha: 2026-01-08
 */

/* require_once __DIR__ . '/../../vendor/phpqrcode/qrlib.php';

function generar_qr_mh(array $data): string
{
    $qrDir = __DIR__ . '/../../temp/qr/';
    if (!is_dir($qrDir)) mkdir($qrDir, 0755, true);

    $contenido = implode('|', [
        $data['codigoGeneracion'],
        $data['numeroControl'],
        $data['selloRecepcion'],
        $data['fechaEmision'],
        $data['totalPagar']
    ]);

    $file = $qrDir . 'qr_' . $data['codigoGeneracion'] . '.png';

    QRcode::png($contenido, $file, QR_ECLEVEL_Q, 6);

    return $file;
}

 */

require_once __DIR__ . '/../../../../../vendor/autoload.php';

function generar_qr_mh(array $data): string
{
    $baseUrl = 'https://admin.factura.gob.sv/consultaPublica';

    $query = http_build_query([
        'codigoGeneracion' => $data['codigoGeneracion'],
        'numeroControl'    => $data['numeroControl'],
        'fechaEmision'     => date('Y-m-d')
    ]);

    $url = $baseUrl . '?' . $query;

    // Create the directory if it doesn't exist
    $path = __DIR__ . '/../../../storage/qr/';
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    
    $fileName = 'qr_' . $data['codigoGeneracion'] . '.png';
    $file = $path . $fileName;

    // Detect version and generate
    if (class_exists('Endroid\QrCode\Writer\PngWriter')) {
        // Version 5+ (or 4 with Writer support)
        // Instantiate QrCode
        $qrCode = new \Endroid\QrCode\QrCode($url);
        
        // Handle immutable setters if applicable, or mutable setters
        // In v5, setters return new instance. In v4/v3 mutable returns $this.
        // Re-assignment covers both cases safely.
        
        if (method_exists($qrCode, 'setEncoding')) {
            $qrCode = $qrCode->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'));
        }
        
        if (method_exists($qrCode, 'setSize')) {
            $qrCode = $qrCode->setSize(300);
        }
        
        if (method_exists($qrCode, 'setMargin')) {
             $qrCode = $qrCode->setMargin(10);
        }
        
        // Use Writer
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($file);
        
    } else {
        // Version 3 (Legacy)
        // Class Endroid\QrCode\QrCode handles everything
        $qrCode = new \Endroid\QrCode\QrCode($url);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->writeFile($file);
    }

    return $file;
}
