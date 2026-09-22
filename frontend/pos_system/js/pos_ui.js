/**
 * Sistema POS - Módulo de Interfaz de Usuario
 * ===========================================
 * Maneja la actualización del DOM y notificaciones
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

class POS_UI {
    constructor() {
        this.posSystem = null;
        this.cartContainer = document.getElementById('cart-items-container');
        this.totalsDisplay = {
            subtotal: document.getElementById('subtotal-amount'),
            tax: document.getElementById('tax-amount'),
            discount: document.getElementById('discount-amount'),
            total: document.getElementById('total-amount')
        };
        this.customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
        this.loadingIndicator = null;
    }
    
    init(posSystem) {
        this.posSystem = posSystem;
        this.setupLoadingIndicator();
        console.log('POS_UI inicializado.');
    }
    
    setupLoadingIndicator() {
        // Crear un indicador de carga global
        this.loadingIndicator = document.createElement('div');
        this.loadingIndicator.id = 'global-loading';
        this.loadingIndicator.style.cssText = `
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: white;
            font-size: 1.5rem;
        `;
        this.loadingIndicator.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> <span>Cargando...</span>';
        document.body.appendChild(this.loadingIndicator);
    }

    /**
     * Muestra el indicador de carga
     */
    showLoading(message = 'Procesando...') {
        document.querySelector('#global-loading span').textContent = message;
        this.loadingIndicator.style.display = 'flex';
    }

    /**
     * Oculta el indicador de carga
     */
    hideLoading() {
        this.loadingIndicator.style.display = 'none';
    }

    /**
     * Muestra notificaciones usando SweetAlert2
     */
    showNotification(message, type = 'info', timer = 3000) {
        Swal.fire({
            title: message,
            icon: type,
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    /**
     * Actualiza la lista de ítems en el carrito
     */
    updateCartDisplay(cart) {
        let html = '';
        if (cart.length === 0) {
            html = `<div class="empty-cart-message">
                        <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                        <p>El carrito está vacío</p>
                        <small>Escanee productos para comenzar</small>
                    </div>`;
            document.getElementById('complete-sale-btn').disabled = true;
        } else {
            cart.forEach(item => {
                html += `
                    <div class="cart-item">
                        <div class="item-details">
                            <span class="item-name">${item.name}</span>
                            <span class="item-price">$${item.price.toFixed(2)} c/u</span>
                        </div>
                        <div class="item-controls">
                            <input type="number" 
                                   class="form-control form-control-sm item-qty-input" 
                                   value="${item.quantity}" 
                                   min="1"
                                   data-item-id="${item.id}">
                            <span class="item-total">$${(item.price * item.quantity).toFixed(2)}</span>
                            <button class="btn btn-sm btn-outline-danger delete-item-btn" data-item-id="${item.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('complete-sale-btn').disabled = false;
        }
        this.cartContainer.innerHTML = html;
        this.cartContainer.scrollTop = this.cartContainer.scrollHeight; // Scroll al final
    }

    /**
     * Actualiza la sección de totales
     */
    updateTotalsDisplay(totals) {
        this.totalsDisplay.subtotal.textContent = `$${totals.subtotal.toFixed(2)}`;
        this.totalsDisplay.tax.textContent = `$${totals.tax.toFixed(2)}`;
        this.totalsDisplay.discount.textContent = `$${totals.discount.toFixed(2)}`;
        this.totalsDisplay.total.textContent = `$${totals.total.toFixed(2)}`;
    }
    
    /**
     * Muestra el modal de clientes
     */
    showCustomerModal() {
        this.customerModal.show();
        // Aquí se implementaría la lógica para cargar y buscar clientes
    }
    
    /**
     * Muestra el resumen de la venta (delegado a TicketPrinter)
     */
    showSaleSummary(saleData) {
        this.posSystem.printer.showSaleSummary(saleData);
    }
    
    /**
     * Muestra los resultados de una búsqueda de productos (para selección manual)
     */
    displaySearchResults(products) {
        // En una implementación real, esto abriría un modal o actualizaría un panel
        console.log('Resultados de búsqueda:', products);
        
        // Simulación de SweetAlert para selección
        if (products.length > 0) {
            const inputOptions = products.reduce((acc, product) => {
                acc[product.id] = `${product.nombre} ($${product.precio.toFixed(2)})`;
                return acc;
            }, {});

            Swal.fire({
                title: 'Seleccionar Producto',
                input: 'select',
                inputOptions: inputOptions,
                inputPlaceholder: 'Seleccione un producto',
                showCancelButton: true,
                confirmButtonText: 'Agregar al Carrito',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debe seleccionar un producto';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const productId = parseInt(result.value);
                    const selectedProduct = products.find(p => p.id === productId);
                    
                    if (selectedProduct) {
                         // Llamada a POSSystem para agregar el producto
                         this.posSystem.addItemToCart(selectedProduct, 1);
                    }
                }
            });
        }
    }
}
