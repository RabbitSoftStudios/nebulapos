<?php
/**
 * VALIDADOR DTE - CONTRA SCHEMA fe-fc-v1.json
 * 
 * Valida que el JSON cumpla exactamente con el schema del MH
 * Validación PROFUNDA: no solo primer nivel, sino todos los niveles
 */

function validar_json_contra_schema($dteData) {
    $schema_file = __DIR__ . '/../signer/schemas/fe-fc-v1.json';
    
    if (!file_exists($schema_file)) {
        return [
            'valid' => false,
            'errors' => ["Schema no encontrado en: $schema_file"],
            'warnings' => []
        ];
    }
    
    $schema = json_decode(file_get_contents($schema_file), true);
    $errors = [];
    $warnings = [];
    
    // VALIDACIÓN 1: numeroControl
    if (empty($dteData['identificacion']['numeroControl'])) {
        $errors[] = "CRÍTICO: identificacion.numeroControl está vacío (se genera en servidor)";
    } else {
        $nc = $dteData['identificacion']['numeroControl'];
        // Formato: DTE-01-P001M001-000000000000001 (31 caracteres exactos)
        if (!preg_match('/^DTE-01-[A-Z0-9]{8}-[0-9]{15}$/', $nc)) {
            $errors[] = "CRÍTICO: numeroControl no coincide con formato DTE-01-YYYYYYYY-ZZZZZZZZZZZZZZZ. Recibido: $nc";
        }
        if (strlen($nc) !== 31) {
            $errors[] = "CRÍTICO: numeroControl debe ser 31 caracteres exactos (incluyendo guiones), tiene " . strlen($nc);
        }
    }
    
    // VALIDACIÓN 2: codigoGeneracion (UUID v4 - 36 caracteres)
    if (empty($dteData['identificacion']['codigoGeneracion'])) {
        $errors[] = "CRÍTICO: identificacion.codigoGeneracion está vacío";
    } else {
        $cg = $dteData['identificacion']['codigoGeneracion'];
        // Formato UUID v4: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (8-4-4-4-12)
        if (!preg_match('/^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}$/', $cg)) {
            $errors[] = "CRÍTICO: codigoGeneracion no es UUID v4 válido. Debe ser: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (36 caracteres). Recibido: $cg";
        }
        if (strlen($cg) !== 36) {
            $errors[] = "CRÍTICO: codigoGeneracion debe ser 36 caracteres exactos (UUID v4), tiene " . strlen($cg);
        }
    }
    
    // VALIDACIÓN 3: ambiente (debe ser "00" o "01")
    if (!isset($dteData['identificacion']['ambiente']) || 
        !in_array($dteData['identificacion']['ambiente'], ['00', '01'])) {
        $errors[] = "CRÍTICO: identificacion.ambiente debe ser '00' o '01'";
    }
    
    // VALIDACIÓN 4: tipoDte (debe ser "01" para Factura)
    if ($dteData['identificacion']['tipoDte'] !== '01') {
        $errors[] = "CRÍTICO: identificacion.tipoDte debe ser '01', recibido: " . $dteData['identificacion']['tipoDte'];
    }
    
    // VALIDACIÓN 5: Emisor
    if (empty($dteData['emisor'])) {
        $errors[] = "CRÍTICO: Falta sección 'emisor'";
    } else {
        $emisor = $dteData['emisor'];
        
        // NIT exactamente 14 dígitos
        if (!isset($emisor['nit']) || !preg_match('/^\d{14}$/', $emisor['nit'])) {
            $errors[] = "CRÍTICO: emisor.nit debe ser 14 dígitos exactos, recibido: " . ($emisor['nit'] ?? 'vacío');
        }
        
        // NRC exactamente 4 dígitos
        if (!isset($emisor['nrc']) || !preg_match('/^\d{4}$/', $emisor['nrc'])) {
            $errors[] = "CRÍTICO: emisor.nrc debe ser 4 dígitos exactos, recibido: " . ($emisor['nrc'] ?? 'vacío');
        }
        
        // Nombre obligatorio
        if (empty($emisor['nombre'])) {
            $errors[] = "CRÍTICO: emisor.nombre es obligatorio";
        }
        
        // Código de actividad 5 dígitos
        if (!preg_match('/^\d{5}$/', $emisor['codActividad'] ?? '')) {
            $errors[] = "CRÍTICO: emisor.codActividad debe ser 5 dígitos";
        }
        
        // Establecimiento formato: P001
        if (!preg_match('/^[A-Z]\d{3}$/', $emisor['codEstableMH'] ?? '')) {
            $errors[] = "CRÍTICO: emisor.codEstableMH formato inválido (debe ser P001, M001, etc)";
        }
        
        // Punto de venta formato: M001
        if (!preg_match('/^[A-Z]\d{3}$/', $emisor['codPuntoVentaMH'] ?? '')) {
            $errors[] = "CRÍTICO: emisor.codPuntoVentaMH formato inválido";
        }
    }
    
    // VALIDACIÓN 6: Receptor
    if (isset($dteData['receptor']) && is_array($dteData['receptor'])) {
        $receptor = $dteData['receptor'];
        
        // Si tipoDocumento está presente, debe ser válido
        if (isset($receptor['tipoDocumento'])) {
            if (!in_array($receptor['tipoDocumento'], ['01', '02', '03', '04', '37'])) {
                $errors[] = "CRÍTICO: receptor.tipoDocumento inválido: " . $receptor['tipoDocumento'];
            }
        }
        
        // NRC receptor debe ser 4 dígitos si existe
        if (isset($receptor['nrc']) && !preg_match('/^\d{4}$/', (string)$receptor['nrc'])) {
            $errors[] = "CRÍTICO: receptor.nrc debe ser 4 dígitos, recibido: " . $receptor['nrc'];
        }
    }
    
    // VALIDACIÓN 7: cuerpoDocumento (items)
    if (empty($dteData['cuerpoDocumento']) || !is_array($dteData['cuerpoDocumento'])) {
        $errors[] = "CRÍTICO: cuerpoDocumento es obligatorio y debe ser array";
    } else {
        $sumaVentaGravada = 0;
        $sumaIvaItems = 0;
        
        foreach ($dteData['cuerpoDocumento'] as $idx => $item) {
            $numItem = $idx + 1;
            
            // Descripción obligatoria
            if (empty($item['descripcion'])) {
                $errors[] = "CRÍTICO: Item $numItem - descripción está vacía";
            }
            
            // Cantidad debe ser positiva
            if (!isset($item['cantidad']) || floatval($item['cantidad']) <= 0) {
                $errors[] = "CRÍTICO: Item $numItem - cantidad debe ser > 0";
            }
            
            // Precio unitario debe ser válido
            if (!isset($item['precioUni']) || floatval($item['precioUni']) < 0) {
                $errors[] = "CRÍTICO: Item $numItem - precioUni inválido";
            }
            
            // Validar ventaGravada
            if (isset($item['precioUni']) && isset($item['cantidad'])) {
                $precioUni = floatval($item['precioUni']);
                $cantidad = floatval($item['cantidad']);
                $descuento = floatval($item['montoDescu'] ?? 0);
                
                $ventaGravadaEsperada = ($precioUni * $cantidad) - $descuento;
                $ventaGravadaEsperada = max(0, $ventaGravadaEsperada);
                
                if (isset($item['ventaGravada'])) {
                    $ventaGravada = floatval($item['ventaGravada']);
                    if (abs($ventaGravada - $ventaGravadaEsperada) > 0.01) {
                        $errors[] = "CRÍTICO: Item $numItem - ventaGravada incorrecta. Esperado: " . number_format($ventaGravadaEsperada, 2) . ", Got: " . number_format($ventaGravada, 2);
                    }
                    $sumaVentaGravada += $ventaGravada;
                }
            }
            
            // Validar ivaItem (para precios CON IVA incluido)
            if (isset($item['ventaGravada'])) {
                $ventaGravada = floatval($item['ventaGravada']);
                // IVA = ventaGravada - (ventaGravada / 1.13)
                $ivaEsperado = $ventaGravada - ($ventaGravada / 1.13);
                $ivaEsperado = floatval(number_format($ivaEsperado, 2, '.', ''));
                
                if (isset($item['ivaItem'])) {
                    $ivaItem = floatval($item['ivaItem']);
                    if (abs($ivaItem - $ivaEsperado) > 0.05) {
                        $errors[] = "CRÍTICO: Item $numItem - ivaItem incorrecta. Esperado: " . number_format($ivaEsperado, 2) . ", Got: " . number_format($ivaItem, 2);
                    }
                    $sumaIvaItems += $ivaItem;
                }
            }
        }
    }
    
    // VALIDACIÓN 8: Resumen (nivel profundo)
    if (empty($dteData['resumen'])) {
        $errors[] = "CRÍTICO: Falta sección 'resumen'";
    } else {
        $resumen = $dteData['resumen'];
        
        // Totales obligatorios
        if (!isset($resumen['totalGravada'])) {
            $errors[] = "CRÍTICO: resumen.totalGravada está vacío";
        }
        
        if (!isset($resumen['totalPagar'])) {
            $errors[] = "CRÍTICO: resumen.totalPagar está vacío";
        } else {
            $totalGravada = floatval($resumen['totalGravada'] ?? 0);
            $totalPagar = floatval($resumen['totalPagar'] ?? 0);
            
            // En modelo con IVA incluido: totalPagar = totalGravada
            if (abs($totalPagar - $totalGravada) > 0.01) {
                $errors[] = "CRÍTICO: resumen.totalPagar ($totalPagar) debe ser igual a totalGravada ($totalGravada)";
            }
        }
        
        // Validar tributos (debe incluir IVA código 20)
        if (!isset($resumen['tributos']) || !is_array($resumen['tributos'])) {
            $errors[] = "CRÍTICO: resumen.tributos debe ser array";
        } else {
            $tieneIVA = false;
            foreach ($resumen['tributos'] as $trib) {
                if (isset($trib['codigo']) && $trib['codigo'] === '20') {
                    $tieneIVA = true;
                    // Validar que tenga valor
                    if (!isset($trib['valor']) || floatval($trib['valor']) < 0) {
                        $errors[] = "CRÍTICO: Tributo IVA (código 20) debe tener valor > 0";
                    }
                }
            }
            // Si no hay tributos pero hay totalIva, error
            if (!$tieneIVA && isset($resumen['totalIva']) && floatval($resumen['totalIva']) > 0) {
                $errors[] = "CRÍTICO: Se esperaba tributo IVA (código 20) en resumen.tributos";
            }
        }
        
        // Validar condicionOperación (1=Contado, 2=Crédito, 3=Combinado)
        if (!isset($resumen['condicionOperacion']) || !in_array($resumen['condicionOperacion'], [1, 2, 3])) {
            $errors[] = "CRÍTICO: resumen.condicionOperacion debe ser 1, 2 o 3";
        }
        
        // Validar pagos (debe existir array de pagos)
        if (!isset($resumen['pagos']) || !is_array($resumen['pagos'])) {
            $errors[] = "CRÍTICO: resumen.pagos debe ser array de pagos";
        } else if (empty($resumen['pagos'])) {
            $errors[] = "CRÍTICO: resumen.pagos no puede estar vacío";
        } else {
            // Validar cada pago
            foreach ($resumen['pagos'] as $pago) {
                if (!isset($pago['codigo']) || !in_array($pago['codigo'], ['01', '02', '03', '04', '05'])) {
                    $errors[] = "CRÍTICO: Pago debe tener código válido (01=Efectivo, 02=Cheque, 03=Tarjeta, 04=Transferencia, 05=Otro)";
                }
                if (!isset($pago['montoPago']) || floatval($pago['montoPago']) <= 0) {
                    $errors[] = "CRÍTICO: Pago debe tener montoPago > 0";
                }
            }
        }
    }
    
    // VALIDACIÓN 9: Fechas y horas
    if (isset($dteData['identificacion']['fecEmi'])) {
        $fec = $dteData['identificacion']['fecEmi'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fec)) {
            $errors[] = "CRÍTICO: identificacion.fecEmi debe ser YYYY-MM-DD, recibido: $fec";
        }
    }
    
    if (isset($dteData['identificacion']['horEmi'])) {
        $hor = $dteData['identificacion']['horEmi'];
        if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $hor)) {
            $errors[] = "CRÍTICO: identificacion.horEmi debe ser HH:MM:SS (formato 24h), recibido: $hor";
        }
    }
    
    // VALIDACIÓN 10: Versión
    if ($dteData['identificacion']['version'] !== 1) {
        $errors[] = "CRÍTICO: identificacion.version debe ser 1";
    }
    
    // VALIDACIÓN 11: tipoModelo
    if (!in_array($dteData['identificacion']['tipoModelo'], [1, 2])) {
        $errors[] = "CRÍTICO: identificacion.tipoModelo debe ser 1 o 2";
    }
    
    // VALIDACIÓN 12: tipoOperacion
    if (!in_array($dteData['identificacion']['tipoOperacion'], [1, 2])) {
        $errors[] = "CRÍTICO: identificacion.tipoOperacion debe ser 1 o 2";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'warnings' => $warnings,
        'error_count' => count($errors),
        'warning_count' => count($warnings),
        'model' => 'FACTURA_ELECTRONICA_FE-FC-V1',
        'validated_at' => date('Y-m-d H:i:s')
    ];
}
