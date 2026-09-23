<?php
/**
 * VERIFICADOR DE INTEGRIDAD - DTE CON IVA INCLUIDO
 * 
 * Verifica que todos los cambios se hayan aplicado correctamente
 * y que el sistema esté listo para procesar ventas
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verificador de Sistema DTE</title>
    <style>
        body { font-family: monospace; background: #f0f0f0; margin: 20px; }
        .container { background: white; padding: 20px; border-radius: 5px; }
        .check { padding: 10px; margin: 5px 0; border-left: 4px solid; }
        .pass { border-color: #28a745; background: #e8f5e9; }
        .fail { border-color: #dc3545; background: #ffebee; }
        .warn { border-color: #ffc107; background: #fff3e0; }
        h2 { color: #333; }
        code { background: #f5f5f5; padding: 2px 5px; }
    </style>
</head>
<body>

<div class="container">
    <h1>🔍 Verificador de Sistema DTE - IVA Incluido</h1>
    <hr>

<?php

$basePath = __DIR__;
$checks = [];

// CHECK 1: Archivo pos_sale.php tiene prepareDTEJson actualizado
echo "<h2>1. Verificar prepareDTEJson() en pos_sale.php</h2>";
$posFile = $basePath . '/views/pos_sale.php';
if (file_exists($posFile)) {
    $content = file_get_contents($posFile);
    
    // Buscar la nueva lógica de IVA
    if (strpos($content, 'precioSinIVA = parseFloat((ventaGravadaTotal / 1.13)') !== false) {
        echo '<div class="check pass">✅ prepareDTEJson() actualizado correctamente</div>';
        $checks[] = true;
    } else {
        echo '<div class="check fail">❌ prepareDTEJson() NO está actualizado (buscar "precioSinIVA = parseFloat")</div>';
        $checks[] = false;
    }
    
    // Verificar que usa el nuevo modelo
    if (strpos($content, 'IMPORTANTE: Los precios en localCart YA INCLUYEN IVA') !== false) {
        echo '<div class="check pass">✅ Comentario sobre modelo IVA incluido presente</div>';
        $checks[] = true;
    } else {
        echo '<div class="check warn">⚠️ Comentario sobre modelo IVA no encontrado (cosmético, no crítico)</div>';
    }
} else {
    echo '<div class="check fail">❌ Archivo pos_sale.php no encontrado en ' . $posFile . '</div>';
    $checks[] = false;
}

// CHECK 2: Validador nuevo existe
echo "<h2>2. Verificar dte_validator_new.php</h2>";
$validatorFile = $basePath . '/includes/signer/utils/dte_validator_new.php';
if (file_exists($validatorFile)) {
    $content = file_get_contents($validatorFile);
    
    if (strpos($content, 'function validar_json_dte_nuevo') !== false) {
        echo '<div class="check pass">✅ dte_validator_new.php existe con función validar_json_dte_nuevo()</div>';
        $checks[] = true;
    } else {
        echo '<div class="check fail">❌ No se encontró la función validar_json_dte_nuevo()</div>';
        $checks[] = false;
    }
    
    // Verificar que contiene la lógica correcta
    if (strpos($content, 'ventaGravada - (ventaGravada / 1.13)') !== false) {
        echo '<div class="check pass">✅ Lógica de IVA extraído presente</div>';
        $checks[] = true;
    } else {
        echo '<div class="check fail">❌ Lógica de IVA no encontrada</div>';
        $checks[] = false;
    }
} else {
    echo '<div class="check fail">❌ Archivo dte_validator_new.php no encontrado en ' . $validatorFile . '</div>';
    $checks[] = false;
}

// CHECK 3: process_sale_complete.php integrado
echo "<h2>3. Verificar process_sale_complete.php integrado</h2>";
$processFile = $basePath . '/views/ajax/process_sale_complete.php';
if (file_exists($processFile)) {
    $content = file_get_contents($processFile);
    
    if (strpos($content, "require_once __DIR__ . '/../../includes/signer/utils/dte_validator_new.php'") !== false) {
        echo '<div class="check pass">✅ process_sale_complete.php requiere dte_validator_new.php</div>';
        $checks[] = true;
    } else {
        echo '<div class="check fail">❌ process_sale_complete.php no requiere dte_validator_new.php</div>';
        $checks[] = false;
    }
    
    if (strpos($content, 'validar_json_dte_nuevo') !== false) {
        echo '<div class="check pass">✅ process_sale_complete.php llama a validar_json_dte_nuevo()</div>';
        $checks[] = true;
    } else {
        echo '<div class="check fail">❌ process_sale_complete.php no llama a validar_json_dte_nuevo()</div>';
        $checks[] = false;
    }
} else {
    echo '<div class="check fail">❌ Archivo process_sale_complete.php no encontrado en ' . $processFile . '</div>';
    $checks[] = false;
}

// CHECK 4: mhControl en pos_sale.php
echo "<h2>4. Verificar inicialización de mhControl</h2>";
$posFile = $basePath . '/views/pos_sale.php';
if (file_exists($posFile)) {
    $content = file_get_contents($posFile);
    
    if (preg_match('/const mhControl\s*=\s*{[^}]*numeroControl:\s*null/', $content)) {
        echo '<div class="check pass">✅ mhControl.numeroControl inicializado como null (correcto)</div>';
        $checks[] = true;
    } else if (preg_match('/const mhControl\s*=\s*{[^}]*numeroControl:', $content)) {
        echo '<div class="check warn">⚠️ mhControl.numeroControl puede no estar null, debería generarse en servidor</div>';
    } else {
        echo '<div class="check fail">❌ mhControl no encontrado o no tiene numeroControl</div>';
        $checks[] = false;
    }
} else {
    echo '<div class="check fail">❌ Archivo pos_sale.php no encontrado</div>';
    $checks[] = false;
}

// CHECK 5: Archivos de documentación
echo "<h2>5. Verificar documentación</h2>";

$docs = [
    '/CORRECCION_IVA_INCLUIDO.md' => 'Guía técnica detallada',
    '/GUIA_RAPIDA_CORRECCION.md' => 'Guía rápida',
    '/__TESTERS/TEST_IVA_INCLUIDO.php' => 'Script de testing'
];

foreach ($docs as $path => $desc) {
    if (file_exists($basePath . $path)) {
        echo '<div class="check pass">✅ ' . $desc . ' presente (' . $path . ')</div>';
        $checks[] = true;
    } else {
        echo '<div class="check warn">⚠️ ' . $desc . ' no encontrado (' . $path . ')</div>';
    }
}

// RESUMEN
echo "<h2>📊 Resumen</h2>";
$passed = count(array_filter($checks));
$total = count($checks);
$percentage = ($total > 0) ? round(($passed / $total) * 100) : 0;

echo "<div style='font-size: 18px; margin: 20px 0;'>";
echo "Verificaciones Pasadas: <strong style='color: #28a745;'>$passed/$total ($percentage%)</strong><br>";

if ($percentage >= 90) {
    echo "<div class='check pass' style='margin-top: 10px;'>🎉 Sistema listo para producción</div>";
} elseif ($percentage >= 70) {
    echo "<div class='check warn' style='margin-top: 10px;'>⚠️ Sistema parcialmente actualizado, revisa errores</div>";
} else {
    echo "<div class='check fail' style='margin-top: 10px;'>❌ Sistema requiere más cambios, revisa errores</div>";
}

echo "</div>";

// INSTRUCCIONES
echo "<h2>🚀 Próximos Pasos</h2>";
echo "<ul>";
echo "<li>1. Revisar cualquier verificación en <span style='color: red;'>ROJO</span></li>";
echo "<li>2. Ejecutar: <code>TEST_IVA_INCLUIDO.php</code> para verificar cálculos</li>";
echo "<li>3. Crear una venta de prueba en el POS</li>";
echo "<li>4. Revisar los logs en: <code>/includes/signer/utils/logs/audit.log</code></li>";
echo "<li>5. Si hay error 094, los logs mostrarán exactamente qué falta</li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Verificador ejecutado: " . date('Y-m-d H:i:s') . "</small></p>";

?>

</div>

</body>
</html>
