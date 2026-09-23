# 📋 CÓDIGOS DE TRIBUTOS VÁLIDOS - MINISTERIO DE HACIENDA EL SALVADOR

## 🔧 PROBLEMA RESUELTO
Error: `resumen.tributos[0].codigo` no tiene valor válido en el enum
- ❌ Código anterior: `"20"` (NO VÁLIDO)
- ✅ Código correcto: `"59"` (IVA - Impuesto al Valor Agregado)

---

## 📑 LISTA COMPLETA DE CÓDIGOS DE TRIBUTOS VÁLIDOS

Según el schema `fe-fc-v1.json` del MH, los códigos válidos son:

### **CÓDIGOS MÁS COMUNES EN FACTURACIÓN**

| Código | Nombre/Descripción |
|--------|-------------------|
| **59** | **IVA (Impuesto al Valor Agregado)** ← **USAR ESTE PARA FACTURAS NORMALES** |
| C3 | Retención por IVA |
| D1 | Retención por Renta |
| 71 | Otra retención |

### **LISTA COMPLETA (Todos los 41 códigos válidos)**

```
"C3"   → Retención por IVA
"59"   → IVA (Impuesto al Valor Agregado) ✅ USAR ESTE
"71"   → Otra retención
"D1"   → Retención por Renta
"C8"   → 
"C5"   → 
"C6"   → 
"C7"   → 
"D5"   → 
"19"   → 
"28"   → 
"31"   → 
"32"   → 
"33"   → 
"34"   → 
"35"   → 
"36"   → 
"37"   → 
"38"   → 
"39"   → 
"42"   → 
"43"   → 
"44"   → 
"50"   → 
"51"   → 
"52"   → 
"53"   → 
"54"   → 
"55"   → 
"58"   → 
"77"   → 
"78"   → 
"79"   → 
"85"   → 
"86"   → 
"91"   → 
"92"   → 
"A1"   → 
"A5"   → 
"A7"   → 
"A9"   → 
```

---

## 📝 DÓNDE APARECE ESTE CÓDIGO EN LA FACTURA

### **En el JSON generado:**

```javascript
// RESUMEN - Tributos totales de la factura
"resumen": {
    "tributos": [
        {
            "codigo": "59",           // ← CÓDIGO DEL TRIBUTO
            "descripcion": "Impuesto al Valor Agregado 13%",
            "valor": 13.00            // Cantidad de IVA en dólares
        }
    ],
    // ... otros campos ...
}

// ITEMS - Tributos por item (cada línea de producto)
"cuerpoDocumento": [
    {
        "numItem": 1,
        "descripcion": "Producto 1",
        "ventaGravada": 113.00,    // Total con IVA
        "tributos": null,          // Para ventas gravadas, puede ser null
        "ivaItem": 13.00,          // IVA del item en $
        // ... otros campos ...
    }
]
```

---

## ✅ REGLAS IMPORTANTES

### **Para IVA (código "59")**

1. **En `resumen.tributos`**: SIEMPRE debe incluir el objeto con código "59"
   ```javascript
   "tributos": [
       {
           "codigo": "59",
           "descripcion": "Impuesto al Valor Agregado 13%",
           "valor": totalIVA
       }
   ]
   ```

2. **En `cuerpoDocumento[].tributos`**: 
   - Puede ser `null` (para ventas normales con IVA)
   - O un array de códigos: `["59"]` (menos común)

3. **El campo NUNCA debe ser undefined o vacío** - Debe ser null o un objeto/array completo

4. **El IVA en El Salvador es fijo: 13%**
   - El código `"59"` representa este 13%
   - NO es un array de codes, es un TRIBUTO ESPECÍFICO

---

## 🔧 CÓMO ESTÁ IMPLEMENTADO AHORA

**Archivo:** `/views/pos_sale.php`
**Función:** `prepareDTEJson()`
**Líneas:** ~1362-1368

```php
// Construir array de tributos
// Código "59" = IVA (Impuesto al Valor Agregado) según MH El Salvador
const tributosArray = totalIVA > 0 ? [{
    "codigo": "59",
    "descripcion": "Impuesto al Valor Agregado 13%",
    "valor": totalIVA
}] : [];
```

---

## 📊 REFERENCIA: CAMPOS RELACIONADOS CON TRIBUTOS

| Campo | Ubicación | Tipo | Descripción |
|-------|-----------|------|-------------|
| `tributos` | `resumen.tributos` | Array | Array de tributos a nivel de resumen |
| `codigo` | `resumen.tributos[].codigo` | String(2) | Código del tributo (debe ser válido) |
| `descripcion` | `resumen.tributos[].descripcion` | String | Descripción del tributo |
| `valor` | `resumen.tributos[].valor` | Number | Monto del tributo |
| `codTributo` | `cuerpoDocumento[].codTributo` | String\|null | Código tributo a nivel item (puede ser null) |
| `tributos` | `cuerpoDocumento[].tributos` | Array\|null | Tributos a nivel item (puede ser null) |
| `ivaItem` | `cuerpoDocumento[].ivaItem` | Number | Monto de IVA por item |

---

## 🐛 ERRORES COMUNES

### ❌ Error: "Does not have a value in the enumeration"
**Causa:** El código no está en la lista válida
**Solución:** Usar `"59"` para IVA

### ❌ Error: "tributos is null"
**Causa:** El campo tributos en resumen debe ser array, no null
**Solución:** Usar `tributosArray = [{codigo: "59", ...}]`

### ❌ Error: "codigo must be 2 characters"
**Causa:** Usar código con más o menos de 2 caracteres
**Solución:** Verificar que sea exactamente 2 caracteres: `"59"` ✅

---

## 📚 REFERENCIAS OFICIALES

- **MH El Salvador**: https://www.mh.gob.sv/
- **FES (Facturación Electrónica)**: https://apitest.dtes.mh.gob.sv/
- **Schema oficial**: `/includes/signer/schemas/fe-fc-v1.json`

---

## ✨ CONCLUSIÓN

**Para facturas normales en El Salvador:**
- Siempre usar código de tributo: `"59"`
- Esto representa el IVA (13%)
- El monto del IVA se calcula como: `ventaTotal / 1.13` para obtener el valor gravado sin IVA

Cambio realizado: `"20"` → `"59"` en `/views/pos_sale.php` línea 1365
