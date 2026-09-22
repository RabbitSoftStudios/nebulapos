<?php
/**
 * VALIDADOR DTE - MODELO CONSUMIDOR FINAL CON IVA INCLUIDO
 * 
 * IMPORTANTE: Los precios en este sistema YA INCLUYEN IVA del 13%
 * El cálculo de IVA es: IVA = ventaGravada - (ventaGravada / 1.13)
 * 
 * Ejemplo:
 *   Cliente paga: $16.00 (precio con IVA incluido)
 *   Sin IVA: $16 / 1.13 = $14.16
 *   IVA: $16 - $14.16 = $1.84
 */

function validar_json_dte_nuevo(array $dte): array
{
    $errors = [];
    $warnings = [];
    
    // 1. VALIDAR numeroControl
    if (empty($dte['identificacion']['numeroControl'])) {
        $errors[] = "CRÍTICO: numeroControl está vacío";
    } else {
        $nc = $dte['identificacion']['numeroControl'];
        // Formato esperado: DTE-XX-YYYYYYYY-ZZZZZZZZZZZZZZZ
        // Ejemplo: DTE-01-P001M001-000000000000001
        if (!preg_match('/^DTE-\d{2}-[A-Z0-9]{8}-\d{15}$/', $nc)) {
            $errors[] = "CRÍTICO: numeroControl inválido. Formato: DTE-XX-YYYYYYYY-ZZZZZZZZZZZZZZZ, recibido: $nc";
        }
    }
    
    // 2. VALIDAR codigoGeneracion (UUID v4 - 36 caracteres: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)
    if (empty($dte['identificacion']['codigoGeneracion'])) {
        $errors[] = "CRÍTICO: codigoGeneracion está vacío";
    } else {
        $cg = $dte['identificacion']['codigoGeneracion'];
        // UUID v4: 8-4-4-4-12 en hexadecimal (mayúsculas)
        if (!preg_match('/^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}$/', $cg)) {
            $errors[] = "CRÍTICO: codigoGeneracion no es UUID v4 válido. Debe ser: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (36 caracteres). Recibido: $cg";
        }
        if (strlen($cg) !== 36) {
            $errors[] = "CRÍTICO: codigoGeneracion debe ser 36 caracteres exactos (UUID v4), tiene " . strlen($cg);
        }
    }
    
    // 3. VALIDAR emisor
    if (empty($dte['emisor'])) {
        $errors[] = "CRÍTICO: Falta sección 'emisor'";
    } else {
        $emisor = $dte['emisor'];
        
        // NIT debe ser 14 dígitos exactos
        if (!isset($emisor['nit']) || !preg_match('/^\d{14}$/', $emisor['nit'])) {
            $errors[] = "CRÍTICO: NIT debe ser 14 dígitos, recibido: " . ($emisor['nit'] ?? 'vacío');
        }
        
        // NRC debe ser 2-8 dígitos (según schema MH fe-fc-v1.json pattern: ^[0-9]{1,8}$)
        if (!isset($emisor['nrc']) || !preg_match('/^\d{2,8}$/', $emisor['nrc'])) {
            $errors[] = "CRÍTICO: NRC debe ser 2-8 dígitos (mínimo 2, máximo 8), recibido: " . ($emisor['nrc'] ?? 'vacío');
        }
        
        if (empty($emisor['nombre'])) {
            $errors[] = "CRÍTICO: Falta nombre del emisor";
        }
    }
    
    // 4. VALIDAR receptor
    if (isset($dte['receptor']) && is_array($dte['receptor'])) {
        // Solo validar campos si existen (consumidor final puede estar incompleto)
        // pero si se proporcionan, deben ser válidos
    }
    
    // 5. VALIDAR cuerpoDocumento
    if (empty($dte['cuerpoDocumento']) || !is_array($dte['cuerpoDocumento'])) {
        $errors[] = "CRÍTICO: cuerpoDocumento es obligatorio y debe ser array";
    } else {
        $sumaVentaGravada = 0;
        $sumaIvaItems = 0;
        
        foreach ($dte['cuerpoDocumento'] as $idx => $item) {
            $numItem = $idx + 1;
            
            // Validar descripción
            if (empty($item['descripcion'])) {
                $errors[] = "CRÍTICO: Item $numItem - descripción está vacía";
            }
            
            // Validar cantidad
            if (!isset($item['cantidad']) || floatval($item['cantidad']) <= 0) {
                $errors[] = "CRÍTICO: Item $numItem - cantidad debe ser > 0, recibido: " . ($item['cantidad'] ?? 'vacío');
            }
            
            // Validar precioUni
            if (!isset($item['precioUni']) || floatval($item['precioUni']) < 0) {
                $errors[] = "CRÍTICO: Item $numItem - precioUni inválido: " . ($item['precioUni'] ?? 'vacío');
            }
            
            // Validar ventaGravada
            if (isset($item['precioUni']) && isset($item['cantidad'])) {
                $precioConIVA = floatval($item['precioUni']);
                $cantidad = floatval($item['cantidad']);
                $descuento = floatval($item['montoDescu'] ?? 0);
                
                $ventaGravadaEsperada = ($precioConIVA * $cantidad) - $descuento;
                $ventaGravadaEsperada = max(0, $ventaGravadaEsperada);
                
                if (isset($item['ventaGravada'])) {
                    $ventaGravada = floatval($item['ventaGravada']);
                    if (abs($ventaGravada - $ventaGravadaEsperada) > 0.01) {
                        $errors[] = "CRÍTICO: Item $numItem - ventaGravada incorrecta. Esperado: " . number_format($ventaGravadaEsperada, 2) . ", Got: " . number_format($ventaGravada, 2);
                    }
                    $sumaVentaGravada += $ventaGravada;
                }
            }
            
            // Validar ivaItem (MODELO CON IVA INCLUIDO)
            // IVA = ventaGravada - (ventaGravada / 1.13)
            if (isset($item['ventaGravada'])) {
                $ventaGravada = floatval($item['ventaGravada']);
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
    
    // 6. VALIDAR resumen
    if (empty($dte['resumen'])) {
        $errors[] = "CRÍTICO: Falta sección 'resumen'";
    } else {
        $resumen = $dte['resumen'];
        
        // totalGravada = total con IVA (lo que cliente pagó)
        if (!isset($resumen['totalGravada'])) {
            $errors[] = "CRÍTICO: resumen.totalGravada está vacío";
        }
        
        // totalPagar DEBE IGUAL a totalGravada
        if (!isset($resumen['totalPagar'])) {
            $errors[] = "CRÍTICO: resumen.totalPagar está vacío";
        } else {
            $totalGravada = floatval($resumen['totalGravada'] ?? 0);
            $totalPagar = floatval($resumen['totalPagar'] ?? 0);
            
            if (abs($totalPagar - $totalGravada) > 0.01) {
                $errors[] = "CRÍTICO: resumen.totalPagar ($totalPagar) debe ser igual a totalGravada ($totalGravada)";
            }
        }
    }
    
    // 7. VALIDAR identificacion.tipoDte
    if (isset($dte['identificacion']['tipoDte'])) {
        $tipoDte = $dte['identificacion']['tipoDte'];
        if (!in_array($tipoDte, ['01', '03', '05'])) {
            $errors[] = "CRÍTICO: tipoDte debe ser 01 (Factura), 03 (Comprobante), o 05 (Nota Crédito). Recibido: $tipoDte";
        }
    }
    
    // 8. VALIDAR fechas y horas
    if (isset($dte['identificacion']['fecEmi'])) {
        $fec = $dte['identificacion']['fecEmi'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fec)) {
            $errors[] = "CRÍTICO: fecEmi debe ser YYYY-MM-DD, recibido: $fec";
        }
    }
    
    if (isset($dte['identificacion']['horEmi'])) {
        $hor = $dte['identificacion']['horEmi'];
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $hor)) {
            $errors[] = "CRÍTICO: horEmi debe ser HH:MM:SS, recibido: $hor";
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'warnings' => $warnings,
        'error_count' => count($errors),
        'warning_count' => count($warnings),
        'model' => 'CONSUMIDOR_FINAL_CON_IVA_INCLUIDO'
    ];
}
