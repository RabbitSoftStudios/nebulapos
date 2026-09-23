# GUÍA RÁPIDA - CORRECCIÓN ERROR 094

## ¿QUÉ SE CAMBIÓ?

El sistema ahora entiende correctamente que **los precios YA INCLUYEN IVA** (no es adicional).

## ANTES ❌
```
Cliente paga: $16
Sistema calculaba: $16 sin IVA + $2.08 IVA = $18.08 ❌
```

## AHORA ✅
```
Cliente paga: $16 (INCLUYE IVA)
Sistema calcula:
  - Valor gravado: $16 / 1.13 = $14.16
  - IVA: $16 - $14.16 = $1.84
  - Total: $14.16 + $1.84 = $16.00 ✓
```

---

## ARCHIVOS MODIFICADOS

### 1. `/views/pos_sale.php`
- Función `prepareDTEJson()`
- Ahora calcula IVA correctamente
- En "resumen": `totalPagar = totalGravada` (ambos con IVA)

### 2. `/includes/signer/utils/dte_validator_new.php`
- NUEVO archivo
- Valida que los cálculos sean correctos
- Detecta si hay inconsistencias en IVA

### 3. `/views/ajax/process_sale_complete.php`
- Integración del nuevo validador
- Antes de firmar, valida el JSON

---

## VALIDACIÓN

El nuevo validador comprueba:

✓ `numeroControl` está presente: `DTE-XX-YYYYYYYY-ZZZZZZZZZZZZZZZ`  
✓ `codigoGeneracion` tiene 8 caracteres  
✓ Por cada item: `ivaItem = ventaGravada - (ventaGravada / 1.13)`  
✓ `totalPagar = totalGravada` (sin agregar IVA extra)  
✓ Array de tributos incluye código "20" (IVA)  

---

## TESTING

Ejecuta este archivo para verificar los cálculos:
```
http://localhost/NebulaDET_DEV_FREE_MINI/posys/pos_system/__TESTERS/TEST_IVA_INCLUIDO.php
```

---

## SI AÚN HAY ERROR 094

Revisa los logs:
```
/includes/signer/utils/logs/audit.log
```

El log mostrará:
1. JSON_GENERATED - El JSON que se envió
2. FIRMA_LOCAL_RESPONSE - Respuesta del firmador local
3. MH_PAYLOAD_SEND - El payload enviado al gobierno
4. MH_RESPONSE_COMPLETE - Respuesta del MH (incluye error 094 si hay)

---

## PRÓXIMAS ACCIONES

Si el error 094 persiste:

1. **Verificar numeroControl**:
   - ¿Está en formato correcto?
   - ¿Es único?
   - ¿Tiene 31 caracteres totales?

2. **Verificar codigoGeneracion**:
   - ¿Es de 8 caracteres?
   - ¿Tiene caracteres no alfanuméricos?

3. **Revisar los logs de audit**:
   - Buscar "VALIDATION PASSED" o "VALIDATION FAILED"
   - Si falla, mostrará exactamente qué está mal

4. **Contactar al MH**:
   - Si validación local pasa pero MH rechaza
   - Existe un problema de comunicación o especificación

---

## CONTACTO

Si necesitas cambios adicionales o debugging:
- Revisar: `/CORRECCION_IVA_INCLUIDO.md` (guía técnica)
- Ejecutar: `/TEST_IVA_INCLUIDO.php` (verificar cálculos)
- Logs: `/includes/signer/utils/logs/audit.log` (diagnóstico)

