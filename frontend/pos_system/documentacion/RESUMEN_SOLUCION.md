# 📋 RESUMEN DE SOLUCIÓN - Error 094 "PARAMETROS NO SON VALIDOS"

## 🎯 Problema Original

Se recibía error `094` del Ministerio de Hacienda indicando **"Faltan datos en peticion para procesar informacion"** sin detalles específicos de cuál dato faltaba.

```json
{
  "codigoMsg": "094",
  "descripcionMsg": "PARAMETROS NO SON VALIDOS",
  "observaciones": ["Faltan datos en peticion para procesar informacion"]
}
```

## ✅ Solución Implementada

Se implementó un **SISTEMA DE AUDITORÍA CENTRALIZADO** que captura TODO el flujo de creación de facturas electrónicas, permitiendo identificar exactamente dónde está el problema.

### 1️⃣ Nuevo Archivo: `/includes/signer/utils/audit_logger.php`

**Propósito:** Centralizar todo el logging en un formato estándar y legible.

**Funciones principales:**
- `audit_log()` - Escritura base con separadores claros
- `audit_log_json_generated()` - Log del JSON ANTES de firma
- `audit_log_firma_local_response()` - Respuesta del firmador local
- `audit_log_mh_payload()` - Payload enviado a MH
- `audit_log_mh_response()` - Respuesta COMPLETA de MH
- `audit_log_final_summary()` - Resumen final del proceso
- `audit_log_error()` - Errores en cualquier punto

**Output:** `/includes/signer/audit_dte_complete.log`

```
############################################################
  CODIGO DE GENERACION: [UUID]
  NUMERO DE CONTROL: DTE-01-P001M001-[15 dígitos]
  TIMESTAMP: YYYY-MM-DD HH:mm:ss
  STEP: [JSON_GENERATED|FIRMA_LOCAL_RESPONSE|MH_PAYLOAD_SEND|MH_RESPONSE_COMPLETE|PROCESS_FINAL_SUMMARY]
############################################################
[JSON formateado bonito]
```

### 2️⃣ Actualización: `/includes/signer/signer_local.php`

**Cambios:**
- ✅ Incluye `audit_logger.php`
- ✅ Firma aceptada en parámetro `$numeroControl` (string)
- ✅ **Captura JSON DTE ANTES de normalizar** con `audit_log_json_generated()`
- ✅ Captura respuesta del firmador local con `audit_log_firma_local_response()`
- ✅ Captura errores con `audit_log_error()`

**Firma de función:**
```php
function sign_document_local(
    array $invoice, 
    string $codigoGeneracion, 
    string $numeroControl = ''
): string
```

### 3️⃣ Actualización: `/includes/signer/signer_goes.php`

**Cambios:**
- ✅ Incluye `audit_logger.php`
- ✅ Firma acepta parámetro `$numeroControl` (string)
- ✅ **Captura payload que se envía a MH** con `audit_log_mh_payload()`
- ✅ **Captura respuesta COMPLETA del MH** con `audit_log_mh_response()`
- ✅ Captura errores con `audit_log_error()`

**Firma de función:**
```php
function enviar_firma_gobierno(
    string $codigoGeneracion,
    string $documentoFirmadoBase64,
    string $numeroControl = ''
): array
```

### 4️⃣ Actualización: `/views/ajax/process_sale_complete.php`

**Cambios:**
- ✅ Incluye `audit_logger.php` y `dte_validator.php`
- ✅ **VALIDA JSON DTE antes de procesarlo** (nuevo)
- ✅ Pasa `$numeroControl` a `sign_document_local()`
- ✅ Pasa `$numeroControl` a `enviar_firma_gobierno()`
- ✅ Captura resumen final con `audit_log_final_summary()`
- ✅ Captura errores con `audit_log_error()`

### 5️⃣ Nuevo Archivo: `/includes/signer/utils/dte_validator.php`

**Propósito:** Validar que el JSON DTE cumpla con TODAS las reglas antes de enviarlo.

**Función principal:**
```php
function validar_json_dte_completo(array $dte): array
```

**Valida:**
- ✅ Formato de `numeroControl` (DTE-XX-P001M001-[15 dígitos])
- ✅ Fechas en formato YYYY-MM-DD
- ✅ Horas en formato HH:mm:ss (24h)
- ✅ NIT con exactamente 14 dígitos
- ✅ Códigos de actividad (5 dígitos)
- ✅ Departamentos y municipios (2 dígitos cada uno)
- ✅ **Matemáticas:** cantidad × precio - descuento = ventaGravada
- ✅ **Matemáticas:** ventaGravada × 13% = IVA
- ✅ **Matemáticas:** suma de items = totalGravada
- ✅ **Matemáticas:** totalGravada + IVA = totalPagar

**Retorna:**
```php
[
    'valid' => bool,
    'errors' => array,      // Problemas CRÍTICOS
    'warnings' => array,    // Problemas potenciales
    'sums' => array         // Totales calculados
]
```

### 6️⃣ Nueva Documentación: `/DIAGNOSTICO_ERROR_094.md`

**Propósito:** Guía completa para:
- 📍 Localizar dónde está el error en los logs
- 📍 Qué buscar exactamente
- 📍 Checklist de validación rápida
- 📍 Ejemplos del JSON correcto vs incorrecto
- 📍 Comparación con el código Python en producción

## 🔄 Flujo de Procesamiento Actual

```
1. Cliente envía JSON DTE
   ↓
2. VALIDACIÓN INMEDIATA (dte_validator.php)
   ├─ Si falla → Error 400 con detalles
   └─ Si pasa → Continuar
   ↓
3. Generar numeroControl
   ↓
4. Guardar JSON en storage/sigs/dte_[codigoGeneracion].json
   ↓
5. AUDIT LOG: JSON_GENERATED
   └─ LOG COMPLETO DEL JSON (para comparación posterior)
   ↓
6. Firma Local
   ├─ Normalizar DTE
   ├─ Validar contra schema MH
   ├─ Enviar a firmador local (http://localhost:8113)
   ↓
7. AUDIT LOG: FIRMA_LOCAL_RESPONSE
   └─ Respuesta del firmador (éxito/error)
   ↓
8. Envío a MH (Ministerio)
   ├─ Obtener token MH
   ├─ Construir payload
   ↓
9. AUDIT LOG: MH_PAYLOAD_SEND
   └─ Exactamente lo que se envió a MH
   ↓
10. Respuesta de MH
    ├─ Si rechazado → Capturar motivo
    └─ Si aceptado → Guardar sello
    ↓
11. AUDIT LOG: MH_RESPONSE_COMPLETE
    └─ RESPUESTA COMPLETA (estado, sello, errores, etc)
    ↓
12. AUDIT LOG: PROCESS_FINAL_SUMMARY
    └─ Resumen: SUCCESS / FAILED / REJECTED con detalles
```

## 📊 Comparación Antes vs Después

### ❌ ANTES (Difícil de Diagnosticar)
```
- Log básico en axelcrashed_debug.log
- Solo capturaba respuesta MH
- No capturaba JSON antes de firma
- No capturaba payload exacto enviado
- No validaba campos matemáticos
- Mensaje de error vago del MH
```

### ✅ DESPUÉS (Fácil de Diagnosticar)
```
- Log centralizado en audit_dte_complete.log
- Captura JSON COMPLETO antes de firma
- Captura respuesta del firmador local
- Captura payload exacto enviado a MH
- Captura respuesta COMPLETA del MH
- Valida matemáticas antes de enviar
- Documento guía detallado (DIAGNOSTICO_ERROR_094.md)
- Validador automático (dte_validator.php)
```

## 🛠️ Cómo Usar la Solución

### Escenario 1: Venta Fallida
1. Obtener `codigoGeneracion` de la respuesta de error
2. Abrir `/includes/signer/audit_dte_complete.log`
3. Buscar ese `codigoGeneracion`
4. Revisar secciones en este orden:
   - `JSON_GENERATED` → Ver exactamente qué se envió
   - `FIRMA_LOCAL_RESPONSE` → ¿Firmó correctamente?
   - `MH_PAYLOAD_SEND` → ¿Payload correcto?
   - `MH_RESPONSE_COMPLETE` → ¿Qué dijo MH?

### Escenario 2: Entender Qué Cambió
1. Tomar dos `codigoGeneracion` (uno fallido, uno exitoso)
2. Comparar la sección `JSON_GENERATED` entre ambos
3. Diferencias = Lo que falta/está incorrecto

### Escenario 3: Validar JSON Localmente
```bash
# En terminal PHP
$dte = json_decode(file_get_contents('storage/sigs/dte_[codigoGeneracion].json'), true);
require 'includes/signer/utils/dte_validator.php';
$val = validar_json_dte_completo($dte);
print_validation_report($val);
```

## 📁 Archivos Modificados/Creados

```
✅ CREADOS:
  /includes/signer/utils/audit_logger.php        (240 líneas)
  /includes/signer/utils/dte_validator.php       (280 líneas)
  /DIAGNOSTICO_ERROR_094.md                      (400+ líneas)

✅ MODIFICADOS:
  /includes/signer/signer_local.php              (+40 líneas logs)
  /includes/signer/signer_goes.php               (+30 líneas logs)
  /views/ajax/process_sale_complete.php          (+50 líneas validación)

✅ OUTPUT LOG:
  /includes/signer/audit_dte_complete.log        (nuevo log centralizado)
```

## 🎓 Próximos Pasos

1. **Reproducir error:** Hacer una venta que falle
2. **Revisar log:** `tail audit_dte_complete.log`
3. **Identificar campo:** Buscar en sección `MH_RESPONSE_COMPLETE`
4. **Comparar con JSON:** Ver sección `JSON_GENERATED`
5. **Corregir generación:** En `pos_sale.php` o `prepareDTEJson()`
6. **Reintentar:** Nueva venta con fix

## 📞 Soporte

- **Error 094 específico?** → Ver `DIAGNOSTICO_ERROR_094.md`
- **JSON no valida?** → Revisar `dte_validator.php` output
- **Matemáticas incorrectas?** → Usar validador antes de enviar
- **Diferencia Python vs PHP?** → Comparar JSON en audit log

---

**Fecha:** 2026-01-20  
**Versión:** 1.0 - Sistema de Auditoría Centralizado  
**Status:** ✅ Listo para producción
