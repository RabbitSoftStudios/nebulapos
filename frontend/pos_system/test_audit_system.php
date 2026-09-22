<?php
/**
 * SCRIPT DE PRUEBA RÁPIDA - Validación del Sistema
 * Ejecutar: php test_audit_system.php
 */

echo "═══════════════════════════════════════════════════════════════\n";
echo "     TEST DE SISTEMA DE AUDITORÍA DE FACTURAS ELECTRÓNICAS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// 1. Verificar archivos creados
echo "1️⃣  VERIFICANDO ARCHIVOS:\n";
$files_to_check = [
    'includes/signer/utils/audit_logger.php' => 'Logger centralizado',
    'includes/signer/utils/dte_validator.php' => 'Validador de DTE',
    'includes/signer/signer_local.php' => 'Firmador local actualizado',
    'includes/signer/signer_goes.php' => 'Firmador gobierno actualizado',
    'views/ajax/process_sale_complete.php' => 'Procesador de venta actualizado',
    'DIAGNOSTICO_ERROR_094.md' => 'Guía de diagnóstico',
    'RESUMEN_SOLUCION.md' => 'Resumen de solución'
];

$all_ok = true;
foreach ($files_to_check as $path => $desc) {
    if (file_exists($path)) {
        $size = filesize($path);
        echo "   ✅ $path ($size bytes) - $desc\n";
    } else {
        echo "   ❌ $path - FALTA\n";
        $all_ok = false;
    }
}

echo "\n2️⃣  VERIFICANDO FUNCIONES:\n";

// Incluir audit_logger
require_once 'includes/signer/utils/audit_logger.php';
require_once 'includes/signer/utils/dte_validator.php';

$functions_to_check = [
    'audit_log' => 'Logger base',
    'audit_log_json_generated' => 'Log JSON generado',
    'audit_log_firma_local_response' => 'Log respuesta firmador',
    'audit_log_mh_payload' => 'Log payload MH',
    'audit_log_mh_response' => 'Log respuesta MH',
    'audit_log_final_summary' => 'Log resumen final',
    'audit_log_error' => 'Log de errores',
    'validar_json_dte_completo' => 'Validador DTE',
    'print_validation_report' => 'Reporte de validación'
];

foreach ($functions_to_check as $func => $desc) {
    if (function_exists($func)) {
        echo "   ✅ $func() - $desc\n";
    } else {
        echo "   ❌ $func() - NO EXISTE\n";
        $all_ok = false;
    }
}

echo "\n3️⃣  VERIFICANDO PERMISOS DE ESCRITURA:\n";

$dirs_to_check = [
    'includes/signer/' => 'Signer directory',
    'storage/sigs/' => 'Storage sigs directory'
];

foreach ($dirs_to_check as $dir => $desc) {
    if (is_writable($dir)) {
        echo "   ✅ $dir - Escribible\n";
    } else {
        echo "   ⚠️  $dir - REVISAR PERMISOS\n";
    }
}

echo "\n4️⃣  PRUEBA DE LOG WRITING:\n";

$test_log = 'includes/signer/audit_dte_complete.log';
if (file_exists($test_log)) {
    $lines = count(explode("\n", file_get_contents($test_log)));
    echo "   ✅ Log existe con ~$lines líneas\n";
} else {
    echo "   ℹ️  Log no existe aún (se creará en primera venta)\n";
}

echo "\n5️⃣  PRUEBA DE VALIDADOR:\n";

// JSON de ejemplo válido
$dte_valido = [
    'identificacion' => [
        'version' => 1,
        'ambiente' => '00',
        'tipoDte' => '01',
        'numeroControl' => 'DTE-01-P001M001-000000000000001',
        'codigoGeneracion' => '12345678-1234-5678-1234-567812345678',
        'tipoModelo' => 1,
        'tipoOperacion' => 1,
        'tipoContingencia' => null,
        'motivoContin' => null,
        'fecEmi' => '2026-01-20',
        'horEmi' => '18:19:00',
        'tipoMoneda' => 'USD'
    ],
    'emisor' => [
        'nit' => '06150911851010',
        'nrc' => '1992934',
        'nombre' => 'Test Company',
        'codActividad' => '46510',
        'descActividad' => 'Venta de productos',
        'nombreComercial' => null,
        'tipoEstablecimiento' => '02',
        'direccion' => [
            'departamento' => '06',
            'municipio' => '23',
            'complemento' => 'Test address'
        ],
        'telefono' => '21212121',
        'correo' => 'test@example.com',
        'codEstableMH' => 'P001',
        'codEstable' => 'P001',
        'codPuntoVentaMH' => 'M001',
        'codPuntoVenta' => 'M001'
    ],
    'receptor' => [
        'tipoDocumento' => null,
        'numDocumento' => null,
        'nrc' => null,
        'nombre' => 'Test Customer',
        'codActividad' => '10005',
        'descActividad' => 'Otros',
        'direccion' => [
            'departamento' => '06',
            'municipio' => '23',
            'complemento' => 'Customer address'
        ],
        'telefono' => null,
        'correo' => 'customer@example.com'
    ],
    'otrosDocumentos' => null,
    'ventaTercero' => null,
    'cuerpoDocumento' => [
        [
            'tipoItem' => 1,
            'numeroDocumento' => null,
            'codTributo' => null,
            'descripcion' => 'Test Product',
            'cantidad' => 1.0,
            'uniMedida' => 59,
            'precioUni' => 100.0,
            'montoDescu' => 0.0,
            'ventaNoSuj' => 0.0,
            'ventaExenta' => 0.0,
            'ventaGravada' => 100.0,
            'tributos' => null,
            'psv' => 0.0,
            'noGravado' => 0.0,
            'codigo' => '001',
            'ivaItem' => 13.0,
            'numItem' => 1
        ]
    ],
    'resumen' => [
        'totalNoSuj' => 0.0,
        'totalExenta' => 0.0,
        'totalGravada' => 100.0,
        'subTotalVentas' => 100.0,
        'descuNoSuj' => 0.0,
        'descuExenta' => 0.0,
        'descuGravada' => 0.0,
        'porcentajeDescuento' => 0.0,
        'totalDescu' => 0.0,
        'tributos' => [],
        'subTotal' => 100.0,
        'ivaRete1' => 0.0,
        'reteRenta' => 0.0,
        'montoTotalOperacion' => 100.0,
        'totalNoGravado' => 0.0,
        'totalPagar' => 113.0,
        'totalLetras' => 'CIENTO 00/100 DOLARES',
        'totalIva' => 13.0,
        'saldoFavor' => 0.0,
        'condicionOperacion' => 1,
        'pagos' => null,
        'numPagoElectronico' => null
    ],
    'extension' => [
        'nombEntrega' => null,
        'docuEntrega' => null,
        'nombRecibe' => null,
        'docuRecibe' => null,
        'observaciones' => null,
        'placaVehiculo' => null
    ],
    'apendice' => null
];

$resultado = validar_json_dte_completo($dte_valido);
if ($resultado['valid']) {
    echo "   ✅ JSON de prueba VÁLIDO\n";
    echo "      - Total Gravada: \${$resultado['sums']['total_gravada']}\n";
    echo "      - Total IVA: \${$resultado['sums']['total_iva']}\n";
    echo "      - Total Pagar: \${$resultado['sums']['total_pagar']}\n";
} else {
    echo "   ❌ JSON de prueba INVÁLIDO (esto no debería pasar)\n";
    foreach ($resultado['errors'] as $err) {
        echo "      - $err\n";
    }
}

echo "\n6️⃣  RESUMEN:\n";
if ($all_ok) {
    echo "   ✅ TODOS LOS SISTEMAS OPERACIONALES\n";
    echo "\n   El sistema está listo para:\n";
    echo "   • Procesar facturas electrónicas\n";
    echo "   • Capturar logs detallados\n";
    echo "   • Diagnosticar errores error 094\n";
} else {
    echo "   ❌ REVISAR LOS PUNTOS MARCADOS ARRIBA\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "   Para ver la guía completa: DIAGNOSTICO_ERROR_094.md\n";
echo "   Para resumen de cambios: RESUMEN_SOLUCION.md\n";
echo "═══════════════════════════════════════════════════════════════\n\n";
?>
