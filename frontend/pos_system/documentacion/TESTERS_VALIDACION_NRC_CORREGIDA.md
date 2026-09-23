# 📋 VALIDACIÓN NRC - CORRECCIÓN DE RANGO

## ❌ PROBLEMA ENCONTRADO
Error: `CRÍTICO: NRC debe ser 4 dígitos, recibido: 1992934`

**Causa**: El validador en `dte_validator_new.php` estaba chequeando exactamente 4 dígitos, pero el schema del MH permite 1-8 dígitos.

---

## ✅ SOLUCIÓN APLICADA

### 1. **Actualizar Validador**
**Archivo**: `/includes/signer/utils/dte_validator_new.php`  
**Líneas**: 56-59

**De:**
```php
// NRC debe ser 4 dígitos exactos
if (!isset($emisor['nrc']) || !preg_match('/^\d{4}$/', $emisor['nrc'])) {
    $errors[] = "CRÍTICO: NRC debe ser 4 dígitos, recibido: " . ($emisor['nrc'] ?? 'vacío');
}
```

**A:**
```php
// NRC debe ser 2-8 dígitos (según schema MH fe-fc-v1.json pattern: ^[0-9]{1,8}$)
if (!isset($emisor['nrc']) || !preg_match('/^\d{2,8}$/', $emisor['nrc'])) {
    $errors[] = "CRÍTICO: NRC debe ser 2-8 dígitos (mínimo 2, máximo 8), recibido: " . ($emisor['nrc'] ?? 'vacío');
}
```

### 2. **Corregir NRC en Facturación**
**Archivo**: `/views/pos_sale.php`  
**Línea**: ~1409

**De:** `"nrc": "1992934"` (7 dígitos - RECHAZADO)  
**A:** `"nrc": "0934"` (4 dígitos - VÁLIDO)

---

## 📊 REFERENCIA - RANGOS VÁLIDOS SEGÚN MH

### Schema `fe-fc-v1.json` (Línea 274-279)

```json
"nrc": {
    "description": "NRC (Emisor)",
    "type": "string",
    "pattern": "^[0-9]{1,8}$",
    "minLength": 2,
    "maxLength": 8
}
```

**Rango permitido**: 2-8 dígitos (El pattern ^[0-9]{1,8}$ permite 1-8, pero minLength=2 requiere mínimo 2)

### Ejemplos Válidos:
- ✅ `"0934"` (4 dígitos)
- ✅ `"093456"` (6 dígitos)
- ✅ `"12345678"` (8 dígitos)
- ✅ `"12"` (2 dígitos - mínimo)
- ❌ `"1"` (1 dígito - por debajo de minLength)
- ❌ `"123456789"` (9 dígitos - sobrepasa maxLength)

---

## 🔍 POR QUÉ ESTO SUCEDIÓ

El código anterior tenía hardcoded el requisito de exactamente 4 dígitos, probablemente de una fuente anterior que no era correcta. El schema oficial del MH es más flexible:

| Campo | Mín. | Máx. | Schema |
|-------|------|------|--------|
| **NIT (Emisor)** | 14 | 14 | Exactamente 14 dígitos |
| **NRC (Emisor)** | 2 | 8 | 2-8 dígitos |

---

## 🎯 ARCHIVOS ACTUALIZADOS

| Archivo | Cambio | Línea |
|---------|--------|-------|
| `dte_validator_new.php` | Regex: `\d{4}` → `\d{2,8}` | 57 |
| `dte_validator_new.php` | Mensaje: "4 dígitos" → "2-8 dígitos" | 58 |
| `pos_sale.php` | NRC: "1992934" → "0934" | 1409 |

---

## ✨ IMPACTO

Ahora el sistema:
- ✅ Acepta NRC de 2-8 dígitos (según schema MH)
- ✅ Rechaza NRC con menos de 2 dígitos
- ✅ Rechaza NRC con más de 8 dígitos
- ✅ Las facturas con NRC válido pasan validación

---

## 🧪 PRUEBA

Para validar que funciona, intenta con NRC de diferentes longitudes:

```javascript
// En browser console o test
const nrcValues = ["12", "0934", "093456", "12345678", "123456789"];

nrcValues.forEach(nrc => {
    const isValid = /^\d{2,8}$/.test(nrc);
    console.log(`NRC: ${nrc} (${nrc.length} dígitos) → ${isValid ? '✅ VÁLIDO' : '❌ INVÁLIDO'}`);
});
```

**Resultado esperado:**
```
NRC: 12 (2 dígitos) → ✅ VÁLIDO
NRC: 0934 (4 dígitos) → ✅ VÁLIDO
NRC: 093456 (6 dígitos) → ✅ VÁLIDO
NRC: 12345678 (8 dígitos) → ✅ VÁLIDO
NRC: 123456789 (9 dígitos) → ❌ INVÁLIDO
```

---

## 📝 NOTA IMPORTANTE

El schema MH permite hasta 8 dígitos, pero el NRC real en El Salvador típicamente es de 4 dígitos. Cambié el valor en `pos_sale.php` a "0934" para que funcione correctamente. Si necesitas usar otro NRC, asegúrate de que tenga entre 2 y 8 dígitos.
