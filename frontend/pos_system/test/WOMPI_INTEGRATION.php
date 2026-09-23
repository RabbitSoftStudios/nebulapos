<?php
/**
 * GUÍA DE INTEGRACIÓN: SISTEMA DE PAGOS CON WOMPI
 * ================================================
 * 
 * ARQUITECTURA GENERAL:
 * 
 * 1. pos_sale.php (Frontend)
 *    ├─ Función: showCardPaymentModal(total)
 *    ├─ Carga: <script src="ajax/wompi.js"></script>
 *    └─ Usa: WompiPayment.processPayment()
 *
 * 2. wompi.js (Módulo JavaScript)
 *    ├─ Responsabilidad: Orquesta el flujo de pago
 *    ├─ Comunica con: wompi_payment.php (backend)
 *    ├─ Abre: Popup de Wompi para entrada de tarjeta
 *    └─ Verifica: Estado del pago tras completar
 *
 * 3. wompi_payment.php (Backend)
 *    ├─ Endpoint: /views/ajax/wompi_payment.php?action=XXX
 *    ├─ Responsabilidad: Comunicación segura con API de Wompi
 *    ├─ Acciones:
 *    │  ├─ get-config: Obtener configuración de cliente
 *    │  ├─ create-payment: Crear enlace de pago
 *    │  ├─ verify-payment: Verificar estado de pago
 *    │  └─ webhook: Notificación de Wompi
 *    └─ Seguridad: Maneja credenciales desde .env
 *
 * ==================================================
 * FLUJO DE PAGO:
 * ==================================================
 *
 * USUARIO HACE CLIC EN "TARJETA"
 *        ↓
 * showCardPaymentModal(total) se ejecuta
 *        ↓
 * WompiPayment.processPayment({amount, email, description})
 *        ↓
 * wompi.js llama a wompi_payment.php?action=create-payment
 *        ↓
 * Backend obtiene token de Wompi y crea enlace de pago
 *        ↓
 * Se abre popup con formulario de tarjeta de Wompi
 *        ↓
 * Usuario ingresa datos de tarjeta y completa pago
 *        ↓
 * Popup se cierra (automático o manual)
 *        ↓
 * wompi.js verifica estado: wompi_payment.php?action=verify-payment
 *        ↓
 * Si éxito: processSale('card', {reference, method, status})
 * Si error: Mostrar error y permitir reintentar
 *
 * ==================================================
 * VARIABLES DE ENTORNO (.env):
 * ==================================================
 *
 * WOMPI_CLIENT_ID = "71e287e0-d36a-469c-b1e7-ab878326b10f"
 * WOMPI_CLIENT_SECRET = "86fbd539-48a2-4f35-8eb0-8f2120166309"
 *
 * Estas credenciales se cargan en wompi_payment.php y NUNCA
 * se exponen al cliente JavaScript.
 *
 * ==================================================
 * ENDPOINTS DISPONIBLES:
 * ==================================================
 *
 * 1. GET /views/ajax/wompi_payment.php?action=get-config
 *    - Respuesta: {success, clientId, apiUrl, tokenUrl}
 *    - Propósito: Obtener configuración de cliente
 *
 * 2. POST /views/ajax/wompi_payment.php?action=create-payment
 *    - Payload: {amount, email, description, reference}
 *    - Respuesta: {success, paymentLink, reference, amount}
 *    - Propósito: Crear enlace de pago en Wompi
 *
 * 3. POST /views/ajax/wompi_payment.php?action=verify-payment
 *    - Payload: {reference}
 *    - Respuesta: {success, status, amount, transactionId}
 *    - Propósito: Verificar si pago fue completado
 *
 * 4. POST /views/ajax/wompi_payment.php?action=webhook
 *    - Headers: X-Wompi-Signature
 *    - Payload: Evento JSON de Wompi
 *    - Propósito: Recibir notificaciones de Wompi
 *
 * ==================================================
 * INSTALACIÓN Y CONFIGURACIÓN:
 * ==================================================
 *
 * PASO 1: Verificar variables de entorno
 *    - Abrir archivo .env
 *    - Confirmar WOMPI_CLIENT_ID y WOMPI_CLIENT_SECRET
 *
 * PASO 2: Crear archivo wompi_payment.php
 *    ✓ Ya creado en: /views/ajax/wompi_payment.php
 *
 * PASO 3: Actualizar wompi.js
 *    ✓ Ya actualizado con módulo JavaScript moderno
 *
 * PASO 4: Actualizar pos_sale.php
 *    ✓ Agregar <script src="ajax/wompi.js"></script>
 *    ✓ Reemplazar función showCardPaymentModal()
 *
 * ==================================================
 * TESTING:
 * ==================================================
 *
 * 1. Abrir consola del navegador (F12)
 *    - Verificar que WompiPayment está disponible
 *    - Verificar que no hay errores de carga de wompi.js
 *
 * 2. Hacer una transacción de prueba
 *    - Caja → Agregar producto → Click en "Tarjeta"
 *    - Verificar que se abre popup de Wompi
 *    - Usar datos de prueba de Wompi
 *
 * 3. Verificar logs
 *    - Consultar storage/logs/error.log
 *    - Verificar que wompi_payment.php está comunicándose
 *
 * ==================================================
 * MANEJO DE ERRORES:
 * ==================================================
 *
 * Error: "Credenciales no configuradas"
 *    → Verificar .env tiene WOMPI_CLIENT_ID y SECRET
 *
 * Error: "No se pudo crear enlace de pago"
 *    → Verificar conexión HTTPS a Wompi
 *    → Verificar credenciales sean válidas
 *    → Revisar logs: storage/logs/error.log
 *
 * Error: "Popup bloqueado"
 *    → Usuario/navegador está bloqueando popups
 *    → Usar breadcrumb de navegador para permitir
 *
 * ==================================================
 * SEGURIDAD:
 * ==================================================
 *
 * ✓ Credenciales de Wompi SOLO en servidor (.env)
 * ✓ Cliente JS NUNCA accede directamente a API de Wompi
 * ✓ Verificación de firma en webhook
 * ✓ Validación de entrada en todos los endpoints
 * ✓ HTTPS obligatorio para pagos (configurar en producción)
 *
 * ==================================================
 * EXTENSIONES FUTURAS:
 * ==================================================
 *
 * 1. Guardar datos de pago en BD
 *    - Crear tabla: pos_payments
 *    - Campos: reference, amount, status, timestamp
 *
 * 2. Procesar webhook de Wompi
 *    - Verificar firma
 *    - Actualizar estado de transacción
 *    - Generar DTE automático
 *
 * 3. Recuperación de pagos incompletos
 *    - Guardar estado provisional
 *    - Permitir reintentar mismo pago
 *
 * 4. Integración con otros métodos
 *    - PayPal
 *    - Stripe
 *    - Transferencia bancaria
 *
 */
?>
