# Integración de Pagos con Wompi - Guía Rápida

## ¿Qué se desarrolló?

Se implementó un sistema completo de pagos con tarjeta de crédito/débito usando **Wompi** como procesador de pagos. La arquitectura está dividida en:

### 1. **Frontend (JavaScript)**
- **Archivo**: `views/ajax/wompi.js`
- **Responsabilidad**: Orquestar el flujo de pago
- **Características**:
  - Módulo encapsulado `WompiPayment`
  - Manejo de popup para formulario de tarjeta
  - Verificación automática de estado del pago
  - Manejo de errores y reintentos

### 2. **Backend (PHP)**
- **Archivo**: `views/ajax/wompi_payment.php`
- **Responsabilidad**: Comunicación segura con API de Wompi
- **Endpoints**:
  - `?action=get-config` → Obtener configuración
  - `?action=create-payment` → Crear enlace de pago
  - `?action=verify-payment` → Verificar estado
  - `?action=webhook` → Recibir notificaciones

### 3. **Configuración Centralizada**
- **Archivo**: `config/wompi.php`
- **Responsabilidad**: Cargar y validar credenciales desde `.env`
- **Ventajas**: Facilita debugging y cambios de ambiente

### 4. **Interfaz Principal**
- **Archivo**: `views/pos_sale.php`
- **Función actualizada**: `showCardPaymentModal(total)`
- **Integración**: 
  - Carga `wompi.js`
  - Usa `WompiPayment.processPayment()`
  - Maneja respuesta de pago

---

## Flujo de Operación

```
1. Usuario hace clic en botón "Tarjeta" en POS
                    ↓
2. showCardPaymentModal(total) se ejecuta
                    ↓
3. Muestra confirmación inicial
                    ↓
4. Usuario confirma → WompiPayment.processPayment()
                    ↓
5. wompi.js llama a wompi_payment.php?action=create-payment
                    ↓
6. Backend obtiene token de Wompi y crea enlace de pago
                    ↓
7. Se abre popup con formulario de tarjeta de Wompi
                    ↓
8. Usuario ingresa datos de tarjeta (SEGURO en dominio de Wompi)
                    ↓
9. Usuario completa/cancela pago
                    ↓
10. wompi.js verifica estado: wompi_payment.php?action=verify-payment
                    ↓
11. Si éxito: processSale('card', {reference, method, status})
    Si error: Mostrar error y permitir reintentar
```

---

## Instalación y Verificación

### ✅ Paso 1: Verificar variables de entorno
```bash
# Verificar que .env contiene:
WOMPI_CLIENT_ID = "71e287e0-d36a-469c-b1e7-ab878326b10f"
WOMPI_CLIENT_SECRET = "86fbd539-48a2-4f35-8eb0-8f2120166309"
```

### ✅ Paso 2: Archivos creados/actualizados
- ✓ `views/ajax/wompi.js` - Módulo de pagos (reescrito)
- ✓ `views/ajax/wompi_payment.php` - Endpoint del servidor
- ✓ `config/wompi.php` - Configuración centralizada
- ✓ `views/pos_sale.php` - Función `showCardPaymentModal()` actualizada
- ✓ `WOMPI_INTEGRATION.php` - Documentación técnica

### ✅ Paso 3: Prueba en navegador
```javascript
// Abrir consola (F12) y verificar:
console.log(WompiPayment); // Debe mostrar objeto con métodos
// Output: Object { processPayment, init, getCurrentPayment, reset }
```

---

## Testing Manual

### Escenario 1: Pago Exitoso
1. Navegar a `/views/pos_sale.php`
2. Agregar un producto al carrito
3. Hacer clic en "Tarjeta"
4. Confirmar pago
5. En popup de Wompi, usar datos de prueba:
   - **Tarjeta**: 4111 1111 1111 1111
   - **Expiración**: 12/25
   - **CVV**: 123
6. Completar pago
7. Verificar que la venta se procesa correctamente

### Escenario 2: Error de Conexión
1. Desconectar internet
2. Intentar pagar
3. Debe mostrar error: "Error en el Pago"
4. Opción para reintentar

### Escenario 3: Usuario Cancela
1. Hacer clic en "Tarjeta"
2. En popup, cerrar la ventana sin completar
3. Debe mostrar: "Pago fue cancelado"

---

## Debugging

### Verificar que wompi.js se carga
```javascript
// En consola del navegador
fetch('ajax/wompi_payment.php?action=get-config')
  .then(r => r.json())
  .then(d => console.log(d))
// Debe mostrar: {success: true, clientId: "...", ...}
```

### Ver logs de servidor
```bash
# En Windows (WAMP):
tail -f storage/logs/error.log
```

### Simular request de pago
```bash
curl -X POST http://localhost:8000/views/ajax/wompi_payment.php?action=create-payment \
  -H "Content-Type: application/json" \
  -d '{"amount":100,"email":"test@example.com","description":"Prueba"}'
```

---

## Seguridad Implementada

✅ **Credenciales en servidor** - WOMPI_CLIENT_ID/SECRET en `.env`, NUNCA en JS
✅ **Validación de entrada** - Todos los datos validados en servidor
✅ **HTTPS obligatorio** - Para pagos en producción
✅ **Verificación de firma** - En webhook de Wompi
✅ **Sanitización** - Todas las entradas sanitizadas
✅ **Manejo de errores** - Try-catch en JS y PHP

---

## Próximas Mejoras

1. **Persistencia de pagos**
   - Crear tabla: `pos_payments`
   - Guardar referencia y estado de cada pago

2. **Procesar webhooks**
   - Implementar verificación completa de webhook
   - Actualizar estado de venta automáticamente

3. **Recuperación de pagos incompletos**
   - Guardar estado provisional
   - Permitir reintentar mismo pago

4. **Otros métodos de pago**
   - PayPal, Stripe, Transferencia bancaria

---

## Archivos Importantes

| Archivo | Responsabilidad |
|---------|-----------------|
| `views/pos_sale.php` | Interfaz principal del POS |
| `views/ajax/wompi.js` | Módulo de pagos (cliente) |
| `views/ajax/wompi_payment.php` | Backend de pagos |
| `config/wompi.php` | Configuración centralizada |
| `.env` | Variables de entorno |

---

## Contacto y Soporte

Para problemas o mejoras, revisar:
1. Logs: `storage/logs/error.log`
2. Consola navegador: F12 → Console
3. Red: F12 → Network (verificar requests a wompi_payment.php)

---

**Última actualización**: 31 de diciembre de 2025  
**Versión**: 1.0  
**Estado**: ✅ Listo para pruebas
