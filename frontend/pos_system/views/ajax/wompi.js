/**
 * Wompi Payment Module
 * ====================
 * Integración con API de Wompi para procesamiento de pagos con tarjeta
 * 
 * Uso:
 * const payment = await WompiPayment.initializePayment({
 *     amount: 100.00,
 *     email: 'cliente@example.com',
 *     description: 'Compra en POS'
 * });
 */

const WompiPayment = (() => {
    // Variables privadas
    let config = null;
    let currentPayment = null;

    /**
     * Inicializar configuración de Wompi
     */
    async function initialize() {
        try {
            const response = await fetch('ajax/wompi_payment.php?action=get-config');
            const data = await response.json();

            if (!data.success) {
                throw new Error('No se pudo cargar la configuración de Wompi');
            }

            config = data;
            console.log('✓ Wompi configurado correctamente');
            return true;

        } catch (error) {
            console.error('Error inicializando Wompi:', error);
            return false;
        }
    }

    /**
     * Crear un pago y obtener enlace
     */
    async function createPayment(paymentData) {
        try {
            // Validar datos
            if (!paymentData.amount || paymentData.amount <= 0) {
                throw new Error('Monto inválido');
            }

            if (!paymentData.email) {
                throw new Error('Email requerido');
            }

            // Llamar al backend para crear el enlace de pago
            const response = await fetch('ajax/wompi_payment.php?action=create-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount: paymentData.amount,
                    email: paymentData.email,
                    description: paymentData.description || 'Compra POS',
                    reference: paymentData.reference || generateReference()
                })
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error || 'Error al crear el pago');
            }

            currentPayment = result;
            return result;

        } catch (error) {
            console.error('Error creando pago:', error);
            throw error;
        }
    }

    /**
     * Abrir modal de pago en popup o modal
     */
    function openPaymentModal(paymentLink) {
        return new Promise((resolve, reject) => {
            // Crear ventana modal/popup para Wompi
            const width = 600;
            const height = 700;
            const left = (window.innerWidth - width) / 2;
            const top = (window.innerHeight - height) / 2;

            const popupWindow = window.open(
                paymentLink,
                'WompiPayment',
                `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
            );

            if (!popupWindow) {
                reject(new Error('No se pudo abrir la ventana de pago. Verifique bloqueadores de pop-ups'));
                return;
            }

            // Monitorear ventana cada 1 segundo
            const checkWindow = setInterval(() => {
                try {
                    // Si la ventana se cerró
                    if (popupWindow.closed) {
                        clearInterval(checkWindow);
                        
                        // Verificar si el pago fue completado
                        if (currentPayment) {
                            verifyPayment(currentPayment.reference)
                                .then(resolve)
                                .catch(reject);
                        } else {
                            reject(new Error('El pago fue cancelado'));
                        }
                    }
                } catch (error) {
                    // No se puede acceder a la ventana (diferente dominio es normal)
                }
            }, 1000);

            // Timeout después de 15 minutos
            setTimeout(() => {
                clearInterval(checkWindow);
                if (!popupWindow.closed) {
                    popupWindow.close();
                }
                reject(new Error('Tiempo de espera agotado para el pago'));
            }, 900000);
        });
    }

    /**
     * Verificar estado del pago
     */
    async function verifyPayment(reference) {
        try {
            const response = await fetch('ajax/wompi_payment.php?action=verify-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reference })
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error('Error al verificar el pago');
            }

            return result;

        } catch (error) {
            console.error('Error verificando pago:', error);
            throw error;
        }
    }

    /**
     * Generar referencia única
     */
    function generateReference() {
        return 'POS-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * API Pública
     */
    return {
        /**
         * Procesar pago completo
         * @param {Object} paymentData - {amount, email, description}
         * @returns {Promise<Object>}
         */
        async processPayment(paymentData) {
            try {
                // Inicializar si no está configurado
                if (!config) {
                    const initialized = await initialize();
                    if (!initialized) {
                        throw new Error('No se pudo inicializar Wompi');
                    }
                }

                // Crear pago
                const payment = await createPayment(paymentData);

                // Abrir modal de pago
                const result = await openPaymentModal(payment.paymentLink);

                return {
                    success: true,
                    paymentData: result,
                    reference: payment.reference
                };

            } catch (error) {
                console.error('Error procesando pago:', error);
                throw error;
            }
        },

        /**
         * Inicializar módulo
         */
        async init() {
            return await initialize();
        },

        /**
         * Obtener referencia actual del pago
         */
        getCurrentPayment() {
            return currentPayment;
        },

        /**
         * Limpiar estado
         */
        reset() {
            currentPayment = null;
        }
    };
})();
