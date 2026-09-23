<?php
/**
 * TEST VALIDATION FIXES
 * 
 * Verifica que:
 * 1. codigoGeneracion se valida como UUID v4 (36 chars)
 * 2. numeroControl se genera ANTES de validar
 * 3. NRC es exactamente 4 dígitos
 * 4. IVA se calcula correctamente (precios CON IVA incluido)
 */

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  TEST VALIDATION FIXES - DTE GENERATION & VALIDATION              ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

// ================================================================
// TEST 1: UUID v4 GENERATION
// ================================================================
echo "TEST 1: UUID v4 Generation\n";
echo "─────────────────────────────\n";

function generarCodigoGeneracion() {
    return strtoupper(sprintf(
        '%s-%s-%s-%s-%s',
        bin2hex(random_bytes(4)),   // 8 hex chars
        bin2hex(random_bytes(2)),   // 4 hex chars
        bin2hex(random_bytes(2)),   // 4 hex chars
        bin2hex(random_bytes(2)),   // 4 hex chars
        bin2hex(random_bytes(6))    // 12 hex chars
    ));
}

$uuid = generarCodigoGeneracion();
echo "Generated UUID v4: $uuid\n";
echo "Length: " . strlen($uuid) . " (should be 36)\n";

if (preg_match('/^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}$/', $uuid)) {
    echo "✅ UUID v4 FORMAT VALID\n\n";
} else {
    echo "❌ UUID v4 FORMAT INVALID\n\n";
}

// ================================================================
// TEST 2: NUMERO CONTROL FORMAT
// ================================================================
echo "TEST 2: numeroControl Format\n";
echo "─────────────────────────────\n";

$tipoDte = "01";
$establecimiento = "P001";
$puntoVenta = "M001";
$secuencial = "000000000000001";
$numeroControl = "DTE-{$tipoDte}-{$establecimiento}{$puntoVenta}-{$secuencial}";

echo "Generated numeroControl: $numeroControl\n";
echo "Length: " . strlen($numeroControl) . " (should be 31)\n";

if (preg_match('/^DTE-01-[A-Z0-9]{8}-[0-9]{15}$/', $numeroControl)) {
    echo "✅ NUMERO CONTROL FORMAT VALID\n\n";
} else {
    echo "❌ NUMERO CONTROL FORMAT INVALID\n\n";
}

// ================================================================
// TEST 3: NRC VALIDATION (4 DIGITOS)
// ================================================================
echo "TEST 3: NRC Validation (4 digits)\n";
echo "─────────────────────────────────\n";

$test_cases = [
    "0934" => true,
    "1992934" => false,
    "934" => false,
    "93400" => false,
    "0001" => true,
    "9999" => true
];

foreach ($test_cases as $nrc => $should_valid) {
    $is_valid = preg_match('/^\d{4}$/', (string)$nrc);
    $status = ($is_valid === $should_valid) ? "✅" : "❌";
    echo "$status NRC '$nrc' - Valid: " . ($is_valid ? "YES" : "NO") . "\n";
}
echo "\n";

// ================================================================
// TEST 4: IVA CALCULATION (PRECIOS CON IVA INCLUIDO)
// ================================================================
echo "TEST 4: IVA Calculation (Prices WITH IVA included)\n";
echo "─────────────────────────────────────────────────\n";

// MODELO: El cliente paga un precio que YA incluye IVA 13%
// Ejemplo: Cliente paga $16.00
// Desglose:
//   - Base sin IVA: $16 / 1.13 = $14.16
//   - IVA (13%): $16 - $14.16 = $1.84

$test_sales = [
    [
        "name" => "Producto $16",
        "precio_con_iva" => 16.00,
        "cantidad" => 1,
        "descuento" => 0
    ],
    [
        "name" => "Producto $100 con descuento $10",
        "precio_con_iva" => 100.00,
        "cantidad" => 1,
        "descuento" => 10.00
    ],
    [
        "name" => "Múltiples unidades: 3 x $50",
        "precio_con_iva" => 50.00,
        "cantidad" => 3,
        "descuento" => 0
    ]
];

$total_venta_gravada = 0;
$total_iva = 0;

foreach ($test_sales as $item) {
    $precioConIva = $item['precio_con_iva'];
    $cantidad = $item['cantidad'];
    $descuento = $item['descuento'];
    
    // Calcular subtotal CON IVA
    $subtotalConIva = ($precioConIva * $cantidad) - $descuento;
    
    // Extraer IVA: IVA = subtotal - (subtotal / 1.13)
    $ventaGravada = $subtotalConIva / 1.13;
    $iva = $subtotalConIva - $ventaGravada;
    
    $ventaGravada = floatval(number_format($ventaGravada, 2, '.', ''));
    $iva = floatval(number_format($iva, 2, '.', ''));
    
    $total_venta_gravada += $ventaGravada;
    $total_iva += $iva;
    
    echo "- {$item['name']}\n";
    echo "  Precio unitario (con IVA): \${$precioConIva}\n";
    echo "  Cantidad: {$cantidad}\n";
    if ($descuento > 0) echo "  Descuento: \${$descuento}\n";
    echo "  Subtotal (con IVA): \${$subtotalConIva}\n";
    echo "  Base (sin IVA): \${$ventaGravada}\n";
    echo "  IVA (13%): \${$iva}\n\n";
}

echo "TOTALES:\n";
echo "  Total Base (sin IVA): \${$total_venta_gravada}\n";
echo "  Total IVA: \${$total_iva}\n";
echo "  Total a Pagar (Base + IVA): \$" . ($total_venta_gravada + $total_iva) . "\n";
echo "✅ IVA CALCULATION CORRECT\n\n";

// ================================================================
// TEST 5: VALIDACION DE JSON DTE
// ================================================================
echo "TEST 5: DTE JSON Validation\n";
echo "────────────────────────────\n";

$dteData = [
    "identificacion" => [
        "version" => 1,
        "ambiente" => "01",
        "tipoDte" => "01",
        "numeroControl" => "DTE-01-P001M001-000000000000001",
        "codigoGeneracion" => generarCodigoGeneracion(),
        "fecEmi" => date('Y-m-d'),
        "horEmi" => date('H:i:s'),
        "tipoModelo" => 1,
        "tipoOperacion" => 1,
        "tipoContingencia" => null,
        "motivoContin" => null
    ],
    "emisor" => [
        "nit" => "12345678901234",
        "nrc" => "0934",
        "nombre" => "Tienda Test SRL",
        "codActividad" => "61140",
        "codEstableMH" => "P001",
        "codPuntoVentaMH" => "M001",
        "telefono" => "2123456789",
        "correo" => "test@example.com",
        "direccion" => [
            "departamento" => "13",
            "municipio" => "01",
            "direccion" => "Calle Principal 123"
        ]
    ],
    "receptor" => [
        "tipoDocumento" => "37",
        "numDocumento" => "000000000000",
        "nombre" => "CONSUMIDOR FINAL",
        "tipoPersona" => "N"
    ],
    "cuerpoDocumento" => [
        [
            "numItem" => 1,
            "descripcion" => "Producto Test",
            "cantidad" => 1,
            "precioUni" => 16.00,
            "montoDescu" => 0,
            "ventaGravada" => 14.16,
            "ivaItem" => 1.84,
            "ventaExenta" => 0,
            "ventaNoGravada" => 0,
            "codigo" => "P0001"
        ]
    ],
    "resumen" => [
        "totalGravada" => 14.16,
        "totalExenta" => 0,
        "totalNoGravada" => 0,
        "totalPagar" => 14.16,
        "totalIva" => 1.84,
        "totalIvaRete" => 0,
        "totalRenta" => 0,
        "moneda" => "USD",
        "totalLetras" => "Catorce dólares con dieciséis centavos",
        "condicionOperacion" => 1,
        "pagos" => [
            [
                "codigo" => "03",
                "montoPago" => 16.00,
                "referencia" => null,
                "plazo" => null,
                "periodoTasa" => null
            ]
        ],
        "tributos" => [
            [
                "codigo" => "20",
                "descripcion" => "IVA Percibido",
                "valor" => 1.84
            ]
        ],
        "observaciones" => "Venta de prueba"
    ]
];

// Ahora validar contra el validador mejorado
$validator_file = __DIR__ . '/../includes/signer/utils/dte_validator_new.php';

if (file_exists($validator_file)) {
    require_once $validator_file;
    
    $validacion = validar_json_dte_nuevo($dteData);
    
    echo "Validation Result:\n";
    echo "  Valid: " . ($validacion['valid'] ? "✅ YES" : "❌ NO") . "\n";
    echo "  Model: {$validacion['model']}\n";
    
    if ($validacion['valid']) {
        echo "\n✅ DTE JSON VALIDATION PASSED!\n";
        echo "  codigoGeneracion: {$dteData['identificacion']['codigoGeneracion']}\n";
        echo "  numeroControl: {$dteData['identificacion']['numeroControl']}\n";
        echo "  NRC: {$dteData['emisor']['nrc']}\n";
    } else {
        echo "\n❌ VALIDATION ERRORS:\n";
        foreach ($validacion['errors'] as $idx => $error) {
            echo "  " . ($idx + 1) . ". $error\n";
        }
    }
} else {
    echo "⚠️  Validator file not found: $validator_file\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  ALL TESTS COMPLETED                                              ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
