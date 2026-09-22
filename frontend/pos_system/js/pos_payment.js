/**
 * Sistema POS - Módulo de Pago
 * =====================================
 * Maneja la lógica de procesamiento de pagos y cálculo de cambio/totales
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

class PaymentProcessor {
    constructor() {
        this.posSystem = null;
        this.paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        this.paymentType = 'cash';
        this.totalToPay = 0;
    }
    
    init(posSystem) {
        this.posSystem = posSystem;
        this.setupEventListeners();
        console.log('PaymentProcessor inicializado.');
    }

    setupEventListeners() {
        document.getElementById('confirm-payment-btn').addEventListener('click', this.confirmPayment.bind(this));
        
        const cashReceivedInput = document.getElementById('cash-received');
        if (cashReceivedInput) {
            cashReceivedInput.addEventListener('input', this.calculateChange.bind(this));
            cashReceivedInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    this.confirmPayment();
                }
            });
        }
    }
    
    /**
     * Muestra el modal de pago configurado para el tipo de pago
     */
    showPaymentModal(type) {
        if (this.posSystem.cart.length === 0) {
            this.posSystem.ui.showNotification('El carrito está vacío', 'info');
            return;
        }

        this.paymentType = type;
        this.totalToPay = this.posSystem.totals.total;

        document.getElementById('payment-total').textContent = `$${this.totalToPay.toFixed(2)}`;
        
        // Ocultar todas las secciones
        document.getElementById('cash-payment-section').style.display = 'none';
        document.getElementById('card-payment-section').style.display = 'none';
        
        const methodDisplay = document.getElementById('payment-method');

        // Mostrar sección relevante y configurar display
        if (type === 'cash') {
            document.getElementById('cash-payment-section').style.display = 'block';
            document.getElementById('cash-received').value = this.totalToPay.toFixed(2);
            this.calculateChange(); // Calcular cambio inicial
            document.getElementById('cash-received').focus();
            methodDisplay.innerHTML = '<i class="fas fa-money-bill-wave text-success"></i> Pago en Efectivo';
        } else if (type === 'card') {
            document.getElementById('card-payment-section').style.display = 'block';
            methodDisplay.innerHTML = '<i class="fas fa-credit-card text-primary"></i> Pago con Tarjeta';
        }
        
        this.paymentModal.show();
    }
    
    /**
     * Calcula el cambio para pagos en efectivo
     */
    calculateChange() {
        const cashReceived = parseFloat(document.getElementById('cash-received').value) || 0;
        const changeAmount = document.getElementById('change-amount');
        
        let change = cashReceived - this.totalToPay;
        
        changeAmount.textContent = `$${change.toFixed(2)}`;
        changeAmount.className = change >= 0 ? 'text-success' : 'text-danger';
        
        document.getElementById('confirm-payment-btn').disabled = change < 0;
    }
    
    /**
     * Confirma el pago y llama a procesar venta
     */
    confirmPayment() {
        const paymentData = {
            method: this.paymentType,
            amount: this.totalToPay
        };
        
        if (this.paymentType === 'cash') {
            const cashReceived = parseFloat(document.getElementById('cash-received').value) || 0;
            const change = cashReceived - this.totalToPay;
            
            if (change < 0) {
                this.posSystem.ui.showNotification('Efectivo insuficiente', 'error');
                return;
            }
            
            paymentData.cash_received = cashReceived;
            paymentData.change = change;
        } else if (this.paymentType === 'card') {
            const cardNumber = document.getElementById('card-number').value;
            // Validación mínima
            if (cardNumber.length < 16) {
                this.posSystem.ui.showNotification('Número de tarjeta inválido', 'error');
                return;
            }
            paymentData.reference = 'TRX-' + Math.random().toString(36).substring(2, 9).toUpperCase();
        }
        
        // Cerrar modal
        this.paymentModal.hide();
        
        // Procesar venta final
        this.posSystem.processSale(paymentData);
    }
    
    /**
     * Wrapper para finalizar venta desde el botón principal
     */
    completeSale() {
        // En este demo, simplemente asumimos un pago en efectivo si no se ha usado el modal
        const dummyPaymentData = {
            method: 'cash',
            amount: this.posSystem.totals.total,
            cash_received: this.posSystem.totals.total,
            change: 0
        };
        this.posSystem.processSale(dummyPaymentData);
    }
}
