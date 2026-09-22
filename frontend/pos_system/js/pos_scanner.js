/**
 * Sistema POS - Módulo de Escáner y Búsqueda
 * ===========================================
 * Maneja la entrada de códigos de barras y la búsqueda de productos
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

class ProductScanner {
    constructor() {
        this.posSystem = null;
        this.barcodeInput = null;
        this.searchTimer = null;
        this.lastScanTime = 0;
        this.scanBuffer = '';
        this.SCAN_TIMEOUT = 50; // ms
    }

    init(posSystem) {
        this.posSystem = posSystem;
        this.barcodeInput = document.getElementById('barcode-input');
        
        if (this.barcodeInput) {
            this.barcodeInput.addEventListener('keydown', this.handleKeyDown.bind(this));
            this.barcodeInput.addEventListener('input', this.handleInput.bind(this));
            console.log('ProductScanner inicializado.');
        } else {
             console.error('El elemento #barcode-input no se encontró.');
        }
    }

    /**
     * Maneja la entrada manual para la funcionalidad de búsqueda.
     */
    handleInput(event) {
        const value = event.target.value.trim();
        
        // Si el valor no es un código de barras típico (numérico largo), es una búsqueda.
        if (value.length > 3 && !/^\d{8,20}$/.test(value)) {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                this.searchProducts(value);
            }, 300); // 300ms de debounce
        }
    }

    /**
     * Maneja la pulsación de teclas, detectando escaneos y la tecla ENTER.
     */
    handleKeyDown(event) {
        const currentTime = new Date().getTime();
        const duration = currentTime - this.lastScanTime;
        this.lastScanTime = currentTime;

        if (event.key === 'Enter') {
            event.preventDefault(); // Previene el submit del formulario
            
            const barcode = this.scanBuffer || event.target.value.trim();
            
            if (barcode) {
                this.processBarcode(barcode);
                this.scanBuffer = ''; // Limpia el buffer después de procesar
                event.target.value = ''; // Limpia el input
            }
            return;
        }

        // Detección de escáner (si la entrada es muy rápida)
        if (duration < this.SCAN_TIMEOUT) {
            this.scanBuffer += event.key;
        } else {
            this.scanBuffer = event.key;
        }
    }

    /**
     * Procesa un código de barras o código de producto.
     */
    async processBarcode(code) {
        if (!code) return;
        
        this.posSystem.ui.showLoading('Buscando producto...');
        this.barcodeInput.value = ''; // Limpiar el input inmediatamente

        try {
            const result = await this.posSystem.api.getProductByBarcode(code);
            
            if (result.success && result.product) {
                this.posSystem.addItemToCart(result.product, 1);
            } else {
                this.posSystem.ui.showNotification(`Producto no encontrado: ${code}`, 'error');
            }
        } catch (error) {
            console.error('Error al buscar producto:', error);
            this.posSystem.ui.showNotification('Error de conexión con el servidor', 'error');
        } finally {
            this.posSystem.ui.hideLoading();
            this.barcodeInput.focus(); // Devolver el foco
        }
    }

    /**
     * Busca productos por nombre/código
     */
    async searchProducts(searchTerm) {
        if (searchTerm.length < 3) return;

        this.posSystem.ui.showLoading('Buscando...');
        
        try {
            const result = await this.posSystem.api.searchProducts(searchTerm);
            
            if (result.success && result.products.length > 0) {
                // Mostrar resultados en un modal o sección de la UI
                this.posSystem.ui.displaySearchResults(result.products);
            } else {
                this.posSystem.ui.showNotification(`No se encontraron productos para: "${searchTerm}"`, 'info');
                this.posSystem.ui.displaySearchResults([]);
            }
        } catch (error) {
            console.error('Error en la búsqueda:', error);
            this.posSystem.ui.showNotification('Error de conexión para la búsqueda', 'error');
        } finally {
            this.posSystem.ui.hideLoading();
        }
    }
}
