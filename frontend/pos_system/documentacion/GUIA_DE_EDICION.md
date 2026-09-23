# 🔧 GUÍA DE EDICIÓN - DÓNDE ESTÁ QUÉ

Esta guía te ayuda a encontrar exactamente qué código cambió y dónde editarlo si necesitas hacer ajustes.

---

## 1. CÁLCULO DE IVA - pos_sale.php

### DÓNDE ENCONTRARLO
```
Archivo: /views/pos_sale.php
Función: prepareDTEJson(metodoPago, paymentDetails = null)
Líneas: ~1280-1450 (aproximadamente)
```

### QUÉ BUSCAR
```javascript
const totalVentaConIVA = calculateTotal();
const totalVentaSinIVA = parseFloat((totalVentaConIVA / 1.13).toFixed(2));
const totalIVA = parseFloat((totalVentaConIVA - totalVentaSinIVA).toFixed(2));
```

### ESTRUCTURA DE LA FUNCIÓN

```javascript
function prepareDTEJson(metodoPago, paymentDetails = null) {
    // PASO 1: Calcular totales
    const totalVentaConIVA = calculateTotal();
    const totalVentaSinIVA = parseFloat((totalVentaConIVA / 1.13).toFixed(2));
    const totalIVA = parseFloat((totalVentaConIVA - totalVentaSinIVA).toFixed(2));
    
    // PASO 2: Procesar cada item del carrito
    const cuerpoDocumento = localCart.map((item, index) => {
        const precioConIVA = item.price;  // YA INCLUYE IVA
        const cantidad = item.quantity;
        let ventaGravadaTotal = precioConIVA * cantidad;  // Subtotal con IVA
        
        // PASO 3: Si hay descuento, aplicarlo
        let montoDescu = 0;
        if (item.discount) { ... }
        
        // PASO 4: CRÍTICO - Extraer IVA
        const ventaSinIVA = parseFloat((ventaGravadaTotal / 1.13).toFixed(2));
        const ivaDelItem = parseFloat((ventaGravadaTotal - ventaSinIVA).toFixed(2));
        
        return {
            "ventaGravada": parseFloat(ventaGravadaTotal.toFixed(2)),
            "ivaItem": ivaDelItem,
            // ... otros campos
        };
    });
    
    // PASO 5: Construir tributos
    const tributosArray = totalIVA > 0 ? [{
        "codigo": "20",
        "descripcion": "Impuesto al Valor Agregado 13%",
        "valor": totalIVA
    }] : [];
    
    // PASO 6: Retornar JSON con resumen
    return {
        "identificacion": { ... },
        "cuerpoDocumento": cuerpoDocumento,
        "resumen": {
            "totalGravada": parseFloat(totalVentaConIVA.toFixed(2)),
            "montoTotalOperacion": parseFloat(totalVentaSinIVA.toFixed(2)),
            "totalPagar": parseFloat(totalVentaConIVA.toFixed(2)),
            "totalIva": totalIVA,
            "tributos": tributosArray,  // INCLUYE TRIBUTOS
            // ... otros campos
        }
    };
}
```

### SI NECESITAS EDITAR
- **Cambiar decimales**: Edita `.toFixed(2)` a `.toFixed(3)` o lo que necesites
- **Cambiar tarifa IVA**: Edita `/ 1.13` por `/ 1.XX` donde XX es la nueva tarifa
- **Cambiar código de tributo**: Edita `"codigo": "20"` a otro código según MH
- **Cambiar descripción**: Edita `"descripcion": "Impuesto al Valor Agregado 13%"`

---

## 2. VALIDADOR - dte_validator_new.php

### DÓNDE ENCONTRARLO
```
Archivo: /includes/signer/utils/dte_validator_new.php
Función: validar_json_dte_nuevo(array $dte): array
Líneas: Completo (240 líneas)
```

### ESTRUCTURA DE VALIDACIONES

```php
function validar_json_dte_nuevo(array $dte): array {
    $errors = [];
    
    // VALIDACIÓN 1: numeroControl
    if (empty($dte['identificacion']['numeroControl'])) {
        $errors[] = "CRÍTICO: numeroControl está vacío";
    } else {
        $nc = $dte['identificacion']['numeroControl'];
        if (!preg_match('/^DTE-\d{2}-[A-Z0-9]{8}-\d{15}$/', $nc)) {
            $errors[] = "Formato incorrecto: $nc";
        }
    }
    
    // VALIDACIÓN 2: codigoGeneracion
    if (empty($dte['identificacion']['codigoGeneracion'])) {
        $errors[] = "CRÍTICO: codigoGeneracion está vacío";
    }
    
    // VALIDACIÓN 3: Por cada item
    foreach ($dte['cuerpoDocumento'] as $idx => $item) {
        // Validar ventaGravada
        // Validar ivaItem
        // Validar descripción, cantidad, etc.
    }
    
    // VALIDACIÓN 4: Resumen
    if (!isset($resumen['totalPagar'])) {
        // totalPagar DEBE = totalGravada
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'warnings' => $warnings,
        'model' => 'CONSUMIDOR_FINAL_CON_IVA_INCLUIDO'
    ];
}
```

### SI NECESITAS EDITAR
- **Cambiar formato numeroControl**: Edita regex `/^DTE-\d{2}-[A-Z0-9]{8}-\d{15}$/`
- **Cambiar fórmula de IVA**: Busca `$ventaGravada - ($ventaGravada / 1.13)` y edita
- **Agregar validación**: Agrega un `if` con `$errors[] = "Tu error"`
- **Agregar warning**: Agrega a `$warnings` en lugar de `$errors`

---

## 3. INTEGRACIÓN - process_sale_complete.php

### DÓNDE ENCONTRARLO
```
Archivo: /views/ajax/process_sale_complete.php
Líneas: ~30 (require), ~40-60 (validación)
```

### ESTRUCTURA

```php
// LÍNEA ~30: Incluir validador
require_once __DIR__ . '/../../includes/signer/utils/dte_validator_new.php';

// LÍNEA ~45-60: Validar antes de procesar
$validacion = validar_json_dte_nuevo($dteData);
if (!$validacion['valid']) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'error' => 'JSON DTE inválido: ' . implode(' | ', $validacion['errors']),
        'validation_errors' => $validacion['errors']
    ]);
    exit;
}

// Si llega aquí, es válido
// ... continuar con firma y envío
```

### SI NECESITAS EDITAR
- **Cambiar función validadora**: Edita `validar_json_dte_nuevo()` por otra
- **Cambiar cuántos errores mostrar**: Edita `array_slice($validacion['errors'], 0, 3)` para mostrar más
- **Cambiar manejo de error**: Edita la estructura del JSON retornado

---

## 4. INICIALIZACIÓN - mhControl en pos_sale.php

### DÓNDE ENCONTRARLO
```
Archivo: /views/pos_sale.php
Líneas: ~546-550
```

### ESTRUCTURA

```javascript
const mhControl = {
    codigoGeneracion: "<?= $codigoGeneracion ?>",  // Viene del servidor
    numeroControl: null,                            // Se genera después
    fechaEmision: "<?= date('Y-m-d') ?>",          // Hoy
    horaEmision: "<?= date('H:i:s') ?>"            // Ahora
};
```

### SI NECESITAS EDITAR
- **Cambiar fuente de codigoGeneracion**: Edita la parte `<?= ... ?>`
- **Cambiar fecha**: Edita `date('Y-m-d')` a otro formato
- **Cambiar hora**: Edita `date('H:i:s')` a otro formato

---

## 5. BÚSQUEDA RÁPIDA

Si buscas algo específico, usa estos términos:

### Búsqueda en pos_sale.php
```
// Encontrar prepareDTEJson:
Ctrl+F: "function prepareDTEJson"

// Encontrar mhControl:
Ctrl+F: "const mhControl"

// Encontrar cálculo de IVA:
Ctrl+F: "totalVentaSinIVA"
Ctrl+F: "ivaDelItem"
```

### Búsqueda en process_sale_complete.php
```
// Encontrar validación:
Ctrl+F: "validar_json_dte_nuevo"

// Encontrar generación de numeroControl:
Ctrl+F: "numeroControl ="

// Encontrar integración:
Ctrl+F: "dte_validator_new"
```

### Búsqueda en dte_validator_new.php
```
// Encontrar fórmula de IVA:
Ctrl+F: "ventaGravada / 1.13"

// Encontrar validación de totalPagar:
Ctrl+F: "totalPagar"

// Encontrar tributos:
Ctrl+F: "codigo.*20"
```

---

## 6. LOGS Y DEBUGGING

### DÓNDE ESTÁN LOS LOGS
```
/includes/signer/utils/logs/audit.log
```

### QUÉ BUSCAR EN LOGS
```
[JSON_GENERATED]           ← JSON que se creó
[FIRMA_LOCAL_RESPONSE]     ← Respuesta del firmador
[MH_PAYLOAD_SEND]          ← Datos enviados al MH
[MH_RESPONSE_COMPLETE]     ← Respuesta del MH (error 094 aquí)
[VALIDATION_FAILED]        ← Si el validador rechazó
```

### BUSCAR ERROR 094
```bash
grep "094" /includes/signer/utils/logs/audit.log
```

---

## 7. ARCHIVOS SIN CAMBIOS (PERO IMPORTANTES)

Si necesitas revisar estos, tienen funcionalidad relacionada:

```
/includes/signer/signer_local.php
  - Firma el JSON con certificado local
  - Ya tiene integración con audit_logger.php
  
/includes/signer/signer_goes.php
  - Envía JSON firmado al MH
  - Captura respuesta del MH
  - Ya tiene integración con audit_logger.php
  
/includes/signer/utils/audit_logger.php
  - Registra todo el flujo
  - Funciones: audit_log(), audit_log_json_generated(), etc.
```

---

## 8. FLUJO COMPLETO DE DATOS

Para entender cómo fluyen los datos:

```
POS (HTML/JavaScript)
    ↓ prepareDTEJson() creates JSON
    ↓ Envía por AJAX a process_sale_complete.php
    ↓
SERVER (process_sale_complete.php)
    ↓ validar_json_dte_nuevo() verifica estructura
    ↓ SI FALLA: retorna error al cliente
    ↓ SI PASA: genera numeroControl
    ↓ Guarda en DB
    ↓ Llama a signer_local.php
    ↓
LOCAL SIGNER (signer_local.php)
    ↓ Firma el JSON con certificado
    ↓ Retorna JSON firmado
    ↓
MH SUBMISSION (signer_goes.php)
    ↓ Envía JSON firmado al MH
    ↓ MH responde: 094, 000, etc.
    ↓ Retorna respuesta al POS
    ↓
POS JavaScript
    ↓ Muestra resultado al usuario
```

---

## 9. TABLA DE CORRESPONDENCIA

| Componente | Archivo | Función | Líneas |
|-----------|---------|---------|-------|
| JSON generation | pos_sale.php | prepareDTEJson | ~1280-1450 |
| Validation | dte_validator_new.php | validar_json_dte_nuevo | Completo |
| Integration | process_sale_complete.php | [main] | ~30, ~45-60 |
| Initialization | pos_sale.php | [script] | ~546-550 |
| Signing | signer_local.php | [main] | Completo |
| Submission | signer_goes.php | [main] | Completo |
| Logging | audit_logger.php | audit_log* | Completo |

---

## 10. CAMBIOS MÍNIMOS REQUERIDOS

Si solo quieres hacer cambios puntuales:

### Para cambiar tarifa IVA (13% → 16%):
1. pos_sale.php: `/ 1.13` → `/ 1.16`
2. dte_validator_new.php: `/ 1.13` → `/ 1.16`
3. dte_validator_new.php: `"descripción"` actualizar porcentaje

### Para cambiar código de tributo:
1. pos_sale.php: `"codigo": "20"` → `"codigo": "XX"`
2. dte_validator_new.php: `codigo' === '20'` → `codigo' === 'XX'`

### Para agregar nuevas validaciones:
1. dte_validator_new.php: Agregar nuevo `if` con `$errors[]`
2. process_sale_complete.php: Opcional, mostrar error correspondiente

---

**Última actualización**: 2024  
**Versión**: 1.0  
**Validez**: Hasta nueva revisión

