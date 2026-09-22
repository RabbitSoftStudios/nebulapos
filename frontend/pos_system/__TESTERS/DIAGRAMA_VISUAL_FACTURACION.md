# 📊 DIAGRAMA VISUAL - FLUJO DE FACTURACIÓN

## 🎯 RESPUESTA RÁPIDA

### ¿QUÉ ARCHIVO GENERA EL JSON DE FIRMA LOCAL?

```
╔════════════════════════════════════════════════════════════════════════╗
║                  /views/pos_sale.php                                  ║
║                                                                        ║
║  ┌──────────────────────────────────────────────────────────────┐   ║
║  │ Función: prepareDTEJson(metodoPago, paymentDetails)          │   ║
║  │ Líneas: ~1289-1450                                            │   ║
║  │                                                                │   ║
║  │ GENERA:                                                        │   ║
║  │  • identificacion { version, ambiente, tipoDte, ... }         │   ║
║  │  • emisor { nit, nrc, nombre, ... }                           │   ║
║  │  • receptor { nombre, nrc, ... }                              │   ║
║  │  • cuerpoDocumento [] { items de compra }                     │   ║
║  │  • resumen { totales, IVA, tributos, pagos }                  │   ║
║  │                                                                │   ║
║  │ RESULTADO: JSON Completo para Factura                        │   ║
║  └──────────────────────────────────────────────────────────────┘   ║
║                                                                        ║
║  ↓ (Se envía al servidor en process_sale_complete.php)              ║
╚════════════════════════════════════════════════════════════════════════╝
```

---

## 🔄 FLUJO PASO A PASO (5 FASES)

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║                          FASE 1: GENERACIÓN JSON                              ║
╚═══════════════════════════════════════════════════════════════════════════════╝

    USUARIO EN POS
         │
         ├─ Escanea/agrega productos
         │
         ├─ Completa dato de cliente
         │
         └─ Click en "FINALIZAR VENTA"
                │
                └──> prepareDTEJson() en pos_sale.php
                     │
                     └─> Genera JSON:
                         ✓ codigoGeneracion (UUID v4 del servidor)
                         ✓ numeroControl: null (se genera en servidor)
                         ✓ Items con precios CON IVA incluido
                         ✓ Cálculo de IVA: ventaGravada = precio / 1.13
                         ✓ Todos los totales


╔═══════════════════════════════════════════════════════════════════════════════╗
║                    FASE 2: PROCESAMIENTO EN SERVIDOR                          ║
╚═══════════════════════════════════════════════════════════════════════════════╝

    JSON LLEGA AL SERVIDOR (process_sale_complete.php)
         │
         ├─ 1. Recibir JSON
         │      └─ json_decode($_POST data)
         │
         ├─ 2. GENERAR NUMERO CONTROL
         │      └─ Lock SQL + SELECT MAX() + Incremento
         │      └─ Formato: DTE-01-P001M001-000000000000001
         │      └─ ASIGNAR: $dteData['identificacion']['numeroControl']
         │
         ├─ 3. VALIDAR JSON
         │      └─ require: dte_validator_new.php
         │      └─ Chequear: formato codigoGeneracion (UUID v4)
         │      └─ Chequear: numeroControl presente y válido
         │      └─ Chequear: NRC = 4 dígitos
         │      └─ Si falla → ROLLBACK y error
         │      └─ Si pasa → continuar
         │
         ├─ 4. GUARDAR JSON ORIGINAL
         │      └─ Archivo: /storage/sigs/dte_{codigoGeneracion}.json
         │
         ├─ 5. INSERTAR EN BD
         │      └─ Tabla: dte_facturas
         │      └─ Campos: codigo_generacion, numero_control, etc
         │
         ├─ 6. ENVIAR A FIRMA LOCAL
         │      └─ require: signer_local.php
         │      └─ Función: sign_document_local($dteData)
         │      └─ Resultado: Documento FIRMADO en base64
         │
         ├─ 7. ENVIAR AL MH
         │      └─ Endpoint: https://apitest.dtes.mh.gob.sv/fesv/recepciondte
         │      └─ Método: POST
         │      └─ Body: JSON + firma + metadatos
         │
         ├─ 8. RECIBIR RESPUESTA MH
         │      ├─ Si ACEPTADO ✅
         │      │   └─ codigoRecepcion
         │      │   └─ selloRecepcion
         │      │   └─ selloGeneracion
         │      │
         │      └─ Si RECHAZADO ❌
         │          └─ codigoError (094, 095, etc)
         │          └─ descripcion del error
         │
         └─ 9. RETORNAR AL CLIENTE
                └─ JSON con: success, numeroControl, codigoGeneracion, etc


╔═══════════════════════════════════════════════════════════════════════════════╗
║                      FASE 3: FIRMA DIGITAL LOCAL                              ║
╚═══════════════════════════════════════════════════════════════════════════════╝

    signer_local.php - sign_document_local()
         │
         ├─ 1. Validar configuración
         │      ├─ NIT válido (14 dígitos)
         │      ├─ PRIVATE_KEY presente
         │      └─ DTE_JSON no vacío
         │
         ├─ 2. Normalizar DTE
         │      ├─ require: normalizer.php
         │      ├─ Eliminar espacios/caracteres inválidos
         │      └─ Ordenar campos JSON
         │
         ├─ 3. Validar contra Schema
         │      ├─ require: schema_validator.php
         │      ├─ Chequear fe-fc-v1.json (schema oficial MH)
         │      └─ Si falla → Exception
         │
         ├─ 4. Crear Cadena de Firma
         │      ├─ Extraer campos clave:
         │      │  ├─ codigoGeneracion
         │      │  ├─ numeroControl
         │      │  ├─ totalGravada
         │      │  ├─ totalIva
         │      │  └─ totalPagar
         │      └─ Concatenar: "G|N|T|..." (cadenaFirma)
         │
         ├─ 5. FIRMAR DIGITALMENTE
         │      ├─ Algoritmo: SHA256 + RSA
         │      ├─ openssl_sign(cadenaFirma, $signature, $privateKey)
         │      └─ base64_encode($signature) ← FIRMA BASE64
         │
         └─ 6. Retornar
                └─ JSON con:
                   ├─ status: "success"
                   ├─ firma: base64_firma
                   ├─ dte_firmado: {json + firma}
                   └─ hash: validación


╔═══════════════════════════════════════════════════════════════════════════════╗
║                    FASE 4: ENVÍO AL MINISTERIO                                ║
╚═══════════════════════════════════════════════════════════════════════════════╝

    POST https://apitest.dtes.mh.gob.sv/fesv/recepciondte
         │
         ├─ PAYLOAD ENVIADO:
         │   {
         │     "nitEmisor": "06150911851010",
         │     "nitReceptor": "000000000000",
         │     "codigoGeneracion": "550E8400-E29B-41D4-A716-446655440000",
         │     "dte": "{...json_firmado...}",
         │     "firma": "0ACB03D1F4B2E5...base64..."
         │   }
         │
         ├─ RESPUESTA SI ACEPTADO (✅):
         │   {
         │     "estado": "ACEPTADO",
         │     "codigoGeneracion": "550E8400-...",
         │     "numeroControl": "DTE-01-P001M001-000000000000001",
         │     "selloRecepcion": "...",
         │     "selloGeneracion": "...",
         │     "timestamp": "2024-01-20T14:30:45Z"
         │   }
         │
         └─ RESPUESTA SI RECHAZADO (❌):
             {
               "estado": "RECHAZADO",
               "codigoError": "094",
               "descripcion": "PARÁMETROS NO SON VÁLIDOS",
               "detalles": "Campo X tiene formato incorrecto"
             }


╔═══════════════════════════════════════════════════════════════════════════════╗
║              FASE 5: GENERACIÓN DE ARTEFACTOS (SI ACEPTADO)                   ║
╚═══════════════════════════════════════════════════════════════════════════════╝

    Si respuesta = ACEPTADO ✅
         │
         ├─ 1. GENERAR QR
         │      ├─ require: qr_maker.php
         │      ├─ Datos: codigoGeneracion, numeroControl, sellos MH
         │      └─ Guardar: /storage/qr/dte_{codigoGeneracion}.png
         │
         ├─ 2. GENERAR PDF
         │      ├─ require: pdf_generator.php
         │      ├─ Librería: TCPDF o Dompdf
         │      ├─ Incluir: Datos factura + QR
         │      └─ Guardar: /storage/pdf/dte_{codigoGeneracion}.pdf
         │
         ├─ 3. GENERAR TICKET
         │      ├─ require: ticket_printer.php
         │      ├─ HTML formateado para impresora
         │      └─ Incluir: Datos + QR
         │
         ├─ 4. ENVIAR EMAIL
         │      ├─ require: mail_sender.php
         │      ├─ Adjuntar: PDF factura
         │      └─ Enviar a: correo del cliente
         │
         ├─ 5. ACTUALIZAR BD
         │      ├─ Estado: ACEPTADO
         │      ├─ Fecha aceptación: NOW()
         │      ├─ Rutas de QR/PDF
         │      └─ Sellos MH
         │
         └─ 6. REGISTRAR AUDITORÍA
                ├─ audit_log()
                ├─ audit_log_json_generated()
                ├─ audit_log_firma_local_response()
                ├─ audit_log_mh_response()
                └─ audit_log_final_summary()
```

---

## 📋 TABLA DE ARCHIVOS CLAVE

| Fase | Archivo | Función | Líneas |
|------|---------|---------|--------|
| **1️⃣ JSON Inicial** | `/views/pos_sale.php` | `prepareDTEJson()` | 1289-1450 |
| **1️⃣ UUID v4** | `/views/pos_sale.php` | `generarCodigoGeneracion()` | 521-530 |
| **2️⃣ Procesamiento** | `/views/ajax/process_sale_complete.php` | Main loop | 48-280 |
| **2️⃣ Validación** | `/includes/signer/utils/dte_validator_new.php` | `validar_json_dte_nuevo()` | 14+ |
| **2️⃣ Auditoría** | `/includes/signer/utils/audit_logger.php` | Multiple functions | 22+ |
| **3️⃣ Firma** | `/includes/signer/signer_local.php` | `sign_document_local()` | 27+ |
| **3️⃣ Normalización** | `/includes/signer/utils/normalizer.php` | `normalizarDTE()` | 13+ |
| **3️⃣ Schema Validator** | `/includes/signer/utils/schema_validator.php` | `validarDTEContraSchema()` | 16+ |
| **5️⃣ QR** | `/includes/signer/utils/qr_maker.php` | `generar_qr_mh()` | 10+ |
| **5️⃣ PDF** | `/includes/signer/utils/pdf_generator.php` | `generar_factura_pdf()` | 12+ |
| **5️⃣ Email** | `/includes/signer/utils/mail_sender.php` | `enviar_factura_email()` | 13+ |

---

## 🔑 CONCEPTOS CLAVE

### JSON Inicial
- **Generado por**: `prepareDTEJson()` en pos_sale.php
- **Cuándo**: Cuando cliente hace click en "Finalizar Venta"
- **Contiene**: Todos los datos de la factura (emisor, receptor, items, totales)
- **numeroControl**: Viene como NULL (se genera en servidor)
- **codigoGeneracion**: UUID v4 del servidor

### Firma Local
- **Qué es**: Aplicar firma digital RSA a la factura
- **Dónde**: En el servidor (signer_local.php)
- **Por qué**: El MH requiere documentos firmados digitalmente
- **Resultado**: Factura firmada + cadena de firma en base64

### JSON Firmado
- **Qué es**: El JSON original + la firma agregada
- **Cómo se crea**: En signer_local.php
- **Dónde se guarda**: /storage/sigs/dte_{codigoGeneracion}.json
- **Se envía a**: MH API junto con firma en base64

---

## ⚡ PUNTOS CRÍTICOS (ERRORES COMUNES)

❌ **Error 094 puede ocurrir si**:
- codigoGeneracion no es UUID v4 (debe ser 36 caracteres)
- numeroControl no está presente o es NULL
- NRC no tiene 4 dígitos
- Campos requeridos faltan o están vacíos
- Formato JSON no coincide con schema MH

✅ **Cómo evitarlo**:
1. Verificar que prepareDTEJson() genera JSON correcto
2. Validar en process_sale_complete.php ANTES de firma
3. Usar dte_validator_new.php para chequeos automáticos
4. Revisar logs en /storage/logs/ si hay error

---

## 🎓 LECTURA RECOMENDADA

1. **Para entender DÓNDE se genera**: Este archivo (Mapa Visual)
2. **Para entender QUÉ datos tiene**: MAPA_FLUJO_FACTURACION.md
3. **Para ver ejemplos JSON**: INTEGRATION_GUIDE.php
4. **Para debugging**: Ver logs en /storage/logs/

---

**Resumen**: El JSON inicial se genera en **prepareDTEJson()** de **pos_sale.php**, luego se procesa en **process_sale_complete.php**, se firma en **signer_local.php** y se envía al MH.
