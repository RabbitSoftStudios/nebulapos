# ⚡ REFERENCIA RÁPIDA - Error 094

## 🚨 Tienes Error 094?

### En 30 segundos:

```bash
# 1. Obtén el codigoGeneracion del error
codigo="B2C91C1A-1B25-F87B-D038-8C9B0869B8E1"

# 2. Abre los logs
grep -A 200 "$codigo" includes/signer/audit_dte_complete.log

# 3. Busca estas secciones (en orden):
#    - JSON_GENERATED (¿está bien formado?)
#    - FIRMA_LOCAL_RESPONSE (¿firmó?)
#    - MH_RESPONSE_COMPLETE (¿qué dijo MH?)

# 4. Lee LEER_LOGS.md para entender cada sección
cat LEER_LOGS.md
```

---

## 📋 Checklist de 30 segundos

```
□ numeroControl = "DTE-01-P001M001-[15 dígitos]"
□ fecEmi = "YYYY-MM-DD" (no DD/MM/YYYY)
□ horEmi = "HH:mm:ss" (con segundos)
□ nit (emisor) = exactamente 14 dígitos
□ codActividad = 5 dígitos
□ nombre (receptor) = no vacío
□ ventaGravada = cantidad × precio - descuento
□ totalPagar = totalGravada + (totalGravada × 13%)
```

Si alguno falla → ESE es el problema

---

## 🎯 3 Pasos para Resolver

### 1. Diagnosticar
```php
require 'includes/signer/utils/dte_validator.php';
$val = validar_json_dte_completo($dte);
print_validation_report($val);
```

### 2. Corregir
En `views/pos_sale.php`, función `prepareDTEJson()`:
```php
// Busca el campo que falla en el reporte
// Corrige su formato o cálculo
```

### 3. Reintentar
Hacer nueva venta → Los logs mostrarán si está bien

---

## 📂 Archivos Importantes

| Archivo | Cuándo leerlo |
|---------|---------------|
| `audit_dte_complete.log` | Cuando hay error |
| `LEER_LOGS.md` | Para entender el log |
| `DIAGNOSTICO_ERROR_094.md` | Para diagnóstico completo |
| `dte_validator.php` | Para validar localmente |
| `test_audit_system.php` | Para verificar sistema |

---

## 🔍 Errores Comunes & Solución

### Error: numeroControl vacío
```
❌ Síntoma: "numeroControl": ""
✅ Solución: Verificar que se genere correctamente en process_sale_complete.php
```

### Error: Fecha mal formateada
```
❌ Síntoma: "fecEmi": "20/01/2026"
✅ Solución: Cambiar a formato YYYY-MM-DD
```

### Error: NIT incorrecto
```
❌ Síntoma: "nit": "061509118510" (12 dígitos)
✅ Solución: Debe tener exactamente 14 dígitos
```

### Error: Cálculo de ventaGravada
```
❌ Síntoma: cantidad=2, precio=100, ventaGravada=150
✅ Solución: Debe ser 2 × 100 = 200
```

### Error: Código de actividad inválido
```
❌ Síntoma: "codActividad": "XXXXX"
✅ Solución: Verificar catálogo de MH
```

---

## 💬 Mensajes de MH & Significado

| Código | Mensaje | Significa |
|--------|---------|-----------|
| 000 | PROCESADO | ✅ Aceptada |
| 094 | PARAMETROS NO VALIDOS | ❌ Falta o está mal un campo |
| 017 | NIT NO AUTORIZADO | ❌ NIT no registrado en MH |
| 023 | CÓDIGO ACTIVIDAD INVÁLIDO | ❌ Código no existe |
| 030 | AMBIENTE INCORRECTO | ❌ Ambiente test vs prod |

---

## 🧪 Test Rápido

```bash
# Verificar que el sistema está funcionando
php test_audit_system.php

# Resultado:
# ✅ TODOS LOS SISTEMAS OPERACIONALES
```

---

## 📞 Flujo de Soporte

1. **¿Tienes error 094?**
   - Abre `audit_dte_complete.log`
   - Busca tu `codigoGeneracion`
   - Lee sección `MH_RESPONSE_COMPLETE`

2. **¿No entiendes los logs?**
   - Lee `LEER_LOGS.md`
   - Ve a sección del error
   - Busca tu caso

3. **¿Aún no resuelves?**
   - Corre `php test_audit_system.php`
   - Compara con `DIAGNOSTICO_ERROR_094.md`
   - Revisa checklist

4. **¿Quieres validar un JSON?**
   ```php
   require 'includes/signer/utils/dte_validator.php';
   $result = validar_json_dte_completo($dte);
   print_validation_report($result);
   ```

---

## 🎯 Lo Más Importante

> **El JSON debe coincidir EXACTAMENTE con el formato de MH**

Compara con el ejemplo Python en producción:
```python
dte_json = {
    "identificacion": {
        "version": 1,
        "ambiente": "00",
        "tipoDte": "01",
        "numeroControl": "DTE-01-S001P001-000000000000001",
        # ... resto igual
    }
}
```

Si el tuyo es diferente → AHÍ está el error

---

## ✅ Verificación Final

```bash
# Asegúrate de que tienes:
✅ /includes/signer/utils/audit_logger.php
✅ /includes/signer/utils/dte_validator.php
✅ /includes/signer/audit_dte_complete.log (al hacer venta)
✅ test_audit_system.php ejecutándose sin errores
✅ DIAGNOSTICO_ERROR_094.md disponible
✅ LEER_LOGS.md disponible

# Si todo ✅ → Sistema listo
```

---

## 🚀 Próxima Venta

Cuando hagas la próxima venta:
1. Los logs se crearán en `audit_dte_complete.log`
2. Si falla → Revisar sección `MH_RESPONSE_COMPLETE`
3. Si tiene error 094 → Usar checklist arriba
4. Corregir → Reintentar

---

**Última actualización:** 2026-01-20  
**TL;DR:** Abre `audit_dte_complete.log`, busca tu `codigoGeneracion`, revisa `MH_RESPONSE_COMPLETE`, compara con checklist
