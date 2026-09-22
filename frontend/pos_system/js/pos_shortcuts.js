/**
 * Sistema POS - Módulo de Atajos de Teclado
 * ==========================================
 * Permite la ejecución de acciones rápidas mediante el teclado
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

class POSShortcuts {
    constructor() {
        this.shortcuts = {
            'F1': {
                action: 'Finalizar Venta (Efectivo)',
                handler: () => { 
                    document.querySelector('.payment-btn[data-type="cash"]').click();
                    return true;
                }
            },
            'F2': {
                action: 'Finalizar Venta (Tarjeta)',
                handler: () => { 
                    document.querySelector('.payment-btn[data-type="card"]').click();
                    return true;
                }
            },
            'F3': {
                action: 'Buscar/Escanear',
                handler: () => { 
                    document.getElementById('barcode-input').focus();
                    return true;
                }
            },
            'F4': {
                action: 'Vaciar Carrito',
                handler: () => { 
                    document.getElementById('clear-cart-btn').click();
                    return true;
                }
            },
            'F5': {
                action: 'Clientes',
                handler: () => { 
                    document.getElementById('customer-btn').click();
                    return true;
                }
            },
             'F6': {
                action: 'Descuento',
                handler: () => { 
                    document.getElementById('discount-btn').click();
                    return true;
                }
            }
        };
    }
    
    init() {
        document.addEventListener('keydown', this.handleKeyDown.bind(this));
        console.log('POSShortcuts inicializado.');
    }
    
    handleKeyDown(event) {
        const key = event.key.toUpperCase();
        
        if (this.shortcuts[key]) {
            // Prevenir el comportamiento por defecto de la tecla Fx
            event.preventDefault();
            
            // Ejecutar el handler
            if (this.shortcuts[key].handler()) {
                console.log(`Atajo de teclado ejecutado: ${this.shortcuts[key].action}`);
            }
        }
        
        // Esc + Cierra modales
        if (event.key === 'Escape') {
             // Cerrar modal de SweetAlert2 si está abierto
             Swal.close();
             // Cerrar modales de Bootstrap
             const openModals = document.querySelectorAll('.modal.show');
             if (openModals.length > 0) {
                 const modalInstance = bootstrap.Modal.getInstance(openModals[openModals.length - 1]);
                 if (modalInstance) {
                     modalInstance.hide();
                 }
             }
        }
    }
    
    getShortcuts() {
        return this.shortcuts;
    }
}
