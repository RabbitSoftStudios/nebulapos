# 🗺️ MAPA DE FLUJO COMPLETO DE FACTURACIÓN ELECTRÓNICA

## 📍 PUNTO DE ENTRADA: ¿QUÉ ARCHIVO GENERA EL JSON INICIAL?

### **RESPUESTA DIRECTA**
El **JSON inicial de facturación** se genera en:

```
📄 /views/pos_sale.php
   ↳ Función JavaScript: prepareDTEJson(metodoPago, paymentDetails)
   ↳ Líneas: ~1289-1450
```

**Descripción**: Esta función JavaScript prepara el objeto JSON con TODOS los datos de la factura (emisor, receptor, items, resumen, etc.) cuando el usuario finaliza la venta en el POS.

---

## 🔄 FLUJO COMPLETO: De Inicio a Fin

```
CLIENTE (POS)                    SERVIDOR (Backend)               MH (Ministerio)
    │                                 │                                  │
    ├─ 1. Usuario crea venta ────────>│                                  │
    │   (agregar productos)           │                                  │
    │                                 │                                  │
    ├─ 2. Click "Finalizar Venta"────>│                                  │
    │   (prepareDTEJson())            │                                  │
    │                                 │                                  │
    └─ 3. Envía JSON ───────────────>│ 4. Procesa factura               │
                                      │    - Genera numeroControl        │
                                      │    - Valida JSON                 │
                                      │    - Firma localmente            │
                                      │    - Genera QR                   │
                                      │                                  │
                                      │ 5. Envía al MH ────────────────>│
                                      │                                  │
                                      │<─ 6. Respuesta del MH ──────────┤
                                      │                                  │
                                      │ 7. Procesa respuesta             │
                                      │    - Genera PDF                  │
                                      │    - Guarda en BD                │
                                      │    - Registra auditoría          │
                                      │                                  │
    <─ 8. Retorna al usuario ────────┤                                  │
       (ticket/PDF)
```

---

## 📋 DETALLES DE CADA FASE

### **FASE 1️⃣: GENERACIÓN DEL JSON INICIAL** (Cliente)

```
Archivo Principal: /views/pos_sale.php
├─ Línea 521-530:    generarCodigoGeneracion()
│                    ↳ Genera UUID v4 (36 chars)
│
├─ Línea 1289-1450:  prepareDTEJson(metodoPago, paymentDetails)
│                    ↳ Crea objeto JSON completo con:
│                    ├─ identificacion { version, ambiente, tipoDte, etc }
│                    ├─ emisor { nit, nrc, nombre, etc }
│                    ├─ receptor { nombre, nrc, etc }
│                    ├─ cuerpoDocumento [] (items de la venta)
│                    └─ resumen { totales, tributos, pagos }
│
└─ Línea 1478-1540:  Envía JSON a proceso_sale_complete.php
                     via fetch() POST
                     └─ body: JSON.stringify(dteData)
```

**¿QUÉ DATOS INCLUYE?**
- CodigoGeneracion: UUID v4 generado en servidor (línea 542-543)
- numeroControl: NULL (se genera en servidor después)
- Datos emisor: NIT, NRC, nombre (hardcodeado en pos_sale.php líneas ~1405-1420)
- Datos receptor: Desde formulario del cliente
- Items: Carrito de compras (localCart array)
- Totales: Calculados con IVA incluido (precioUni / 1.13)

---

### **FASE 2️⃣: PROCESAMIENTO EN SERVIDOR** (Backend)

```
Archivo Principal: /views/ajax/process_sale_complete.php

Paso A: Recibir JSON (Línea 35-40)
├─ $dteData = json_decode($input, true)
└─ Validar que codigoGeneracion no esté vacío

Paso B: Generar numeroControl (Línea 48-87)
├─ Iniciar transacción con lock SQL
├─ SELECT MAX(numero) para obtener siguiente secuencial
├─ Generar: DTE-01-P001M001-000000000000001
└─ Asignar: $dteData['identificacion']['numeroControl']

Paso C: Validar JSON (Línea 93-105)
├─ require_once: dte_validator_new.php
├─ Llamar: validar_json_dte_nuevo($dteData)
└─ Si falla: rollback y error
    └─ Si pasa: continuar

Paso D: Guardar JSON original (Línea 107-112)
├─ Archivo: /storage/sigs/dte_{codigoGeneracion}.json
└─ Contenido: JSON formateado y sin encodificación

Paso E: Insertar en Base de Datos (Línea 114-122)
├─ Tabla: dte_facturas
├─ Campos: codigo_generacion, numero_control, fecha_emision, etc
└─ supabase('dte_facturas')->insert([...])

Paso F: Enviar a Firma Local (Línea 124-160)
├─ require_once: signer_local.php
├─ Llamar: sign_document_local($dteData, $codigoGeneracion)
│          └─ FIRMA: Aplica firma digital a la factura
│          └─ RETORNA: Factura firmada + firma base64
└─ Guardar respuesta de firma

Paso G: Enviar a Ministerio de Hacienda (Línea 162-210)
├─ API Endpoint: https://apitest.dtes.mh.gob.sv/fesv/recepciondte
├─ Payload: JSON firmado + metadatos
├─ Método: POST
└─ Recibir respuesta MH

Paso H: Procesar respuesta MH (Línea 212-250)
├─ Si aceptado: ✅ Continuar
│   ├─ Estado: 'ACEPTADO'
│   ├─ Generar QR: qr_maker.php
│   ├─ Generar PDF: pdf_generator.php
│   └─ Guardar archivos
│
└─ Si rechazado: ❌ Error
    ├─ Estado: 'RECHAZADO'
    ├─ Código error: Error 094, 095, etc
    └─ Mensaje: Descripción del error

Paso I: Auditoría y logs (Línea 252-280)
├─ audit_log_final_summary()
├─ Registrar en logs del sistema
└─ Guardar en tabla de auditoría

Paso J: Retornar respuesta al cliente (Línea 276-304)
└─ echo json_encode([
     'success' => true/false,
     'message' => 'Mensaje',
     'numeroControl' => '...',
     'qrPath' => '...',
     'pdfPath' => '...',
     'etc'
   ])
```

---

### **FASE 3️⃣: FIRMA LOCAL** (Signer)

```
Archivo Principal: /includes/signer/signer_local.php

Función: sign_document_local(array $invoice, string $codigoGeneracion)

Paso A: Validación (Línea 27-60)
├─ Validar NIT en configuración
├─ Validar PRIVATE_KEY disponible
├─ Validar DTE_JSON no esté vacío
└─ Extraer JSON de factura: $invoice['dte_json']

Paso B: Normalización (Línea 62-75)
├─ require_once: utils/normalizer.php
├─ normalizarDTE($dteData)
│  └─ Asegurar formatos correctos
│  └─ Eliminar espacios/caracteres inválidos
│  └─ Ordenar campos JSON
└─ Retorna JSON normalizado

Paso C: Validación contra Schema (Línea 77-95)
├─ require_once: utils/schema_validator.php
├─ validarDTEContraSchema($dteData, $schemaPath)
│  └─ Chequea contra fe-fc-v1.json del MH
│  └─ Valida todos los campos requeridos
└─ Si falla: Exception y error

Paso D: Crear Cadena de Firma (Línea 97-130)
├─ Extraer campos clave del DTE:
│  ├─ codigoGeneracion
│  ├─ numeroControl
│  ├─ totalGravada
│  ├─ totalIva
│  ├─ totalPagar
│  └─ etc
├─ Concatenar en orden específico: "G|N|T|..."
└─ Resultado: cadenaFirma (string)

Paso E: Firmar digitalmente (Línea 132-165)
├─ Cargar PRIVATE_KEY desde archivo
├─ Usar algoritmo: SHA256 con RSA
├─ openssl_sign($cadenaFirma, $signature, $privateKey, 'sha256')
│  └─ Genera firma digital binaria
├─ base64_encode($signature)
│  └─ Convierte a base64 para transmisión
└─ Resultado: $firmaBase64 (string)

Paso F: Crear respuesta (Línea 167-195)
├─ Generar JSON de respuesta con:
│  ├─ "status": "success"
│  ├─ "firma": $firmaBase64
│  ├─ "dte_firmado": { ...json con firma adjunta }
│  └─ "hash": hash de validación
└─ return string (JSON firmado)
```

---

### **FASE 4️⃣: ENVÍO A MINISTERIO DE HACIENDA**

```
Endpoint: https://apitest.dtes.mh.gob.sv/fesv/recepciondte

Payload (Línea 162-210 en process_sale_complete.php):
{
  "nitEmisor": "06150911851010",
  "nitReceptor": "000000000000",
  "codigoGeneracion": "550E8400-E29B-41D4-A716-446655440000",
  "dte": "{...dte_json_firmado...}",
  "firma": "0ACB03D1F4B2E5...base64..."
}

Respuesta MH:
{
  "estado": "ACEPTADO" | "RECHAZADO",
  "codigoGeneracion": "...",
  "numeroControl": "DTE-01-P001M001-000000000000001",
  "selloRecepcion": "...",
  "selloGeneracion": "...",
  "timestamp": "2024-01-20T14:30:45Z"
}

O ERROR:
{
  "estado": "RECHAZADO",
  "codigoError": "094",
  "descripcion": "PARÁMETROS NO SON VÁLIDOS",
  "detalles": "Campo X con formato incorrecto"
}
```

---

### **FASE 5️⃣: GENERACIÓN DE ARTEFACTOS** (Después de Aceptación)

```
Si la respuesta es ACEPTADA:

A. Generar QR (Línea 212-230)
   Archivo: /includes/signer/utils/qr_maker.php
   Función: generar_qr_mh(array $data)
   ├─ Datos para QR:
   │  ├─ codigoGeneracion
   │  ├─ numeroControl
   │  ├─ selloRecepcion (del MH)
   │  └─ selloGeneracion (del MH)
   ├─ Generar imagen PNG
   └─ Guardar: /storage/qr/dte_{codigoGeneracion}.png

B. Generar PDF (Línea 232-250)
   Archivo: /includes/signer/utils/pdf_generator.php
   Función: generar_factura_pdf(string $html, string $codigoGeneracion)
   ├─ Recibir HTML de ticket
   ├─ Usar librería TCPDF o Dompdf
   ├─ Insertar QR en PDF
   ├─ Formatear documento según MH
   └─ Guardar: /storage/pdf/dte_{codigoGeneracion}.pdf

C. Generar Ticket HTML (Línea 252-270)
   Archivo: /includes/signer/utils/ticket_printer.php
   Función: generar_ticket_html(array $dte, string $qrPath)
   ├─ Crear HTML formateado para impresora
   ├─ Incluir datos de factura
   ├─ Incluir QR
   └─ Retornar HTML para impresión

D. Enviar Email (Línea 272-290)
   Archivo: /includes/signer/utils/mail_sender.php
   Función: enviar_factura_email(string $email, string $pdfPath)
   ├─ Adjuntar PDF generado
   ├─ Enviar a correo del cliente
   └─ Registrar envío en auditoría
```

---

## 🎯 MAPEO VISUAL DE ARCHIVOS

```
FRONTEND (Navegador)
│
├─ /views/pos_sale.php ───────────────────────────────────────┐
│  ├─ prepareDTEJson()     ← Genera JSON inicial              │
│  │  └─ Líneas 1289-1450                                      │
│  │                                                             │
│  └─ generarCodigoGeneracion()  ← UUID v4                     │
│     └─ Líneas 521-530                                        │
│                                                               │
│  [Envía JSON via POST]                                       │
│                                                               │
└─────────────────────────────────────────────────────────────>│
                                                                │
BACKEND (Servidor PHP)                                         │
│                                                                │
├─ /views/ajax/process_sale_complete.php                       │
│  │                                                             │
│  ├─ Recibir JSON (línea 35)                                  │
│  │  └─ $dteData = json_decode()                              │
│  │                                                             │
│  ├─ Generar numeroControl (línea 48-87)                      │
│  │  └─ Con lock SQL para evitar duplicados                   │
│  │                                                             │
│  ├─ Validar JSON (línea 93-105)                              │
│  │  └─ require: /includes/signer/utils/dte_validator_new.php│
│  │     └─ validar_json_dte_nuevo($dteData)                   │
│  │                                                             │
│  ├─ Guardar JSON (línea 107-112)                             │
│  │  └─ /storage/sigs/dte_{codigoGeneracion}.json             │
│  │                                                             │
│  ├─ Insertar en BD (línea 114-122)                           │
│  │  └─ supabase('dte_facturas')->insert()                    │
│  │                                                             │
│  ├─ Firmar localmente (línea 124-160)                        │
│  │  └─ require: /includes/signer/signer_local.php            │
│  │     └─ sign_document_local($dteData, $codigoGeneracion)   │
│  │        ├─ require: utils/normalizer.php                   │
│  │        ├─ require: utils/schema_validator.php             │
│  │        └─ require: utils/config.php                       │
│  │                                                             │
│  ├─ Enviar al MH (línea 162-210)                             │
│  │  └─ CURL POST a https://apitest.dtes.mh.gob.sv/...      │
│  │                                                             │
│  ├─ Procesar respuesta (línea 212-250)                       │
│  │  ├─ Si ACEPTADO:                                          │
│  │  │  ├─ require: utils/qr_maker.php → generar_qr_mh()    │
│  │  │  ├─ require: utils/pdf_generator.php → generar_pdf()  │
│  │  │  ├─ require: utils/ticket_printer.php → ticket HTML   │
│  │  │  └─ require: utils/mail_sender.php → enviar email     │
│  │  │                                                         │
│  │  └─ Si RECHAZADO:                                         │
│  │     └─ Registrar error y retornar                         │
│  │                                                             │
│  └─ Registrar auditoría (línea 252-280)                      │
│     └─ require: utils/audit_logger.php                       │
│        ├─ audit_log()                                        │
│        ├─ audit_log_json_generated()                         │
│        ├─ audit_log_firma_local_response()                   │
│        ├─ audit_log_mh_payload()                             │
│        ├─ audit_log_mh_response()                            │
│        └─ audit_log_final_summary()                          │
│                                                               │
└─────────────────────────────────────────────────────────────┤
                                                                │
RESPUESTA AL CLIENTE                                            │
│                                                                │
└─ JSON con:                                                    │
   ├─ success: true/false                                      │
   ├─ numeroControl: "DTE-01-P001M001-000000000000001"        │
   ├─ codigoGeneracion: "550E8400-..."                         │
   ├─ qrPath: "/storage/qr/dte_..."                           │
   ├─ pdfPath: "/storage/pdf/dte_..."                         │
   ├─ estado: "ACEPTADO" | "RECHAZADO"                         │
   └─ detalles: {...}                                          │
```

---

## 📂 ESTRUCTURA DE DIRECTORIOS CLAVE

```
pos_system/
├─ views/
│  ├─ pos_sale.php                    ← JSON INICIAL (prepareDTEJson)
│  └─ ajax/
│     └─ process_sale_complete.php    ← ORQUESTADOR PRINCIPAL
│
├─ includes/
│  └─ signer/
│     ├─ signer_local.php             ← FIRMA DIGITAL
│     ├─ WORKS_1_signer_local.php     ← Respaldo
│     └─ utils/
│        ├─ dte_validator_new.php     ← VALIDACIÓN JSON
│        ├─ dte_validator_schema.php  ← VALIDACIÓN PROFUNDA
│        ├─ audit_logger.php          ← AUDITORÍA
│        ├─ normalizer.php            ← NORMALIZACIÓN
│        ├─ schema_validator.php       ← CHEQUEO vs MH
│        ├─ qr_maker.php              ← GENERADOR QR
│        ├─ pdf_generator.php         ← GENERADOR PDF
│        ├─ ticket_printer.php        ← HTML TICKET
│        ├─ mail_sender.php           ← ENVÍO EMAIL
│        ├─ pg_connection.php         ← CONEXIÓN BD
│        ├─ config.php                ← CONFIGURACIÓN
│        └─ schemas/
│           └─ fe-fc-v1.json          ← SCHEMA OFICIAL MH
│
├─ storage/
│  ├─ sigs/
│  │  └─ dte_{codigoGeneracion}.json  ← JSON ORIGINAL GUARDADO
│  ├─ qr/
│  │  └─ dte_{codigoGeneracion}.png   ← QR GENERADO
│  ├─ pdf/
│  │  └─ dte_{codigoGeneracion}.pdf   ← PDF FACTURA
│  └─ logs/
│     ├─ signer_local_debug.log       ← LOGS FIRMA
│     ├─ audit.log                    ← LOGS AUDITORÍA
│     └─ error.log                    ← LOGS ERRORES
│
└─ config/
   ├─ constants.php                   ← CONSTANTES
   ├─ database.php                    ← CONFIG BD
   └─ supabase_config.php             ← CONFIG SUPABASE
```

---

## 🔄 RESUMEN: "JSON INICIAL" → FACTURA FINAL

```
1. CLIENTE (POS)
   └─ prepareDTEJson() en pos_sale.php
      └─ Genera JSON inicial con TODOS los datos
      
2. SERVIDOR RECIBE
   └─ process_sale_complete.php
      └─ Valida, genera numeroControl, firma, envía al MH
      
3. MINISTERIO RESPONDE
   └─ ACEPTADO o RECHAZADO
   
4. SI ACEPTADO
   ├─ Generar QR (qr_maker.php)
   ├─ Generar PDF (pdf_generator.php)
   ├─ Generar Ticket (ticket_printer.php)
   └─ Enviar email (mail_sender.php)
   
5. REGISTRAR TODO
   └─ Auditoría completa (audit_logger.php)
   └─ Guardar en BD
```

---

## ⚠️ PUNTOS CRÍTICOS

### 1. **codigoGeneracion** ← Se genera en pos_sale.php (servidor)
   - Función: `generarCodigoGeneracion()` línea 521
   - Formato: UUID v4 (36 caracteres)
   - Se envía al cliente en `mhControl` object línea 543

### 2. **numeroControl** ← Se genera en process_sale_complete.php (servidor)
   - NO se envía desde cliente (viene como null)
   - Se genera en servidor con lock SQL para evitar duplicados
   - Formato: DTE-01-P001M001-000000000000001

### 3. **JSON Inicial** ← Se genera en prepareDTEJson() (cliente)
   - Función: `prepareDTEJson(metodoPago, paymentDetails)` línea 1289
   - Incluye codigoGeneracion (del servidor)
   - Incluye numeroControl: null (se llena en servidor)
   - Incluye todos los items, totales, IVA calculado

### 4. **Validación** ← Ocurre en servidor (antes de firma)
   - Archivo: `dte_validator_new.php`
   - Chequea format UUID v4 para codigoGeneracion
   - Chequea que numeroControl no sea null
   - Chequea formato de todos los campos

### 5. **Firma Digital** ← En signer_local.php
   - Usa PRIVATE_KEY del archivo config
   - Algoritmo: SHA256 con RSA
   - Retorna firma en base64

---

**Este es el flujo COMPLETO de una factura desde que se crea hasta que se acepta en el MH.** ✅
