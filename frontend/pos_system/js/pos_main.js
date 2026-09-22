/**
 * Sistema POS - Controlador Principal
 * =====================================
 * Coordina todos los módulos del sistema POS
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

class POSSystem {
    constructor() {
        this.cart = [];
        this.customer = null;
        this.cashier = null;
        this.register = null;
        this.currentSale = null;
        this.isOffline = false;
        this.pendingSales = [];
        
        // Inicializar módulos
        this.scanner = new ProductScanner();
        this.ui = new POS_UI();
        this.api = new POS_API();
        this.payment = new PaymentProcessor();
        this.printer = new TicketPrinter();
    }
    
    /**
     * Inicializar sistema POS
     */
    init() {
        console.log('Inicializando sistema POS...');
        
        // Cargar configuración
        this.loadConfig();
        
        // Cargar datos del cajero
        this.loadCashierData();
        
        // Cargar datos de la caja
        this.loadRegisterData();
        
        // Cargar productos frecuentes
        this.loadFrequentProducts();
        // Cargar carrito guardado
        this.loadCartStorage();

        // Inicializar módulos dependientes
        this.scanner.init(this);
        this.ui.init(this);
        this.payment.init(this);

        // Registrar manejadores de eventos
        this.setupEventListeners();
        
        console.log('Sistema POS inicializado.');
    }

    /**
     * Cargar configuración del sistema (asume constantes PHP cargadas en JS globals)
     */
    loadConfig() {
        this.config = {
            appName: 'Nebula POS System',
            appVersion: '2.0.0',
            ivaRate: parseFloat(window.IVA_RATE || 0.13),
            posCajaNumero: window.POS_CAJA_NUMERO || '001',
            dteEnvironment: window.DTE_ENVIRONMENT || '00',
            dteFactura: window.DTE_FACTURA || '01',
            dteEmisorNit: window.DTE_EMISOR_NIT || 'N/A',
            dteEmisorNrc: window.DTE_EMISOR_NRC || 'N/A',
            enableOfflineMode: window.ENABLE_OFFLINE_MODE || false,
            offlineSyncInterval: window.OFFLINE_SYNC_INTERVAL || 300,
        };
        console.log('Configuración cargada:', this.config);
    }
    
    /**
     * Cargar datos del cajero (simulado)
     */
    loadCashierData() {
        // En un sistema real, esto vendría de una sesión PHP
        this.cashier = window.cashierData ?? { 
            id: 1, 
            name: 'Administrador', 
            code: 'ADMIN',
            permissions: ['all'] 
        };
        console.log('Cajero activo:', this.cashier);
    }

    /**
     * Cargar datos de la caja
     */
    loadRegisterData() {
        this.register = {
            number: this.config.posCajaNumero,
            openingTime: new Date().toISOString(),
            initialCash: 0,
            currentCash: 0,
            salesCount: 0
        };
        console.log('Caja registradora:', this.register);
    }

    /**
     * Cargar productos frecuentes
     */
    async loadFrequentProducts() {
        try {
            // Asume que la UI ya tiene los productos PHP inyectados para el primer render
            // Si la lista es dinámica o paginada, usar la API.
            // Para el ejemplo, usamos el HTML generado por PHP.
            const grid = document.getElementById('frequent-products-grid');
            if (grid && grid.children.length > 0) {
                console.log('Productos frecuentes cargados desde HTML.');
            } else {
                 console.log('No hay productos frecuentes o están vacíos.');
            }
        } catch (error) {
            console.error('Error cargando productos frecuentes:', error);
        }
    }
    
    /**
     * Agregar producto al carrito
     */
    addItemToCart(product, quantity = 1) {
        if (!product || product.stock <= 0) {
            this.ui.showNotification(product ? 'Stock agotado' : 'Producto no válido', 'error');
            return;
        }

        const existingItem = this.cart.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({
                id: product.id,
                name: product.nombre,
                barcode: product.codigo_barras,
                price: parseFloat(product.precio),
                quantity: quantity,
                taxRate: this.config.ivaRate,
            });
        }
        
        this.updateTotals();
        this.ui.updateCartDisplay(this.cart);
        this.saveCartStorage();
        this.ui.showNotification(`"${product.nombre}" agregado al carrito`, 'success', 1500);
    }
    
    /**
     * Eliminar producto del carrito
     */
    removeItemFromCart(itemId) {
        this.cart = this.cart.filter(item => item.id !== itemId);
        this.updateTotals();
        this.ui.updateCartDisplay(this.cart);
        this.saveCartStorage();
        this.ui.showNotification('Producto eliminado del carrito', 'warning', 1500);
    }
    
    /**
     * Vaciar carrito
     */
    clearCart() {
        this.cart = [];
        this.updateTotals();
        this.ui.updateCartDisplay(this.cart);
        this.clearCartStorage();
        this.ui.showNotification('Carrito vaciado', 'info', 1500);
    }
    
    /**
     * Actualizar totales del carrito
     */
    updateTotals() {
        let subtotal = 0;
        let tax = 0;
        let discount = 0; // Se implementaría en el módulo de pago

        this.cart.forEach(item => {
            const itemSubtotal = item.price * item.quantity;
            subtotal += itemSubtotal;
            tax += itemSubtotal * item.taxRate;
        });

        const total = subtotal + tax - discount;
        
        this.totals = {
            subtotal: subtotal,
            tax: tax,
            discount: discount,
            total: total
        };
        
        this.ui.updateTotalsDisplay(this.totals);
        document.getElementById('complete-sale-btn').disabled = this.cart.length === 0;
    }

    /**
     * Procesar venta final
     */
    async processSale(paymentData) {
        if (this.cart.length === 0) {
            this.ui.showNotification('El carrito está vacío', 'error');
            return;
        }

        this.updateTotals(); // Asegurar que los totales estén al día

        const saleData = {
            register: this.register.number,
            cashier: this.cashier.id,
            timestamp: new Date().toISOString(),
            items: this.cart,
            totals: this.totals,
            customer: this.customer,
            payment: paymentData,
            status: 'completed',
            dte: null // Documento Tributario Electrónico
        };

        try {
            let saved = { success: false };
            
            if (!this.isOffline) {
                // 1. Guardar la venta en Supabase
                saved = await this.api.saveSale(saleData);
            }

            if (saved.success || this.config.enableOfflineMode) {
                // 2. Si se guardó o estamos en modo offline, procesar DTE y stock
                
                // Procesar DTE (solo si estamos online o se puede firmar localmente)
                if (!this.isOffline) {
                    const invoice = await this.generateDTE(saleData);
                    if (invoice.success) {
                        saleData.dte = invoice.data;
                        // Enviar factura por correo si el cliente tiene email
                        if (saleData.customer && saleData.customer.email) {
                            await this.sendEmailInvoice(saleData);
                        }
                    } else {
                        // Marcar error DTE para sincronización posterior
                        saleData.status = 'completed_error_dte'; 
                    }
                    
                    // Actualizar stock de productos
                    for (const item of saleData.items) {
                        await this.api.updateProductStock(item.id, item.quantity);
                    }
                }

                if (this.isOffline) {
                    // Guardar como pendiente si estamos offline
                    this.savePendingSale(saleData);
                    this.ui.showNotification('Venta guardada localmente (Modo offline)', 'warning');
                } else {
                    this.ui.showNotification('Venta procesada exitosamente', 'success');
                }
                
            } else {
                // Si falla la API y no es modo offline
                this.ui.showNotification('Error al guardar la venta. Intente de nuevo.', 'error');
                return;
            }
            
            // Finalización exitosa
            this.currentSale = saleData;
            this.cart = [];
            this.ui.updateCartDisplay(this.cart);
            this.updateTotals();
            this.clearCartStorage();
            this.ui.showSaleSummary(saleData); // Mostrar ticket o resumen

        } catch (error) {
            console.error('Error procesando venta:', error);
            this.ui.showNotification('Error grave al procesar venta', 'error');
        }
    }

    /**
     * Generar documento tributario electrónico (Simulado)
     */
    async generateDTE(saleData) {
        try {
            // Lógica compleja de generación, firma y envío de DTE a DGII.
            // Aquí solo se simula la estructura.
            const dteData = {
                identificacion: {
                    version: 1,
                    ambiente: this.config.dteEnvironment,
                    tipoDte: this.config.dteFactura,
                    numeroControl: this.generateControlNumber(),
                    codigoGeneracion: this.generateUUID(),
                    fecEmi: new Date().toISOString().split('T')[0],
                    horEmi: new Date().toLocaleTimeString('es-SV')
                },
                emisor: {
                    nit: this.config.dteEmisorNit,
                    nrc: this.config.dteEmisorNrc,
                    nombre: this.config.appName,
                },
                // ... datos de receptor, cuerpoDocumento, resumen, etc.
            };
            
            // Simular llamada a API o librería de firma DTE
            const response = await this.api.generateDTE(dteData);
            return response;
            
        } catch (error) {
            console.error('Error generando DTE:', error);
            return { success: false, error: 'Error interno en DTE' };
        }
    }

    /**
     * Generar número de control DTE (Simulado)
     */
    generateControlNumber() {
        // Lógica de generación de número de control (ej: C001-000001-0000000001)
        return `C${this.register.number}-000001-${Math.floor(Math.random() * 10000000000).toString().padStart(10, '0')}`;
    }

    /**
     * Generar UUID (Simulado)
     */
    generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    /**
     * Enviar factura por correo (Simulado)
     */
    async sendEmailInvoice(saleData) {
        console.log(`Enviando factura a ${saleData.customer.email}...`);
        // Lógica de envío de correo
        return { success: true };
    }

    /**
     * Cargar carrito desde el storage
     */
    loadCartStorage() {
        const savedCart = localStorage.getItem('pos_current_cart');
        if (savedCart) {
            this.cart = JSON.parse(savedCart);
            this.ui.updateCartDisplay(this.cart);
            this.updateTotals();
            this.ui.showNotification('Carrito anterior recuperado', 'info', 2000);
        }
    }

    /**
     * Guardar carrito en el storage
     */
    saveCartStorage() {
        localStorage.setItem('pos_current_cart', JSON.stringify(this.cart));
    }

    /**
     * Limpiar carrito del storage
     */
    clearCartStorage() {
        localStorage.removeItem('pos_current_cart');
    }
    
    /**
     * Guardar venta pendiente (Modo offline)
     */
    savePendingSale(saleData) {
        saleData.synced = false;
        this.pendingSales.push(saleData);
        localStorage.setItem('pos_pending_sales', JSON.stringify(this.pendingSales));
    }

    /**
     * Sincronizar ventas pendientes
     */
    async syncPendingSales() {
        if (this.pendingSales.length === 0 || this.isOffline) return;

        this.ui.showNotification(`Sincronizando ${this.pendingSales.length} ventas pendientes...`, 'info', 5000);

        const newPendingSales = [];
        for (const sale of this.pendingSales) {
            try {
                // 1. Guardar la venta en Supabase
                const saved = await this.api.saveSale(sale);
                if (saved.success) {
                    // 2. Procesar DTE para la venta sincronizada
                    const invoice = await this.generateDTE(sale);
                    if (invoice.success) {
                        sale.dte = invoice.data;
                        sale.synced = true;
                    } else {
                        // Si falla DTE, queda pendiente para reintentar solo DTE
                        newPendingSales.push(sale);
                    }
                } else {
                    // Si falla el guardado, queda pendiente
                    newPendingSales.push(sale);
                }
            } catch (error) {
                console.error('Error sincronizando venta:', error);
                newPendingSales.push(sale); // Mantener pendiente
            }
        }
        
        this.pendingSales = newPendingSales;
        localStorage.setItem('pos_pending_sales', JSON.stringify(this.pendingSales));

        if (this.pendingSales.length === 0) {
            this.ui.showNotification('Sincronización de ventas completada', 'success');
        } else {
            this.ui.showNotification(`${this.pendingSales.length} ventas pendientes por sincronizar`, 'warning');
        }
    }

    /**
     * Verificar conexión a internet
     */
    setupEventListeners() {
        window.addEventListener('online', () => this.handleOnlineStatus());
        window.addEventListener('offline', () => this.handleOfflineStatus());
        window.addEventListener('beforeunload', (event) => this.handleBeforeUnload(event));
        
        // Asignar listeners a botones de la UI (ej. botón de vaciar carrito)
        document.getElementById('clear-cart-btn').addEventListener('click', () => this.clearCart());

        // Event listener para agregar producto al carrito desde la cuadrícula de productos frecuentes
        document.getElementById('frequent-products-grid').addEventListener('click', (event) => {
            const btn = event.target.closest('.add-to-cart-btn');
            if (btn) {
                const card = btn.closest('.product-card');
                const productId = card.getAttribute('data-product-id');
                // En un caso real, harías una llamada API para obtener el producto completo
                // Para este demo, simulemos la búsqueda en un array local si estuviera disponible.
                console.log('Agregando producto con ID:', productId);
                // Lógica de búsqueda simulada o llamada API para obtener el producto por ID
                // y luego llamar a this.addItemToCart(product, 1)
            }
        });
        
        // Inicializar sincronización periódica si aplica
        if (this.config.enableOfflineMode && this.config.offlineSyncInterval > 0) {
            setInterval(() => this.syncPendingSales(), this.config.offlineSyncInterval * 1000);
        }
    }

    /**
     * Manejar cambio a online
     */
    handleOnlineStatus() {
        this.isOffline = false;
        this.ui.showNotification('Conexión restablecida', 'success');
        // Sincronizar ventas pendientes
        this.syncPendingSales();
    }

    /**
     * Manejar cambio a offline
     */
    handleOfflineStatus() {
        this.isOffline = true;
        this.ui.showNotification('Modo offline activado', 'warning');
    }

    /**
     * Manejar cierre de ventana (advertencia si el carrito tiene ítems)
     */
    handleBeforeUnload(event) {
        if (this.cart.length > 0) {
            const message = 'Tiene productos en el carrito. ¿Está seguro de salir?';
            event.returnValue = message;
            return message;
        }
    }

    /**
     * Abrir reportes
     */
    openReports() {
        window.open('../views/pos_reports.php', '_blank');
    }
}

// Inicializar sistema cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Estas variables globales deberían venir del PHP (pos_sale.php)
    window.cashierData = { id: 1, name: 'Administrador', code: 'ADMIN' };
    window.POS_CAJA_NUMERO = '001';
    window.DTE_ENVIRONMENT = '00';
    window.DTE_FACTURA = '01';
    window.DTE_EMISOR_NIT = 'N/A';
    window.DTE_EMISOR_NRC = 'N/A';
    window.IVA_RATE = 0.13;
    window.ENABLE_OFFLINE_MODE = true;
    window.OFFLINE_SYNC_INTERVAL = 300;
    
    window.posSystem = new POSSystem();
    window.posSystem.init();
});
