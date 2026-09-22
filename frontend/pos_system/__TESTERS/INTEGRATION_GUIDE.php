<?php
/**
 * INTEGRATION GUIDE - CORRECCIONES IMPLEMENTADAS
 * 
 * Este archivo explica cómo todas las partes trabajan juntas para resolver Error 094
 */

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "FLUJO COMPLETO DE GENERACIÓN Y VALIDACIÓN DE DTE\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

// ═══════════════════════════════════════════════════════════════════════════════
// PASO 1: CLIENTE COMPLETA VENTA EN POS (pos_sale.php)
// ═══════════════════════════════════════════════════════════════════════════════
echo "PASO 1: Cliente completa venta en el POS\n";
echo "─────────────────────────────────────────\n\n";

echo "Archivo: /views/pos_sale.php\n";
echo "Función: generarCodigoGeneracion()\n\n";

echo "Qué ocurre:\n";
echo "1. En el SERVIDOR (PHP): Se genera UUID v4 para codigoGeneracion\n";
echo "2. En el CLIENTE (JavaScript): El valor UUID se envía al navegador\n";
echo "3. Cuando se hace click en 'Finalizar Venta':\n";
echo "   - prepareDTEJson() crea el JSON con los datos\n";
echo "   - numeroControl = null (se genera en servidor después)\n";
echo "   - codigoGeneracion = UUID v4 (ejemplo: 550E8400-E29B-41D4-A716-446655440000)\n\n";

echo "Código:\n";
echo "┌─────────────────────────────────────────────────────────────────────────┐\n";
echo "│ // En PHP (pos_sale.php línea 521):                                    │\n";
echo "│ function generarCodigoGeneracion() {                                  │\n";
echo "│     return strtoupper(sprintf(                                        │\n";
echo "│         '%s-%s-%s-%s-%s',                                            │\n";
echo "│         bin2hex(random_bytes(4)),   // 8 hex chars                    │\n";
echo "│         bin2hex(random_bytes(2)),   // 4 hex chars                    │\n";
echo "│         bin2hex(random_bytes(2)),   // 4 hex chars                    │\n";
echo "│         bin2hex(random_bytes(2)),   // 4 hex chars                    │\n";
echo "│         bin2hex(random_bytes(6))    // 12 hex chars                   │\n";
echo "│     ));                                                              │\n";
echo "│     // Resultado: 36 caracteres UUID v4                              │\n";
echo "│     // Ej: 550E8400-E29B-41D4-A716-446655440000                      │\n";
echo "│ }                                                                     │\n";
echo "│                                                                       │\n";
echo "│ // Luego en PHP (línea 543):                                         │\n";
echo "│ \$codigoGeneracion = generarCodigoGeneracion();                      │\n";
echo "│                                                                       │\n";
echo "│ // En HTML/JavaScript (línea 547):                                   │\n";
echo "│ var mhControl = {                                                   │\n";
echo "│     codigoGeneracion: '<?= \$codigoGeneracion ?>',  // 550E8400-...  │\n";
echo "│     fechaEmision: today,                                            │\n";
echo "│     horaEmision: time                                               │\n";
echo "│ };                                                                   │\n";
echo "└─────────────────────────────────────────────────────────────────────────┘\n\n";

// ═══════════════════════════════════════════════════════════════════════════════
// PASO 2: PREPARAR JSON DTE (pos_sale.php - prepareDTEJson)
// ═══════════════════════════════════════════════════════════════════════════════
echo "PASO 2: Preparar JSON DTE (Cliente hace click en 'Finalizar Venta')\n";
echo "───────────────────────────────────────────────────────────────────\n\n";

echo "Archivo: /views/pos_sale.php\n";
echo "Función: prepareDTEJson()\n\n";

echo "Qué ocurre:\n";
echo "1. Se ejecuta prepareDTEJson() que retorna objeto JSON con:\n";
echo "   ✓ identificacion.codigoGeneracion = UUID v4 (36 chars, ej: 550E8400-...)\n";
echo "   ✓ identificacion.numeroControl = null (se genera en servidor)\n";
echo "   ✓ emisor.nrc = '0934' (4 dígitos)\n";
echo "   ✓ cuerpoDocumento[] = items con precios CON IVA incluido\n";
echo "   ✓ resumen con totales correctos\n";
echo "2. El JSON se envía al servidor en process_sale_complete.php\n\n";

echo "JSON generado:\n";
echo "┌─────────────────────────────────────────────────────────────────────────┐\n";
echo "│ {                                                                     │\n";
echo "│   \"identificacion\": {                                               │\n";
echo "│     \"version\": 1,                                                  │\n";
echo "│     \"ambiente\": \"00\",                                            │\n";
echo "│     \"tipoDte\": \"01\",                                             │\n";
echo "│     \"numeroControl\": null,              // ← null por ahora          │\n";
echo "│     \"codigoGeneracion\": \"550E8400-E29B-41D4-A716-446655440000\",  │\n";
echo "│                                           // ← 36 chars UUID v4        │\n";
echo "│     \"fecEmi\": \"2024-01-15\",                                      │\n";
echo "│     \"horEmi\": \"14:30:45\"                                         │\n";
echo "│   },                                                                 │\n";
echo "│   \"emisor\": {                                                       │\n";
echo "│     \"nit\": \"06150911851010\",                                    │\n";
echo "│     \"nrc\": \"0934\",                     // ← 4 dígitos             │\n";
echo "│     \"nombre\": \"Rodriguez Machuca Jose Alexander\"                │\n";
echo "│   },                                                                 │\n";
echo "│   \"cuerpoDocumento\": [                                             │\n";
echo "│     {                                                               │\n";
echo "│       \"descripcion\": \"Producto XYZ\",                            │\n";
echo "│       \"precioUni\": 100.00,              // ← Precio CON IVA       │\n";
echo "│       \"cantidad\": 1,                                              │\n";
echo "│       \"ventaGravada\": 88.50,            // ← Base sin IVA          │\n";
echo "│       \"ivaItem\": 11.50                  // ← IVA extraído          │\n";
echo "│     }                                                               │\n";
echo "│   ],                                                                 │\n";
echo "│   \"resumen\": {                                                      │\n";
echo "│     \"totalGravada\": 88.50,              // ← Total base            │\n";
echo "│     \"totalIva\": 11.50,                  // ← Total IVA             │\n";
echo "│     \"totalPagar\": 88.50,                // ← En modelo con IVA     │\n";
echo "│     \"condicionOperacion\": 1,                                       │\n";
echo "│     \"pagos\": [{                                                    │\n";
echo "│       \"codigo\": \"03\",                 // Tarjeta                 │\n";
echo "│       \"montoPago\": 100.00               // Monto con IVA           │\n";
echo "│     }]                                                              │\n";
echo "│   }                                                                 │\n";
echo "│ }                                                                    │\n";
echo "└─────────────────────────────────────────────────────────────────────────┘\n\n";

// ═══════════════════════════════════════════════════════════════════════════════
// PASO 3: ENVIAR JSON AL SERVIDOR (process_sale_complete.php)
// ═══════════════════════════════════════════════════════════════════════════════
echo "PASO 3: Enviar JSON al servidor\n";
echo "──────────────────────────────\n\n";

echo "Archivo: /views/ajax/process_sale_complete.php\n\n";

echo "Qué ocurre (NUEVO ORDEN CORRECTO):\n";
echo "1. Recibir JSON del cliente\n";
echo "2. ⬜ PRIMERO: Generar numeroControl (con lock SQL)\n";
echo "3. ⬜ SEGUNDO: Asignar numeroControl al JSON\n";
echo "4. ⬜ TERCERO: VALIDAR el JSON\n";
echo "5. Si validación OK: Guardar y enviar al MH\n";
echo "6. Si validación FALLA: Rollback y error\n\n";

echo "ANTES (INCORRECTO):\n";
echo "1. Recibir JSON\n";
echo "2. VALIDAR (numeroControl = null) ❌ FALLA\n";
echo "3. Generar numeroControl (nunca llega aquí)\n\n";

echo "Código Corregido:\n";
echo "┌─────────────────────────────────────────────────────────────────────────┐\n";
echo "│ // Línea 48: PRIMERO - Generar numeroControl                           │\n";
echo "│ \$pdo->beginTransaction();  // Lock para evitar duplicados              │\n";
echo "│ \$lockStmt = \$pdo->prepare(\"SELECT ... FOR UPDATE\");                │\n";
echo "│ \$lockStmt->execute([':tipo' => \$tipoDte]);                          │\n";
echo "│                                                                       │\n";
echo "│ // Obtener siguiente secuencial                                       │\n";
echo "│ \$stmt = \$pdo->prepare(\"SELECT MAX(...) + 1 AS next_num ...\");      │\n";
echo "│ \$row = \$stmt->fetch();                                              │\n";
echo "│ \$secuencial = str_pad(\$row['next_num'], 15, '0', STR_PAD_LEFT);    │\n";
echo "│ \$numeroControl = \"DTE-01-P001M001-{\$secuencial}\";                 │\n";
echo "│ // Resultado: DTE-01-P001M001-000000000000001                        │\n";
echo "│                                                                       │\n";
echo "│ // SEGUNDO - Asignar al JSON ANTES de validar                        │\n";
echo "│ \$dteData['identificacion']['numeroControl'] = \$numeroControl;     │\n";
echo "│                                                                       │\n";
echo "│ // TERCERO - AHORA SÍ validar                                        │\n";
echo "│ \$validacion = validar_json_dte_nuevo(\$dteData);                   │\n";
echo "│ if (!\$validacion['valid']) {                                        │\n";
echo "│     \$pdo->rollBack();  // Rollback si falla                         │\n";
echo "│     echo json_encode(['success' => false, ...]);                    │\n";
echo "│     exit;                                                           │\n";
echo "│ }                                                                    │\n";
echo "└─────────────────────────────────────────────────────────────────────────┘\n\n";

// ═══════════════════════════════════════════════════════════════════════════════
// PASO 4: VALIDAR JSON (dte_validator_new.php)
// ═══════════════════════════════════════════════════════════════════════════════
echo "PASO 4: Validar JSON contra especificaciones del MH\n";
echo "────────────────────────────────────────────────────\n\n";

echo "Archivo: /includes/signer/utils/dte_validator_new.php\n\n";

echo "Validaciones que se hacen:\n";
echo "✓ numeroControl: patrón ^DTE-01-[A-Z0-9]{8}-[0-9]{15}$ (31 chars)\n";
echo "✓ codigoGeneracion: patrón UUID v4 (36 chars, 8-4-4-4-12)\n";
echo "✓ nrc: exactamente 4 dígitos\n";
echo "✓ ambiente: '00' o '01'\n";
echo "✓ tipoDte: '01'\n";
echo "✓ Emisor: NIT 14 dígitos, NRC 4 dígitos\n";
echo "✓ Items: cantidad > 0, precio válido, IVA correcto\n";
echo "✓ Resumen: totales consistentes, pagos presentes\n\n";

echo "Validaciones CORREGIDAS:\n";
echo "┌─────────────────────────────────────────────────────────────────────────┐\n";
echo "│ // ANTES (INCORRECTO):                                               │\n";
echo "│ if (!preg_match('/^[A-Z0-9]{8}\$/', \$cg)) {  // 8 chars solo       │\n";
echo "│     \$errors[] = \"codigoGeneracion debe ser 8 caracteres\";          │\n";
echo "│ }                                                                    │\n";
echo "│                                                                       │\n";
echo "│ // DESPUÉS (CORRECTO):                                              │\n";
echo "│ if (!preg_match('/^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-...\$/', \$cg)) {│\n";
echo "│     // UUID v4: 36 caracteres                                        │\n";
echo "│     \$errors[] = \"codigoGeneracion debe ser UUID v4 (36 chars)\";   │\n";
echo "│ }                                                                    │\n";
echo "│ if (strlen(\$cg) !== 36) {                                           │\n";
echo "│     \$errors[] = \"codigoGeneracion tiene \" . strlen(\$cg) . \" chars, debe ser 36\"; │\n";
echo "│ }                                                                    │\n";
echo "└─────────────────────────────────────────────────────────────────────────┘\n\n";

// ═══════════════════════════════════════════════════════════════════════════════
// PASO 5: GUARDAR Y ENVIAR AL MH
// ═══════════════════════════════════════════════════════════════════════════════
echo "PASO 5: Si validación OK → Guardar JSON y enviar al MH\n";
echo "──────────────────────────────────────────────────────\n\n";

echo "Qué ocurre:\n";
echo "1. Guardar JSON en /storage/sigs/dte_<codigoGeneracion>.json\n";
echo "2. Guardar en base de datos\n";
echo "3. Enviar a MH API: https://apitest.dtes.mh.gob.sv/fesv/recepciondte\n";
echo "4. Recibir respuesta del MH\n";
echo "5. Si ERROR 094: Revisar JSON con validadores\n";
echo "6. Si SUCCESS: Generar PDF, QR, ticket\n\n";

// ═══════════════════════════════════════════════════════════════════════════════
// EJEMPLO DE JSON COMPLETO Y VÁLIDO
// ═══════════════════════════════════════════════════════════════════════════════
echo "EJEMPLO DE JSON VÁLIDO Y COMPLETO\n";
echo "──────────────────────────────────\n\n";

$json_ejemplo = [
    "identificacion" => [
        "version" => 1,
        "ambiente" => "00",  // test
        "tipoDte" => "01",   // factura
        "numeroControl" => "DTE-01-P001M001-000000000000001",  // ✓ 31 chars
        "codigoGeneracion" => "550E8400-E29B-41D4-A716-446655440000",  // ✓ 36 chars UUID
        "tipoModelo" => 1,
        "tipoOperacion" => 1,
        "fecEmi" => "2024-01-15",
        "horEmi" => "14:30:45"
    ],
    "emisor" => [
        "nit" => "06150911851010",  // 14 dígitos
        "nrc" => "0934",             // ✓ 4 dígitos
        "nombre" => "Rodriguez Machuca Jose Alexander",
        "codActividad" => "46510",
        "codEstableMH" => "P001",
        "codPuntoVentaMH" => "M001",
        "telefono" => "21212121",
        "correo" => "test@example.com"
    ],
    "receptor" => [
        "tipoDocumento" => "37",
        "numDocumento" => "000000000000",
        "nombre" => "CONSUMIDOR FINAL"
    ],
    "cuerpoDocumento" => [
        [
            "numItem" => 1,
            "descripcion" => "Producto XYZ",
            "cantidad" => 1,
            "precioUni" => 100.00,  // Precio CON IVA incluido
            "ventaGravada" => 88.50,  // Base sin IVA (precioUni / 1.13)
            "ivaItem" => 11.50       // IVA extraído (100.00 - 88.50)
        ]
    ],
    "resumen" => [
        "totalGravada" => 88.50,
        "totalIva" => 11.50,
        "totalPagar" => 88.50,
        "condicionOperacion" => 1,
        "pagos" => [
            [
                "codigo" => "03",
                "montoPago" => 100.00
            ]
        ],
        "tributos" => [
            [
                "codigo" => "20",
                "valor" => 11.50
            ]
        ]
    ]
];

echo json_encode($json_ejemplo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// ═══════════════════════════════════════════════════════════════════════════════
// CHECKLIST DE VERIFICACIÓN
// ═══════════════════════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "CHECKLIST PARA RESOLVER ERROR 094\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

$checklist = [
    "✅ codigoGeneracion es UUID v4 (36 chars, patrón: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)",
    "✅ numeroControl se genera ANTES de validar",
    "✅ numeroControl tiene formato DTE-01-P001M001-000000000000001 (31 chars)",
    "✅ nrc tiene exactamente 4 dígitos",
    "✅ ambiente es '00' (test) o '01' (producción)",
    "✅ tipoDte es '01' (string, no integer)",
    "✅ version es 1 (integer)",
    "✅ tipoModelo es 1 o 2 (number)",
    "✅ Precios en cuerpoDocumento incluyen IVA",
    "✅ ventaGravada = precioUni / 1.13 (base sin IVA)",
    "✅ ivaItem = ventaGravada * 0.13 (o precioUni - ventaGravada)",
    "✅ Emisor NIT tiene 14 dígitos",
    "✅ Receptor tipoDocumento es válido ('01', '02', '03', '04', '37')",
    "✅ Cantidad > 0",
    "✅ Montos no negativos",
    "✅ Fechas en formato YYYY-MM-DD",
    "✅ Horas en formato HH:MM:SS",
    "✅ Totales en resumen son consistentes",
    "✅ Hay mínimo un pago en resumen.pagos",
    "✅ Hay tributo IVA (código 20) en resumen.tributos"
];

foreach ($checklist as $item) {
    echo "  $item\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "Si todos los items están ✅, el JSON debería ser aceptado por el MH\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
