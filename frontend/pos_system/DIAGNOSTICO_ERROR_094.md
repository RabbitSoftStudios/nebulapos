# GUÍA DE DIAGNÓSTICO - ERROR 094 "PARAMETROS NO SON VALIDOS"

## 📋 Resumen del Sistema de Logging

Se ha implementado un sistema de auditoría centralizado que captura **TODO** el flujo de creación de facturas electrónicas:

1. **JSON DTE generado** - Antes de firma local
2. **Respuesta del Firmador Local** - Después de firmar
3. **Payload enviado a MH** - Exactamente lo que se envía a Ministerio
4. **Respuesta completa de MH** - La respuesta del gobierno

## 🗂️ Archivos de Log

### 1. **Log Centralizado de Auditoría** (PRINCIPAL)
```
/includes/signer/audit_dte_complete.log
```

**Formato:**
```
############################################################
  CODIGO DE GENERACION: [UUID]
  NUMERO DE CONTROL: DTE-01-P001M001-[NUMERO]
  TIMESTAMP: YYYY-MM-DD HH:mm:ss
  STEP: [JSON_GENERATED|FIRMA_LOCAL_RESPONSE|MH_PAYLOAD_SEND|MH_RESPONSE_COMPLETE|PROCESS_FINAL_SUMMARY|ERROR_OCCURRED]
############################################################
[Datos JSON formateados]
```

**Pasos capturados:**
- `JSON_GENERATED` - El DTE exacto ANTES de firma
- `FIRMA_LOCAL_RESPONSE` - Respuesta del firmador local
- `MH_PAYLOAD_SEND` - Lo que se envió a Ministerio
- `MH_RESPONSE_COMPLETE` - La respuesta completa del MH
- `PROCESS_FINAL_SUMMARY` - Resumen final (éxito o error)
- `ERROR_OCCURRED` - Errores en cualquier etapa

### 2. Log Antiguo de Auditoría (aún activo)
```
/includes/signer/axelcrashed_debug.log
```
Este log sigue funcionando para compatibilidad, pero el PRINCIPAL es `audit_dte_complete.log`

### 3. Log del Firmador Local
```
/includes/signer/signer_local_debug.log
```
Detalles de validación de schema y errores internos

## 🔍 Cómo Diagnosticar el Error 094

### Error 094: "PARAMETROS NO SON VALIDOS"
```json
{
    "codigoMsg": "094",
    "descripcionMsg": "PARAMETROS NO SON VALIDOS",
    "observaciones": ["Faltan datos en peticion para procesar informacion"]
}
```

### Pasos para Diagnosticar:

#### 1️⃣ Buscar el Código de Generación de tu Factura Fallida
- En `audit_dte_complete.log`, busca el `codigoGeneracion`
- Por ejemplo: `B2C91C1A-1B25-F87B-D038-8C9B0869B8E1`

#### 2️⃣ Revisar el JSON Generado (Paso 1)
Busca la sección `JSON_GENERATED`:

```javascript
// DEBE CONTENER EXACTAMENTE:
{
  "identificacion": {
    "version": 1,
    "ambiente": "00",
    "tipoDte": "01",
    "numeroControl": "DTE-01-S001P001-000000000000001",  // ⚠️ CRÍTICO
    "codigoGeneracion": "B2C91C1A-...",
    "tipoModelo": 1,
    "tipoOperacion": 1,
    "tipoContingencia": null,
    "motivoContin": null,
    "fecEmi": "YYYY-MM-DD",  // ⚠️ Formato YYYY-MM-DD
    "horEmi": "HH:mm:ss",     // ⚠️ Formato HH:mm:ss
    "tipoMoneda": "USD"
  },
  "emisor": {
    "nit": "14 dígitos",       // ⚠️ Exactamente 14
    "nrc": "número",
    "nombre": "string",
    "codActividad": "5 dígitos", // ⚠️ Código actividad válido
    "descActividad": "string",
    "nombreComercial": null,
    "tipoEstablecimiento": "02",
    "direccion": {
      "departamento": "01",  // ⚠️ 2 dígitos
      "municipio": "08",     // ⚠️ 2 dígitos
      "complemento": "string"
    },
    "telefono": "string",
    "correo": "email@domain.com",
    "codEstableMH": "P001",
    "codEstable": "P001",
    "codPuntoVentaMH": "M001",
    "codPuntoVenta": "M001"
  },
  "receptor": {
    "tipoDocumento": null,
    "numDocumento": null,
    "nrc": null,
    "nombre": "string",        // ⚠️ REQUERIDO (no null)
    "codActividad": "5 dígitos", // ⚠️ Puede ser "10005" para "Otros"
    "descActividad": "string",
    "direccion": {
      "departamento": "XX",
      "municipio": "XX",
      "complemento": "string"
    },
    "telefono": null,
    "correo": "email@domain.com"
  },
  "otrosDocumentos": null,
  "ventaTercero": null,
  "cuerpoDocumento": [
    {
      "tipoItem": 1,
      "numeroDocumento": null,
      "codTributo": null,
      "descripcion": "string",
      "cantidad": 1.0,
      "uniMedida": 59,      // ⚠️ Código INCOTERM correcto
      "precioUni": number,
      "montoDescu": 0.0,
      "ventaNoSuj": 0.0,
      "ventaExenta": 0.0,
      "ventaGravada": number,  // ⚠️ CRÍTICO - debe coincidir con cantidad * precioUni
      "tributos": null,
      "psv": 0.0,
      "noGravado": 0.0,
      "codigo": "string",
      "ivaItem": number,   // ⚠️ 13% de ventaGravada
      "numItem": 1
    }
  ],
  "resumen": {
    "totalNoSuj": 0.0,
    "totalExenta": 0.0,
    "totalGravada": number,   // ⚠️ Suma de ventaGravada
    "subTotalVentas": number,
    "descuNoSuj": 0.0,
    "descuExenta": 0.0,
    "descuGravada": 0.0,
    "porcentajeDescuento": 0.0,
    "totalDescu": 0.0,
    "tributos": [],
    "subTotal": number,
    "ivaRete1": 0.0,
    "reteRenta": 0.0,
    "montoTotalOperacion": number,  // ⚠️ CRÍTICO - totalGravada sin descuentos
    "totalNoGravado": 0.0,
    "totalPagar": number,           // ⚠️ CRÍTICO - cantidad total a pagar
    "totalLetras": "CIENTO TRECE 00/100 DOLARES",
    "totalIva": number,             // ⚠️ 13% de totalGravada
    "saldoFavor": 0.0,
    "condicionOperacion": 1 | 2,
    "pagos": null,
    "numPagoElectronico": null
  },
  "extension": {
    "nombEntrega": null,
    "docuEntrega": null,
    "nombRecibe": null,
    "docuRecibe": null,
    "observaciones": null,
    "placaVehiculo": null
  },
  "apendice": null
}
```

#### 3️⃣ Verificar Validaciones Clave

**Campos CRÍTICOS que causan error 094:**

| Campo | Validación | Causa de Error |
|-------|-----------|------------------|
| `numeroControl` | Formato: `DTE-XX-P001M001-[15 dígitos]` | Si está vacío o formato incorrecto |
| `fecEmi` | Formato `YYYY-MM-DD` | Si está en otro formato (DD/MM/YYYY, etc) |
| `horEmi` | Formato `HH:mm:ss` (24h) | Si falta o está mal formateado |
| `nit` (emisor) | Exactamente 14 dígitos | Si tiene menos/más dígitos |
| `codActividad` (emisor) | 5 dígitos válidos | Si no existe en catálogo MH |
| `codActividad` (receptor) | 5 dígitos válidos | Si está vacío |
| `nombre` (receptor) | String no vacío | Si está null o empty |
| `ventaGravada` (items) | = cantidad * precioUni | Si no cuadra matemáticamente |
| `totalGravada` (resumen) | Suma exacta de items | Si está desincronizado |
| `totalPagar` | Debe incluir IVA | Si no coincide con resumen |
| `uniMedida` | Código INCOTERM válido | Si no es 59 (UNITARIO) |

#### 4️⃣ Revisar Firma Local
Busca la sección `FIRMA_LOCAL_RESPONSE`:

```json
{
  "success": true,
  "body_length": 4110,
  "body_preview": "MIIDtTCCAyqgAwIBAgIJAOqf...",
  "error": null,
  "timestamp": "2026-01-20 18:19:00"
}
```

**Si aquí hay error:**
- La firma local falló
- El JSON que llegó fue rechazado por el firmador
- Revisar el JSON_GENERATED anterior

#### 5️⃣ Revisar Payload a MH
Busca la sección `MH_PAYLOAD_SEND`:

```json
{
  "ambiente": "00",
  "idEnvio": 1,
  "version": 1,
  "tipoDte": "01",
  "documento_length": 4110,
  "documento_preview": "MIIDtTCCAyqgAwIBAgIJAOqf...",
  "codigoGeneracion": "B2C91C1A-...",
  "url_destino": "https://apitest.dtes.mh.gob.sv/fesv/recepciondte",
  "timestamp": "2026-01-20 18:19:00"
}
```

**Verificar:**
- `ambiente`: "00" para test, "01" para producción
- `documento_length`: Debe ser > 1000 (es base64 comprimido)
- `url_destino`: Correcta según ambiente

#### 6️⃣ Revisar Respuesta MH (La Clave)
Busca la sección `MH_RESPONSE_COMPLETE`:

```json
{
  "http_code": 400,
  "success": true,
  "estado": "RECHAZADO",
  "codigoMsg": "094",
  "descripcionMsg": "PARAMETROS NO SON VALIDOS",
  "selloRecibido": null,
  "observaciones": [
    "Faltan datos en peticion para procesar informacion"
  ],
  "fhProcesamiento": "20/01/2026 18:19:00",
  "full_response": { /* ... */ },
  "timestamp": "2026-01-20 18:19:00"
}
```

**Acción:**
- Revisar `observaciones` - MH te dice cuál dato falta
- Si dice "Faltan datos", comparar JSON con ejemplo Python de arriba
- Typical issues:
  - `numeroControl` vacío o mal formateado
  - Fechas/horas en formato incorrecto
  - NIT sin 14 dígitos
  - Código de actividad inválido

## 📝 Checklist de Revisión Rápida

```
☑️ numeroControl: DTE-01-P001M001-[15 dígitos]
☑️ fecEmi: Formato YYYY-MM-DD (no DD/MM/YYYY)
☑️ horEmi: Formato HH:mm:ss (24 horas)
☑️ nit (emisor): Exactamente 14 dígitos
☑️ codActividad (emisor): 5 dígitos, válido en catálogo
☑️ codActividad (receptor): 5 dígitos, no null
☑️ nombre (receptor): No vacío
☑️ ventaGravada (items): = cantidad * precioUni - descuento
☑️ totalGravada: Suma exacta de ventaGravada de items
☑️ totalPagar: totalGravada + IVA
☑️ uniMedida: 59 para unidades
☑️ direccion.departamento: 2 dígitos (01-14)
☑️ direccion.municipio: 2 dígitos (01-23)
☑️ tipoEstablecimiento: 02 o valor válido
☑️ codEstableMH/codPuntoVentaMH: Formatos P001/M001
```

## 🛠️ Cómo Acceder a los Logs

### Opción 1: VS Code
1. Abre `/includes/signer/audit_dte_complete.log`
2. Busca tu `codigoGeneracion`
3. Lee desde `JSON_GENERATED` hasta `MH_RESPONSE_COMPLETE`

### Opción 2: Terminal
```bash
# Ver últimos 50 logs
tail -n 50 includes/signer/audit_dte_complete.log

# Buscar por código de generación
grep -n "B2C91C1A-1B25-F87B-D038-8C9B0869B8E1" includes/signer/audit_dte_complete.log

# Ver todo el bloque para ese código
grep -A 50 "B2C91C1A-1B25-F87B-D038-8C9B0869B8E1" includes/signer/audit_dte_complete.log
```

### Opción 3: PHP Script (Quick Viewer)
```php
<?php
$log = file_get_contents(__DIR__ . '/includes/signer/audit_dte_complete.log');
$entries = explode('############################################################', $log);
$search = 'B2C91C1A-1B25-F87B-D038-8C9B0869B8E1';

foreach ($entries as $entry) {
    if (strpos($entry, $search) !== false) {
        echo "<pre>" . htmlspecialchars($entry) . "</pre>";
        break;
    }
}
?>
```

## 📌 Resumen de Cambios Realizados

✅ **Archivo:** `/includes/signer/utils/audit_logger.php`
- Funciones centralizadas para logging
- Separa información en steps claros
- Formatea JSON para legibilidad

✅ **Archivo:** `/includes/signer/signer_local.php`
- Log del JSON ANTES de firma
- Log de respuesta del firmador
- Log de errores

✅ **Archivo:** `/includes/signer/signer_goes.php`
- Log del payload a enviar a MH
- Log de respuesta completa de MH
- Log de errores de comunicación

✅ **Archivo:** `/views/ajax/process_sale_complete.php`
- Integración de audit_logger
- Paso de numeroControl a funciones de firma
- Log de resumen final

## 🚀 Próximos Pasos

Una vez identifiques qué dato le falta a MH:

1. **Localiza el campo** en `pos_sale.php` o `prepareDTEJson()`
2. **Verifica el formato** contra el JSON de ejemplo Python
3. **Corrige la generación** del DTE
4. **Reinicia la venta** y monitorea los nuevos logs
5. **Compara el JSON** antes/después entre intentos fallidos y exitosos

## 📞 Soporte

Si necesitas información adicional:
- Revisa el archivo `audit_dte_complete.log` completamente
- Compara con el JSON de ejemplo en producción (Python)
- Verifica catálogos de códigos en MH (actividades, departamentos, municipios)

---

**Actualizado:** 2026-01-20
**Versión:** 1.0 - Sistema de Auditoría Centralizado
