# 🔍 GUÍA RÁPIDA: Cómo Leer los Logs de Auditoría

## 📍 Ubicación del Log Principal

```
/includes/signer/audit_dte_complete.log
```

## 🗂️ Estructura del Log

Cada factura electrónica genera **5-6 bloques de información** separados por líneas de `#`:

```
############################################################
  CODIGO DE GENERACION: B2C91C1A-1B25-F87B-D038-8C9B0869B8E1
  NUMERO DE CONTROL: DTE-01-P001M001-000000000000001
  TIMESTAMP: 2026-01-20 18:19:00
  STEP: JSON_GENERATED
############################################################
[CONTENIDO JSON FORMATEADO]

############################################################
  CODIGO DE GENERACION: B2C91C1A-1B25-F87B-D038-8C9B0869B8E1
  NUMERO DE CONTROL: DTE-01-P001M001-000000000000001
  TIMESTAMP: 2026-01-20 18:19:00
  STEP: FIRMA_LOCAL_RESPONSE
############################################################
[DATOS DE RESPUESTA]

... y así sucesivamente
```

## 📋 Los 6 Pasos Capturados

### 1️⃣ **STEP: JSON_GENERATED**
**¿Qué es?** El JSON DTE EXACTO que se envía a firma local

**Cuándo aparece:** Inmediatamente al validar el JSON de entrada

**Qué buscar:**
- ✅ `numeroControl` - Debe estar en formato `DTE-01-P001M001-[15 dígitos]`
- ✅ `fecEmi` - Debe ser `YYYY-MM-DD`
- ✅ `horEmi` - Debe ser `HH:mm:ss`
- ✅ `nit` (emisor) - Exactamente 14 dígitos
- ✅ `ventaGravada` - Debe coincidir con cantidad × precio
- ✅ `totalGravada` - Suma exacta de items
- ✅ `totalPagar` - Debe incluir IVA (13%)

**Si falta algo aquí → El error será en sección JSON_GENERATED**

```json
{
  "identificacion": {
    "version": 1,
    "ambiente": "00",           ← Debe ser "00" (test) o "01" (prod)
    "tipoDte": "01",            ← Siempre "01"
    "numeroControl": "DTE-01-P001M001-000000000000001",  ← CRÍTICO
    "codigoGeneracion": "B2C91C1A-...",
    "tipoModelo": 1,
    "tipoOperacion": 1,
    "fecEmi": "2026-01-20",     ← Formato YYYY-MM-DD
    "horEmi": "18:19:00",       ← Formato HH:mm:ss
    "tipoMoneda": "USD"
  },
  "emisor": {
    "nit": "06150911851010",    ← Exactamente 14 dígitos
    "codActividad": "46510",    ← 5 dígitos válidos
    ...
  },
  "receptor": {
    "nombre": "CLIENTE NOMBRE", ← NO DEBE ESTAR VACÍO
    "codActividad": "10005",    ← 5 dígitos válidos
    ...
  },
  "cuerpoDocumento": [
    {
      "descripcion": "Producto",
      "cantidad": 1.0,
      "precioUni": 100.0,
      "montoDescu": 0.0,
      "ventaGravada": 100.0,    ← Debe ser: cantidad × precioUni - descuento
      "ivaItem": 13.0           ← Debe ser: ventaGravada × 0.13
    }
  ],
  "resumen": {
    "totalGravada": 100.0,      ← Suma de todos los ventaGravada
    "totalIva": 13.0,           ← Suma de todos los ivaItem
    "totalPagar": 113.0         ← totalGravada + totalIva
  }
}
```

---

### 2️⃣ **STEP: FIRMA_LOCAL_RESPONSE**
**¿Qué es?** Respuesta del servicio local de firma (http://localhost:8113)

**Cuándo aparece:** Después de intentar firmar el documento

**Qué buscar:**
```json
{
  "success": true,              ← Debe ser true
  "body_length": 4110,          ← Firma BASE64 (mínimo 1000+)
  "body_preview": "MIIDtTCCAyqgAwIBAgIJAOqf...",  ← Firma truncada para preview
  "error": null,                ← Debe ser null si success=true
  "timestamp": "2026-01-20 18:19:00"
}
```

**Si aquí falla:**
- `success: false` → JSON enviado fue rechazado por normalizador/validador del firmware
- `body_length < 100` → Firma inválida
- `error: "..."` → Ver mensaje de error exacto

**Acción:** Si falla aquí, el problema está en el JSON_GENERATED. Comparar con ejemplo Python.

---

### 3️⃣ **STEP: MH_PAYLOAD_SEND**
**¿Qué es?** El PAYLOAD EXACTO que se envía al Ministerio de Hacienda

**Cuándo aparece:** Justo antes de hacer POST a API MH

**Qué buscar:**
```json
{
  "ambiente": "00",             ← "00"=test, "01"=prod
  "idEnvio": 1,
  "version": 1,
  "tipoDte": "01",
  "documento_length": 4110,     ← Longitud de la firma BASE64
  "documento_preview": "MIIDtTCCAyqgAwIBAgIJAOqf...",
  "codigoGeneracion": "B2C91C1A-...",
  "url_destino": "https://apitest.dtes.mh.gob.sv/fesv/recepciondte",
  "timestamp": "2026-01-20 18:19:00"
}
```

**Verificaciones:**
- ✅ `ambiente`: Debe coincidir con configuración
- ✅ `documento_length`: Debe ser > 1000 (es comprimido en BASE64)
- ✅ `url_destino`: Debe ser correcta según ambiente
- ✅ `tipoDte`: Debe ser "01"

---

### 4️⃣ **STEP: MH_RESPONSE_COMPLETE** ⭐ **LA MÁS IMPORTANTE**
**¿Qué es?** La RESPUESTA COMPLETA del Ministerio de Hacienda

**Cuándo aparece:** Después de que MH responde al POST

**Qué buscar:**
```json
{
  "http_code": 200,             ← 200=OK, 400=Error
  "success": true,              ← true=fue procesada por MH
  "estado": "PROCESADO",        ← "PROCESADO" o "RECHAZADO"
  "codigoMsg": "000",           ← "000"=éxito, "094"=error parámetros
  "descripcionMsg": "PROCESADO",
  "selloRecibido": "2600120123...",  ← Sello recepción (si PROCESADO)
  "observaciones": [],          ← Array de mensajes si hay rechazo
  "fhProcesamiento": "20/01/2026 18:19:00",
  "full_response": {
    "version": 2,
    "ambiente": "00",
    "versionApp": 2,
    "estado": "PROCESADO",
    "codigoGeneracion": "...",
    "selloRecibido": "...",
    ...
  },
  "timestamp": "2026-01-20 18:19:00"
}
```

**Casos Posibles:**

**✅ ÉXITO:**
```json
"estado": "PROCESADO",
"codigoMsg": "000",
"descripcionMsg": "PROCESADO",
"selloRecibido": "2600120123..."
```
→ **Factura aceptada por el gobierno** ✅

**❌ ERROR 094 (Tu caso):**
```json
"estado": "RECHAZADO",
"codigoMsg": "094",
"descripcionMsg": "PARAMETROS NO SON VALIDOS",
"observaciones": ["Faltan datos en peticion para procesar informacion"]
```
→ **Falta un campo en el JSON o está mal formateado**
→ **Comparar JSON_GENERATED con checklist arriba**

**❌ Otros errores posibles:**
- `"codigoMsg": "017"` → NIT no autorizado
- `"codigoMsg": "030"` → Ambiente incorrecto
- `"codigoMsg": "023"` → Código de actividad inválido

---

### 5️⃣ **STEP: PROCESS_FINAL_SUMMARY**
**¿Qué es?** Resumen final del proceso completo

**Cuándo aparece:** Al finalizar todo (éxito o error)

**Qué buscar - ÉXITO:**
```json
{
  "final_status": "SUCCESS",
  "estado_mh": "PROCESADO",
  "sello_recibido": "2600120123...",
  "total_pagar": 113.00,
  "cliente": "CLIENTE NOMBRE",
  "timestamp": "2026-01-20 18:19:00"
}
```

**Qué buscar - ERROR:**
```json
{
  "final_status": "FAILED",
  "error_message": "...",
  "error_detail": "...",
  "timestamp": "2026-01-20 18:19:00"
}
```

---

### 6️⃣ **STEP: ERROR_OCCURRED** (Solo si hay problema)
**¿Qué es?** Errores capturados en cualquier punto del proceso

**Cuándo aparece:** Si algo falla (validación, firma, comunicación, BD)

**Qué buscar:**
```json
{
  "error_message": "...mensaje del error...",
  "error_detail": "...detalles adicionales...",
  "file": "/path/to/file.php",
  "line": 123,
  "timestamp": "2026-01-20 18:19:00"
}
```

---

## 🚀 Cómo Diagnosticar Error 094

### Paso 1: Obtener el Código
```
Del mensaje de error o del cliente:
codigoGeneracion = "B2C91C1A-1B25-F87B-D038-8C9B0869B8E1"
```

### Paso 2: Abrir el Log
```bash
vim /includes/signer/audit_dte_complete.log
# Buscar:
grep "B2C91C1A-1B25-F87B-D038-8C9B0869B8E1" audit_dte_complete.log
```

### Paso 3: Encontrar las 4 Secciones
1. `JSON_GENERATED` ← El DTE que se envió
2. `FIRMA_LOCAL_RESPONSE` ← ¿Firmó correctamente?
3. `MH_PAYLOAD_SEND` ← Exacto lo que se envió a MH
4. `MH_RESPONSE_COMPLETE` ← La respuesta del MH

### Paso 4: Comparar
```
JSON_GENERATED vs El JSON de ejemplo Python
                    ↓
¿Están iguales? → SÍ → Problema en servidor MH
                 → NO → Identificar diferencia
```

### Paso 5: Usar el Validador
```bash
# En VS Code o terminal PHP:
php test_audit_system.php
```

### Paso 6: Aplicar Fix
1. Si JSON mal formado → Corregir en `pos_sale.php`
2. Si cálculos incorrectos → Corregir `prepareDTEJson()`
3. Si formato de fecha/hora → Revisar función de generación

---

## 💡 Quick Reference: Campos Críticos

| Campo | Formato | Ejemplo | Error Si |
|-------|---------|---------|----------|
| `numeroControl` | `DTE-XX-P001M001-[15 dígitos]` | `DTE-01-P001M001-000000000000001` | Mal formato → 094 |
| `fecEmi` | `YYYY-MM-DD` | `2026-01-20` | DD/MM/YYYY → 094 |
| `horEmi` | `HH:mm:ss` | `18:19:00` | 18:19 sin segundos → 094 |
| `nit` (emisor) | `[14 dígitos]` | `06150911851010` | 13 o 15 dígitos → 094 |
| `nombre` (receptor) | `string no vacío` | `JOSE RODRIGUEZ` | NULL o "" → 094 |
| `codActividad` | `[5 dígitos]` | `46510` | 4 o 6 dígitos → 094 |
| `ventaGravada` | `cantidad * precioUni - desc` | `100.0` | Mismatch → 094 |
| `totalGravada` | `suma de ventaGravada` | `100.0` | Mismatch → 094 |
| `totalPagar` | `totalGravada + totalIva` | `113.0` | Mismatch → 094 |
| `uniMedida` | `INCOTERM (típico 59)` | `59` | 58 o 60 → Posible error |

---

## 🔧 Ejemplos Reales de Diagnóstico

### Caso 1: Error 094 por numeroControl vacío
```json
// JSON_GENERATED:
"numeroControl": "",  ← ❌ PROBLEMA

// Solución:
// Revisar: generarCodigoGeneracion() en pos_sale.php
```

### Caso 2: Error 094 por fecha mal formateada
```json
// JSON_GENERATED:
"fecEmi": "20/01/2026",  ← ❌ PROBLEMA (DD/MM/YYYY en lugar de YYYY-MM-DD)

// Solución:
// Cambiar formato a: "2026-01-20"
```

### Caso 3: Error 094 por NIT incorrecto
```json
// JSON_GENERATED:
"nit": "061509118510",  ← ❌ 12 dígitos en lugar de 14

// Solución:
// Verificar NIT en configuración: debe tener exactamente 14 dígitos
```

### Caso 4: Error 094 por ventaGravada incorrecta
```json
// cuerpoDocumento:
{
  "cantidad": 2.0,
  "precioUni": 100.0,
  "montoDescu": 0.0,
  "ventaGravada": 150.0  ← ❌ Debería ser 200.0 (2 * 100)
}

// Solución:
// Revisar cálculo en prepareDTEJson()
```

---

## 📊 Estadísticas del Log

```bash
# Contar transacciones por día
grep "TIMESTAMP:" audit_dte_complete.log | grep "2026-01-20" | wc -l

# Ver últimas 100 transacciones
tail -n 1000 audit_dte_complete.log | grep "CODIGO DE GENERACION"

# Encontrar todas las que fallaron
grep -B 5 "final_status.*FAILED" audit_dte_complete.log

# Ver distribución de errores
grep "codigoMsg" audit_dte_complete.log | sort | uniq -c
```

---

## 🎓 Checklist de Lectura

Cuando veas error 094:

```
☑️ Abrir audit_dte_complete.log
☑️ Buscar el codigoGeneracion
☑️ Encontrar JSON_GENERATED
☑️ Verificar numeroControl (formato?)
☑️ Verificar fecEmi (YYYY-MM-DD?)
☑️ Verificar horEmi (HH:mm:ss?)
☑️ Verificar NIT emisor (14 dígitos?)
☑️ Verificar nombre receptor (no vacío?)
☑️ Verificar ventaGravada (matemáticas?)
☑️ Verificar totalPagar (con IVA?)
☑️ Encontrar MH_RESPONSE_COMPLETE
☑️ Ver observaciones exactas del MH
☑️ Comparar con ejemplo Python en producción
```

---

**Última actualización:** 2026-01-20  
**Versión:** 1.0
