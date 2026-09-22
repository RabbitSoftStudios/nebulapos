/**
 * EJEMPLOS DE USO - INTEGRACIÓN WOMPI
 * ====================================
 * Ejemplos prácticos de cómo usar el sistema de pagos
 */

// ============================================================
// EJEMPLO 1: Uso básico de WompiPayment
// ============================================================

// En la consola del navegador (F12):
(async () => {
    try {
        const result = await WompiPayment.processPayment({
            amount: 50.00,           // $50.00
            email: 'cliente@mail.com',
            description: 'Compra de productos'
        });

        console.log('✓ Pago exitoso:', result);
        console.log('Referencia:', result.reference);
        console.log('Monto:', result.paymentData.amount);

    } catch (error) {
        console.error('✗ Error en pago:', error.message);
    }
})();

// ============================================================
// EJEMPLO 2: Dentro de pos_sale.php (Contexto completo)
// ============================================================

async function showCardPaymentModal(total) {
    // ... código anterior ...
    
    try {
        // Obtener datos del cliente
        const email = currentCustomer.correo || 'cliente@empresa.com';
        const description = `Venta POS - ${currentCustomer.nombre}`;

        // Procesar pago con Wompi
        const paymentResult = await WompiPayment.processPayment({
            amount: total,
            email: email,
            description: description
        });

        // Procesar venta si fue exitoso
        if (paymentResult.success) {
            processSale('card', {
                reference: paymentResult.reference,
                method: 'Wompi',
                status: 'completed'
            });
        }

    } catch (error) {
        console.error('Error:', error);
        // Mostrar error al usuario
    }
}

// ============================================================
// EJEMPLO 3: Verificar configuración de Wompi
// ============================================================

(async () => {
    // Obtener configuración del servidor
    const response = await fetch('ajax/wompi_payment.php?action=get-config');
    const config = await response.json();

    if (config.success) {
        console.log('Configuración de Wompi:');
        console.log('Client ID:', config.clientId);
        console.log('API URL:', config.apiUrl);
        console.log('Token URL:', config.tokenUrl);
    }
})();

// ============================================================
// EJEMPLO 4: Test de crear un enlace de pago manualmente
// ============================================================

async function testCreatePaymentLink() {
    const paymentData = {
        amount: 25.50,
        email: 'test@example.com',
        description: 'Producto de prueba',
        reference: 'TEST-' + Date.now()
    };

    try {
        const response = await fetch('ajax/wompi_payment.php?action=create-payment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(paymentData)
        });

        const result = await response.json();

        if (result.success) {
            console.log('✓ Enlace creado:');
            console.log('URL:', result.paymentLink);
            console.log('Referencia:', result.reference);
            console.log('Monto:', result.amount);

            // Abrir en nueva ventana
            window.open(result.paymentLink, '_blank', 'width=600,height=700');

        } else {
            console.error('Error:', result.error);
        }

    } catch (error) {
        console.error('Error de conexión:', error);
    }
}

// ============================================================
// EJEMPLO 5: Monitorear estado de múltiples pagos
// ============================================================

async function monitorPayments(references) {
    const results = {};

    for (const ref of references) {
        try {
            const response = await fetch('ajax/wompi_payment.php?action=verify-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reference: ref })
            });

            const data = await response.json();
            results[ref] = data;

        } catch (error) {
            results[ref] = { error: error.message };
        }
    }

    return results;
}

// Uso:
// monitorPayments(['REF-001', 'REF-002']).then(results => {
//     console.log('Estados:', results);
// });

// ============================================================
// EJEMPLO 6: Integración con eventos del POS
// ============================================================

// Interceptar clic en botón "Tarjeta"
document.addEventListener('DOMContentLoaded', () => {
    const cardButton = document.querySelector('.card-btn');

    if (cardButton) {
        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();

            const total = calculateTotal();

            if (total <= 0) {
                alert('Carrito vacío');
                return;
            }

            try {
                // Mostrar loader
                console.log('Iniciando pago de $' + total.toFixed(2));

                // Procesar pago
                const result = await WompiPayment.processPayment({
                    amount: total,
                    email: currentCustomer.correo,
                    description: 'Venta POS'
                });

                // Resultado
                if (result.success) {
                    console.log('✓ Pago completado');
                    processSale('card', { reference: result.reference });
                } else {
                    console.error('✗ Pago fallido');
                }

            } catch (error) {
                console.error('Error:', error.message);
            }
        });
    }
});

// ============================================================
// EJEMPLO 7: Implementar reintentos automáticos
// ============================================================

async function processPaymentWithRetries(paymentData, maxRetries = 3) {
    let lastError = null;

    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            console.log(`Intento ${attempt}/${maxRetries}...`);

            const result = await WompiPayment.processPayment(paymentData);

            if (result.success) {
                console.log('✓ Pago completado en intento', attempt);
                return result;
            }

        } catch (error) {
            lastError = error;
            console.warn(`Intento ${attempt} falló:`, error.message);

            if (attempt < maxRetries) {
                // Esperar 2 segundos antes de reintentar
                await new Promise(resolve => setTimeout(resolve, 2000));
            }
        }
    }

    throw new Error(`Falló después de ${maxRetries} intentos: ${lastError.message}`);
}

// Uso:
// processPaymentWithRetries({
//     amount: 100,
//     email: 'user@example.com'
// }).catch(err => console.error(err));

// ============================================================
// EJEMPLO 8: Validación de monto antes de pago
// ============================================================

function validatePaymentAmount(amount) {
    // Validaciones comunes
    const validations = [
        { pass: amount > 0, msg: 'El monto debe ser mayor a 0' },
        { pass: amount <= 10000, msg: 'El monto máximo es $10,000' },
        { pass: Number.isFinite(amount), msg: 'El monto debe ser un número válido' },
        { pass: /^\d+(\.\d{1,2})?$/.test(amount.toString()), msg: 'Máximo 2 decimales' }
    ];

    const errors = validations
        .filter(v => !v.pass)
        .map(v => v.msg);

    return { valid: errors.length === 0, errors };
}

// Uso:
// const validation = validatePaymentAmount(100.50);
// if (validation.valid) {
//     // Proceder con pago
// } else {
//     console.error(validation.errors);
// }

// ============================================================
// EJEMPLO 9: Simular flujo completo de venta
// ============================================================

async function completeSaleFlow() {
    console.log('=== FLUJO DE VENTA COMPLETO ===\n');

    // 1. Preparar datos
    console.log('1. Preparando datos de venta...');
    const total = calculateTotal();
    const email = currentCustomer.correo;

    // 2. Validar
    console.log('2. Validando información...');
    if (total <= 0) {
        console.error('Error: Carrito vacío');
        return;
    }

    // 3. Procesar pago
    console.log('3. Procesando pago con Wompi...');
    try {
        const paymentResult = await WompiPayment.processPayment({
            amount: total,
            email: email,
            description: `Venta - ${new Date().toLocaleString()}`
        });

        // 4. Procesar venta
        console.log('4. Registrando venta...');
        const saleData = prepareDTEJson('card', {
            reference: paymentResult.reference,
            method: 'Wompi',
            status: 'completed'
        });

        // 5. Guardar en servidor
        console.log('5. Guardando en servidor...');
        const response = await fetch('ajax/process_sale_complete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(saleData)
        });

        const result = await response.json();

        if (result.success) {
            console.log('✓ Venta completada exitosamente');
            console.log('Número Control:', result.numeroControl);
            console.log('Código Generación:', result.codigoGeneracion);

            // 6. Limpiar carrito
            localCart = [];
            renderLocalCart();
        }

    } catch (error) {
        console.error('✗ Error en el proceso:', error.message);
    }
}

// ============================================================
// EJEMPLO 10: Manejo de errores específicos
// ============================================================

async function processPaymentWithErrorHandling(amount, email) {
    try {
        return await WompiPayment.processPayment({
            amount: amount,
            email: email,
            description: 'Compra POS'
        });

    } catch (error) {
        // Diferentes tipos de errores
        if (error.message.includes('popup')) {
            console.error('Error: Bloqueador de pop-ups activo');
            alert('Desactive el bloqueador de pop-ups e intente nuevamente');

        } else if (error.message.includes('timeout')) {
            console.error('Error: Tiempo de espera agotado');
            alert('La operación tardó demasiado. Intente nuevamente');

        } else if (error.message.includes('network')) {
            console.error('Error: Problema de conexión');
            alert('Verifique su conexión a internet');

        } else if (error.message.includes('Monto inválido')) {
            console.error('Error: Monto no válido');
            alert('El monto debe ser mayor a 0');

        } else {
            console.error('Error desconocido:', error.message);
            alert('Ocurrió un error. Intente nuevamente');
        }

        throw error;
    }
}

// ============================================================
// EJEMPLO 11: Testing en consola
// ============================================================

// Copiar y pegar en consola (F12):

// Probar módulo cargado
console.log('WompiPayment:', typeof WompiPayment);

// Probar get-config
fetch('ajax/wompi_payment.php?action=get-config')
    .then(r => r.json())
    .then(d => console.log('Config:', d));

// Probar create-payment
fetch('ajax/wompi_payment.php?action=create-payment', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        amount: 10,
        email: 'test@test.com',
        description: 'Test'
    })
})
    .then(r => r.json())
    .then(d => console.log('Payment:', d));

// ============================================================
