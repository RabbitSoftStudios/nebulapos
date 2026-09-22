/**
 * Sistema POS - Módulo de Carrito
 * =====================================
 * Contiene la lógica del manejo de ítems en el carrito de compra
 * (Las funciones principales están en POSSystem, este archivo podría contener helpers)
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

/**
 * POSCart.js - Gestión Lógica del Carrito
 * Auditoría v4.0 - Reactividad y Persistencia
 */
class POSCart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('pos_cart_items')) || [];
        this.container = document.getElementById('cart-items-container');
        this.totalDisplay = document.getElementById('total-display');
        this.finishBtn = document.getElementById('finish-sale-btn');
    }

    init() {
        this.render();
        console.log('POSCart: Sistema de carrito auditado iniciado.');
    }

    addItem(product) {
        // product = { id, name, price, quantity }
        const existing = this.items.find(item => item.id === product.id);
        
        if (existing) {
            existing.quantity += 1;
        } else {
            this.items.push({ ...product });
        }
        
        this.saveAndRender();
    }

    removeItem(index) {
        this.items.splice(index, 1);
        this.saveAndRender();
    }

    updateQuantity(index, newQty) {
        if (newQty <= 0) {
            this.removeItem(index);
        } else {
            this.items[index].quantity = parseInt(newQty);
            this.saveAndRender();
        }
    }

    clear() {
        this.items = [];
        this.saveAndRender();
    }

    getTotal() {
        return this.items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    }

    saveAndRender() {
        localStorage.setItem('pos_cart_items', JSON.stringify(this.items));
        this.render();
    }

    render() {
        if (!this.container) return;

        if (this.items.length === 0) {
            this.container.innerHTML = '<div class="text-center text-muted p-4">Carrito vacío</div>';
            this.totalDisplay.textContent = '$0.00';
            if (this.finishBtn) this.finishBtn.disabled = true;
            return;
        }

        if (this.finishBtn) this.finishBtn.disabled = false;
        
        let html = '';
        this.items.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            html += `
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div style="flex: 1;">
                        <div class="fw-bold">${item.name}</div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" class="form-control form-control-sm" 
                                   style="width: 60px" value="${item.quantity}" 
                                   onchange="window.posCart.updateQuantity(${index}, this.value)">
                            <small class="text-muted">x $${item.price.toFixed(2)}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">$${subtotal.toFixed(2)}</div>
                        <button class="btn btn-sm text-danger" onclick="window.posCart.removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>`;
        });

        this.container.innerHTML = html;
        this.totalDisplay.textContent = `$${this.getTotal().toFixed(2)}`;
        
        // Actualizar subtotal si existe el elemento
        const subDisplay = document.getElementById('subtotal-display');
        if (subDisplay) subDisplay.textContent = `$${this.getTotal().toFixed(2)}`;
    }
}

// Instanciación global para que pos_sale.php lo reconozca
window.posCart = new POSCart();