<?php
/**
 * VALIDADOR DE JSON DTE ANTES DE FIRMA
 * Verifica que el JSON cumpla con TODAS las validaciones antes de enviarlo a firma/MH
 * 
 * Uso: Incluir en process_sale_complete.php antes de firmar
 */

function validar_json_dte_completo(array $dte): array
{
    $errors = [];
    $warnings = [];
    
    /* ============================================================
     * 1. IDENTIFICACION
     * ============================================================ */
    if (empty($dte['identificacion'])) {
        $errors[] = "CRÍTICO: Falta sección 'identificacion'";
        return ['valid' => false, 'errors' => $errors, 'warnings' => $warnings];
    }
    
    $id = $dte['identificacion'];
    
    // numeroControl
    if (empty($id['numeroControl'])) {
        $errors[] = "CRÍTICO: numeroControl está vacío";
    } elseif (!preg_match('/^DTE-\d{2}-[A-Z0-9]{7}-\d{15}$/', $id['numeroControl'])) {
        $errors[] = "CRÍTICO: numeroControl no coincide con formato esperado. Got: " . $id['numeroControl'];
    }
    
    // codigoGeneracion
    if (empty($id['codigoGeneracion'])) {
        $errors[] = "CRÍTICO: codigoGeneracion está vacío";
    } elseif (!preg_match('/^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}$/', $id['codigoGeneracion'])) {
        $warnings[] = "codigoGeneracion no parece UUID válido: " . $id['codigoGeneracion'];
    }
    
    // Fechas y horas
    if (empty($id['fecEmi'])) {
        $errors[] = "CRÍTICO: fecEmi está vacía";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $id['fecEmi'])) {
        $errors[] = "CRÍTICO: fecEmi debe ser YYYY-MM-DD. Got: " . $id['fecEmi'];
    }
    
    if (empty($id['horEmi'])) {
        $errors[] = "CRÍTICO: horEmi está vacía";
    } elseif (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $id['horEmi'])) {
        $errors[] = "CRÍTICO: horEmi debe ser HH:mm:ss. Got: " . $id['horEmi'];
    }
    
    // Campos de control
    if (($id['version'] ?? 0) != 1) {
        $errors[] = "CRÍTICO: version debe ser 1. Got: " . ($id['version'] ?? 'null');
    }
    
    if (($id['ambiente'] ?? '') == '') {
        $errors[] = "CRÍTICO: ambiente está vacío. Debe ser '00' o '01'";
    } elseif (!in_array($id['ambiente'], ['00', '01'])) {
        $errors[] = "CRÍTICO: ambiente debe ser '00' (test) o '01' (prod). Got: " . $id['ambiente'];
    }
    
    if (($id['tipoDte'] ?? '') !== '01') {
        $errors[] = "CRÍTICO: tipoDte debe ser '01'. Got: " . ($id['tipoDte'] ?? 'null');
    }
    
    if (($id['tipoModelo'] ?? 0) != 1) {
        $errors[] = "CRÍTICO: tipoModelo debe ser 1. Got: " . ($id['tipoModelo'] ?? 'null');
    }
    
    if (($id['tipoOperacion'] ?? 0) != 1) {
        $errors[] = "CRÍTICO: tipoOperacion debe ser 1. Got: " . ($id['tipoOperacion'] ?? 'null');
    }
    
    /* ============================================================
     * 2. EMISOR
     * ============================================================ */
    if (empty($dte['emisor'])) {
        $errors[] = "CRÍTICO: Falta sección 'emisor'";
        return ['valid' => false, 'errors' => $errors, 'warnings' => $warnings];
    }
    
    $emisor = $dte['emisor'];
    
    if (empty($emisor['nit'])) {
        $errors[] = "CRÍTICO: emisor.nit está vacío";
    } elseif (strlen($emisor['nit']) !== 14) {
        $errors[] = "CRÍTICO: emisor.nit debe tener exactamente 14 dígitos. Got: {$emisor['nit']} ({" . strlen($emisor['nit']) . "} chars)";
    }
    
    if (empty($emisor['nombre'])) {
        $errors[] = "CRÍTICO: emisor.nombre está vacío";
    }
    
    if (empty($emisor['codActividad'])) {
        $errors[] = "CRÍTICO: emisor.codActividad está vacío";
    } elseif (strlen($emisor['codActividad']) !== 5) {
        $errors[] = "CRÍTICO: emisor.codActividad debe ser 5 dígitos. Got: " . $emisor['codActividad'];
    }
    
    if (empty($emisor['nrc'])) {
        $warnings[] = "emisor.nrc está vacío (generalmente requerido)";
    }
    
    // Establecimiento
    if (($emisor['tipoEstablecimiento'] ?? '') == '') {
        $errors[] = "CRÍTICO: emisor.tipoEstablecimiento está vacío";
    }
    
    if (($emisor['codEstableMH'] ?? '') == '' || ($emisor['codEstable'] ?? '') == '') {
        $errors[] = "CRÍTICO: Faltan codEstableMH o codEstable (ej: P001)";
    }
    
    if (($emisor['codPuntoVentaMH'] ?? '') == '' || ($emisor['codPuntoVenta'] ?? '') == '') {
        $errors[] = "CRÍTICO: Faltan codPuntoVentaMH o codPuntoVenta (ej: M001)";
    }
    
    /* ============================================================
     * 3. RECEPTOR
     * ============================================================ */
    if (empty($dte['receptor'])) {
        $errors[] = "CRÍTICO: Falta sección 'receptor'";
        return ['valid' => false, 'errors' => $errors, 'warnings' => $warnings];
    }
    
    $receptor = $dte['receptor'];
    
    if (empty($receptor['nombre'])) {
        $errors[] = "CRÍTICO: receptor.nombre está vacío (mínimo, requerido)";
    }
    
    if (empty($receptor['codActividad'])) {
        $errors[] = "CRÍTICO: receptor.codActividad está vacío";
    } elseif (strlen($receptor['codActividad']) !== 5) {
        $errors[] = "CRÍTICO: receptor.codActividad debe ser 5 dígitos. Got: " . $receptor['codActividad'];
    }
    
    // Dirección
    $dir = $receptor['direccion'] ?? [];
    if (empty($dir['departamento']) || strlen($dir['departamento']) !== 2) {
        $errors[] = "CRÍTICO: receptor.direccion.departamento debe ser 2 dígitos (01-14)";
    }
    if (empty($dir['municipio']) || strlen($dir['municipio']) !== 2) {
        $errors[] = "CRÍTICO: receptor.direccion.municipio debe ser 2 dígitos (01-23)";
    }
    
    /* ============================================================
     * 4. CUERPO DOCUMENTO (Items)
     * ============================================================ */
    $cuerpo = $dte['cuerpoDocumento'] ?? [];
    
    if (empty($cuerpo) || !is_array($cuerpo)) {
        $errors[] = "CRÍTICO: cuerpoDocumento está vacío o no es array";
        return ['valid' => false, 'errors' => $errors, 'warnings' => $warnings];
    }
    
    $suma_venta_gravada = 0;
    $suma_iva = 0;
    
    foreach ($cuerpo as $idx => $item) {
        $num = $idx + 1;
        
        // Tipo de item
        if (($item['tipoItem'] ?? 0) == 0) {
            $errors[] = "CRÍTICO: Item $num - tipoItem está vacío o es 0";
        }
        
        // Descripción
        if (empty($item['descripcion'])) {
            $errors[] = "CRÍTICO: Item $num - descripcion está vacía";
        }
        
        // Cantidad
        if (($item['cantidad'] ?? 0) <= 0) {
            $errors[] = "CRÍTICO: Item $num - cantidad debe ser > 0. Got: " . ($item['cantidad'] ?? 'null');
        }
        
        // Unidad de medida
        if (($item['uniMedida'] ?? 0) != 59) {
            $warnings[] = "Item $num - uniMedida = " . ($item['uniMedida'] ?? 'null') . " (típicamente 59 para unitarios)";
        }
        
        // Precio unitario
        if (($item['precioUni'] ?? 0) <= 0) {
            $errors[] = "CRÍTICO: Item $num - precioUni debe ser > 0. Got: " . ($item['precioUni'] ?? 'null');
        }
        
        // VALIDACIÓN MATEMÁTICA CRÍTICA
        $esperado_venta_gravada = ($item['cantidad'] ?? 0) * ($item['precioUni'] ?? 0) - ($item['montoDescu'] ?? 0);
        $venta_gravada = $item['ventaGravada'] ?? 0;
        
        if (abs($esperado_venta_gravada - $venta_gravada) > 0.01) {
            $errors[] = "CRÍTICO: Item $num - ventaGravada incorrecta. Esperado: $esperado_venta_gravada, Got: $venta_gravada";
        }
        
        // IVA (13%)
        $iva_esperado = $venta_gravada * 0.13;
        $iva_item = $item['ivaItem'] ?? 0;
        
        if (abs($iva_esperado - $iva_item) > 0.01) {
            $errors[] = "CRÍTICO: Item $num - ivaItem incorrecta. Esperado: " . round($iva_esperado, 2) . ", Got: $iva_item";
        }
        
        $suma_venta_gravada += $venta_gravada;
        $suma_iva += $iva_item;
    }
    
    /* ============================================================
     * 5. RESUMEN (Validación de sumas)
     * ============================================================ */
    if (empty($dte['resumen'])) {
        $errors[] = "CRÍTICO: Falta sección 'resumen'";
        return ['valid' => false, 'errors' => $errors, 'warnings' => $warnings];
    }
    
    $resumen = $dte['resumen'];
    
    $tolerancia = 0.05; // 5 centavos de tolerancia
    
    // totalGravada
    $total_gravada = $resumen['totalGravada'] ?? 0;
    if (abs($suma_venta_gravada - $total_gravada) > $tolerancia) {
        $errors[] = "CRÍTICO: resumen.totalGravada no coincide. Esperado: " . round($suma_venta_gravada, 2) . ", Got: $total_gravada";
    }
    
    // totalIva
    $total_iva = $resumen['totalIva'] ?? 0;
    if (abs($suma_iva - $total_iva) > $tolerancia) {
        $errors[] = "CRÍTICO: resumen.totalIva no coincide. Esperado: " . round($suma_iva, 2) . ", Got: $total_iva";
    }
    
    // totalPagar (debe incluir IVA)
    $total_pagar = $resumen['totalPagar'] ?? 0;
    $total_esperado = $suma_venta_gravada + $suma_iva;
    if (abs($total_esperado - $total_pagar) > $tolerancia) {
        $errors[] = "CRÍTICO: resumen.totalPagar incorrecto. Esperado: " . round($total_esperado, 2) . ", Got: $total_pagar";
    }
    
    // montoTotalOperacion
    $monto_total_op = $resumen['montoTotalOperacion'] ?? 0;
    if (abs($total_gravada - $monto_total_op) > $tolerancia) {
        $warnings[] = "resumen.montoTotalOperacion debería ser totalGravada: " . round($total_gravada, 2) . ", Got: $monto_total_op";
    }
    
    // condicionOperacion
    if (($resumen['condicionOperacion'] ?? 0) == 0) {
        $errors[] = "CRÍTICO: resumen.condicionOperacion está vacío. Debe ser 1 (contado) o 2 (plazo)";
    }
    
    /* ============================================================
     * 6. RESULTADO
     * ============================================================ */
    $is_valid = count($errors) === 0;
    
    return [
        'valid' => $is_valid,
        'errors' => $errors,
        'warnings' => $warnings,
        'sums' => [
            'total_gravada' => $suma_venta_gravada,
            'total_iva' => $suma_iva,
            'total_pagar' => $suma_venta_gravada + $suma_iva
        ]
    ];
}

/**
 * Función para imprimir el reporte de validación
 */
function print_validation_report(array $result): void
{
    echo "\n=== REPORTE DE VALIDACIÓN DTE ===\n";
    
    if ($result['valid']) {
        echo "✅ JSON DTE ES VÁLIDO\n";
        echo "\nSumas verificadas:\n";
        echo "  - Total Gravada: $" . number_format($result['sums']['total_gravada'], 2) . "\n";
        echo "  - Total IVA (13%): $" . number_format($result['sums']['total_iva'], 2) . "\n";
        echo "  - Total a Pagar: $" . number_format($result['sums']['total_pagar'], 2) . "\n";
    } else {
        echo "❌ JSON DTE TIENE ERRORES:\n";
        foreach ($result['errors'] as $err) {
            echo "  ❌ $err\n";
        }
    }
    
    if (!empty($result['warnings'])) {
        echo "\n⚠️ ADVERTENCIAS:\n";
        foreach ($result['warnings'] as $warn) {
            echo "  ⚠️ $warn\n";
        }
    }
    
    echo "\n";
}
?>
