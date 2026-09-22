# Guía Rápida de Prueba - Productos POS

## 🚀 Inicio Rápido

### 1. Abrir la Página de Productos
```
http://localhost/NebulaDET_DEV_FREE_MINI/posys/pos_system/views/pos_products.php
```

### 2. Abrir Consola del Navegador
Presiona **F12** y ve a la pestaña **Console**

### 3. Crear un Producto de Prueba

1. Click en **"Agregar Producto"**
2. Llenar el formulario:
   - **Código**: `TEST-001`
   - **Nombre**: `Producto de Prueba`
   - **Precio**: `10.50`
   - **Stock**: `100`
3. Click **"Guardar Producto"**

### ✅ Resultados Esperados

**En la Consola:**
```
🔧 POS Products JS Initialized
📡 API URL: ../api/v1/products.php
➕ Opening Add Product Modal
💾 Submitting Product Form { method: "POST", ... }
✅ Success Response: { success: true, ... }
```

**En la Pantalla:**
- ✅ Aparece notificación SweetAlert2 verde: "¡Éxito!"
- ✅ El modal se cierra automáticamente
- ✅ La página se recarga
- ✅ El nuevo producto aparece en la tabla

---

## 🔍 Verificar en Base de Datos

**Supabase Dashboard:**
1. Ir a: https://supabase.com/dashboard
2. Seleccionar proyecto
3. Table Editor → `dte_productos`
4. Buscar producto con código `TEST-001`

---

## 📋 Revisar Logs

**Logs de API:**
```powershell
Get-Content c:/wamp64/www/NebulaDET_DEV_FREE_MINI/posys/pos_system/logs/api_errors.log -Tail 20
```

**Logs de PHP:**
```powershell
Get-Content c:/wamp64/logs/php_error.log -Tail 20
```

---

## ❌ Si Algo Falla

### No aparece notificación SweetAlert2
1. Verificar en consola: `console.log(typeof Swal)`
   - Debe mostrar: `"function"`
2. Limpiar caché del navegador (Ctrl+Shift+Del)

### Producto no se guarda
1. Revisar consola para errores AJAX
2. Revisar `logs/api_errors.log`
3. Verificar conexión Supabase en `.env`

### Error de CORS
1. Verificar que `api/v1/products.php` tiene headers CORS
2. Limpiar caché del navegador

---

## 🧪 Script de Diagnóstico

Si los problemas persisten, ejecutar:
```
http://localhost/NebulaDET_DEV_FREE_MINI/posys/pos_system/test_product_api.php
```

Este script prueba:
- ✅ Archivos requeridos
- ✅ Carga de ProductService
- ✅ Obtener productos (GET)
- ✅ Crear producto (POST)
- ✅ Configuración Supabase

---

## 📞 Soporte

Para más detalles, consultar:
- [walkthrough.md](file:///C:/Users/X541N/.gemini/antigravity/brain/263f5b59-690f-4524-b139-a7e86ba7b97c/walkthrough.md) - Documentación completa
- [implementation_plan.md](file:///C:/Users/X541N/.gemini/antigravity/brain/263f5b59-690f-4524-b139-a7e86ba7b97c/implementation_plan.md) - Plan de implementación
