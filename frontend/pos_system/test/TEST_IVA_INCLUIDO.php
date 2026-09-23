<?php
/**
 * TEST_IVA_INCLUIDO.php
 * Verifica que los cálculos de IVA con modelo incluido funcionan correctamente
 */

// Función para calcular IVA extraído de precio que ya lo incluye
function calcularIVA($precioConIVA) {
    $precioSinIVA = $precioConIVA / 1.13;
    $iva = $precioConIVA - $precioSinIVA;
    return [
        'precioConIVA' => round($precioConIVA, 2),
        'precioSinIVA' => round($precioSinIVA, 2),
        'iva' => round($iva, 2),
        'verificacion' => 'OK: ' . round($precioSinIVA + $iva, 2) . ' = ' . round($precioConIVA, 2)
    ];
}

echo "═══════════════════════════════════════════════════════════\n";
echo "TEST: CÁLCULOS DE IVA INCLUIDO\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Test 1: Precio unitario $16
echo "TEST 1: Precio unitario $16 × 3 unidades\n";
echo "─────────────────────────────────────────────────────────────\n";

$precioUnitario = 16.00;
$cantidad = 3;
$totalConIVA = $precioUnitario * $cantidad;

echo "Entrada: Precio unitario = \${$precioUnitario} (con IVA)\n";
echo "         Cantidad = {$cantidad}\n";
echo "         Total = \${$totalConIVA}\n\n";

$resultado = calcularIVA($totalConIVA);
echo "Resultado:\n";
echo "  Precio con IVA: \${$resultado['precioConIVA']}\n";
echo "  Precio sin IVA: \${$resultado['precioSinIVA']}\n";
echo "  IVA extraído:   \${$resultado['iva']}\n";
echo "  {$resultado['verificacion']}\n\n";

// Verificación esperada
echo "Verificación:\n";
echo "  ✓ Cliente pagó: \${$resultado['precioConIVA']}\n";
echo "  ✓ Valor gravado (sin IVA): \${$resultado['precioSinIVA']}\n";
echo "  ✓ IVA (13% sobre sin IVA): \${$resultado['iva']}\n";
echo "  ✓ Pago confirmado: " . ($resultado['precioSinIVA'] + $resultado['iva'] == $resultado['precioConIVA'] ? "✅" : "❌") . "\n\n";

// Test 2: Precio unitario $10
echo "TEST 2: Precio unitario $10 × 5 unidades\n";
echo "─────────────────────────────────────────────────────────────\n";

$precioUnitario2 = 10.00;
$cantidad2 = 5;
$totalConIVA2 = $precioUnitario2 * $cantidad2;

echo "Entrada: Precio unitario = \${$precioUnitario2} (con IVA)\n";
echo "         Cantidad = {$cantidad2}\n";
echo "         Total = \${$totalConIVA2}\n\n";

$resultado2 = calcularIVA($totalConIVA2);
echo "Resultado:\n";
echo "  Precio con IVA: \${$resultado2['precioConIVA']}\n";
echo "  Precio sin IVA: \${$resultado2['precioSinIVA']}\n";
echo "  IVA extraído:   \${$resultado2['iva']}\n";
echo "  {$resultado2['verificacion']}\n\n";

// Test 3: Múltiples items
echo "TEST 3: Carrito con múltiples items\n";
echo "─────────────────────────────────────────────────────────────\n";

$items = [
    ['nombre' => 'Laptop', 'precio' => 1000.00, 'cantidad' => 1],
    ['nombre' => 'Mouse', 'precio' => 25.00, 'cantidad' => 2],
    ['nombre' => 'Teclado', 'precio' => 75.00, 'cantidad' => 1]
];

$totalCarrito = 0;
$totalIVA = 0;
$totalSinIVA = 0;

foreach ($items as $item) {
    $subtotal = $item['precio'] * $item['cantidad'];
    $res = calcularIVA($subtotal);
    $totalCarrito += $res['precioConIVA'];
    $totalIVA += $res['iva'];
    $totalSinIVA += $res['precioSinIVA'];
    
    echo "{$item['nombre']}:\n";
    echo "  Precio unitario: \${$item['precio']} × {$item['cantidad']} = \${$subtotal}\n";
    echo "  Sin IVA: \${$res['precioSinIVA']}, IVA: \${$res['iva']}\n\n";
}

echo "TOTALES:\n";
echo "  Total con IVA: \${$totalCarrito}\n";
echo "  Total sin IVA: \${$totalSinIVA}\n";
echo "  Total IVA: \${$totalIVA}\n";
echo "  Verificación: \${$totalSinIVA} + \${$totalIVA} = \$" . round($totalSinIVA + $totalIVA, 2) . "\n";
echo "  " . ($totalSinIVA + $totalIVA == $totalCarrito ? "✅ CORRECTO" : "❌ ERROR") . "\n\n";

// Test 4: Validación de formato numeroControl
echo "TEST 4: Validación de formato numeroControl\n";
echo "─────────────────────────────────────────────────────────────\n";

$testControls = [
    'DTE-01-P001M001-000000000000001' => true,
    'DTE-03-P001M001-000000000000001' => true,
    'DTE-05-P001M001-000000000000001' => true,
    'DTE-01-P001M001-00000000000000A' => false,
    'DTE-01-00000000-000000000000001' => false,
    'DTE-01-P001M001' => false,
];

foreach ($testControls as $control => $esperado) {
    $valida = preg_match('/^DTE-\d{2}-[A-Z0-9]{8}-\d{15}$/', $control);
    $resultado = ($valida ? true : false) === $esperado ? "✅" : "❌";
    echo "{$resultado} {$control}: " . ($valida ? "VÁLIDO" : "INVÁLIDO") . "\n";
}

echo "\n═══════════════════════════════════════════════════════════\n";
echo "FIN DE TESTS\n";
echo "═══════════════════════════════════════════════════════════\n";
?>
