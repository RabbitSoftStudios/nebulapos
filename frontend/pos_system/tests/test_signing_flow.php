<?php
/**
 * Test Script for Signing Flow
 * c:\wamp64\www\NebulaDET_DEV_FREE_MINI\posys\pos_system\tests\test_signing_flow.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Adjust paths relative to this script location
require_once __DIR__ . '/../includes/signer/signer_local.php';

echo "<h1>Test Signing Flow</h1>";

// 1. Mock DTE Data (Minimal valid structure)
$codigoGeneracion = 'DTE-TEST-' . time();
$dteData = [
    'identificacion' => [
        'version' => 1,
        'ambiente' => '00',
        'tipoDte' => '01',
        'numeroControl' => 'DTE-01-M001P001-000000000000001',
        'codigoGeneracion' => $codigoGeneracion,
        'tipoModelo' => 1,
        'tipoOperacion' => 1,
        'fecEmi' => date('Y-m-d'),
        'horEmi' => date('H:i:s'),
        'tipoMoneda' => 'USD',
    ],
    // ... minimal other fields to pass schema if possible, or assume relaxed schema for test
    // Actually, schema validator is strict. We might fail validation if we don't provide full DTE.
    // Let's try to load a sample or construct a very basic valid one if possible.
    // Ideally we rely on the fact that the user said "schema validator works well".
    // I represents "json content" so I might just pass a dummy if validation fails to see where it breaks.
];

// NOTE: To properly test, we'd need a valid DTE. 
// For now, let's see if we can trigger the signer at all.
// If schema validation is blocking, we will see it in the logs.

echo "<h2>1. Testing Local Signer</h2>";
echo "<p>Codigo Generacion: $codigoGeneracion</p>";

try {
    // We will attempt to sign. If schema fails, it throws, which is fine, we want to see logs.
    $signature = sign_document_local(
        ['dte_json' => json_encode($dteData)],
        $codigoGeneracion
    );
    
    echo "<p style='color:green'>Success! Signature received (length: " . strlen($signature) . ")</p>";
    echo "<textarea style='width:100%; height:100px;'>$signature</textarea>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error in local signer: " . $e->getMessage() . "</p>";
    echo "<p>Check <code>includes/signer/signer_local_debug.log</code></p>";
}

echo "<h2>2. Log Output</h2>";
$logFile = __DIR__ . '/../includes/signer/signer_local_debug.log';
if (file_exists($logFile)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
} else {
    echo "<p>No log file found.</p>";
}
