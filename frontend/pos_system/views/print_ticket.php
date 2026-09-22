<?php
/**
 * Print Ticket View
 * Handles the display of the ticket for printing.
 */

// Load dependencies
require_once __DIR__ . '/../includes/signer/utils/ticket_printer.php';

// Get ID
$codigoGeneracion = $_GET['id'] ?? ''; // Sanitize if needed using htmlspecialchars in output, but here it is used for file lookup

if (empty($codigoGeneracion)) {
    die("Error: Código de generación no válido.");
}

// Define paths
$storageDir = __DIR__ . '/../storage/';
$jsonPath = $storageDir . 'sigs/dte_' . $codigoGeneracion . '.json';
$qrPath = $storageDir . 'qr/qr_' . $codigoGeneracion . '.png';

// Validation
if (!file_exists($jsonPath)) {
    die("Error: No se encontró el archivo del documento (JSON).");
}

// Load DTE Data
$dteData = json_decode(file_get_contents($jsonPath), true);
if (!$dteData) {
    die("Error: JSON inválido o corrupto.");
}

// Prepare QR for embedding (Base64) to avoid path issues
$qrSrc = '';
if (file_exists($qrPath)) {
    $type = pathinfo($qrPath, PATHINFO_EXTENSION);
    $data = file_get_contents($qrPath);
    $qrSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
} else {
    // If QR doesn't exist, maybe generate it on the fly? 
    // For now, let's assume it should exist since process_sale_complete generates it.
    // Use a placeholder or simple text if missing
    $qrSrc = '#'; // Image will fail
}

// Generate HTML
// Note: generar_ticket_html might already have <html><body> tags. 
// We will output it directly.

$html = generar_ticket_html($dteData, $qrSrc);

echo $html;

// Append script to auto-print
echo '<script>
    window.onload = function() {
        window.print();
    }
</script>';
