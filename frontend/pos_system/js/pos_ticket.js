/**
 * Sistema POS - Módulo de Impresión de Ticket
 * ============================================
 * Maneja la generación y visualización de tickets
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

class TicketPrinter {
    constructor() {
        this.posSystem = null;
    }

    init(posSystem) {
        this.posSystem = posSystem;
    }
    
    /**
     * Muestra el resumen de la venta (simulando un ticket)
     */
    showSaleSummary(saleData) {
        Swal.fire({
            title: 'Venta Completada',
            html: this.generateTicketHTML(saleData),
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-print"></i> Imprimir Ticket',
            cancelButtonText: 'Cerrar',
            width: '400px',
            customClass: {
                container: 'pos-summary-modal'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.printTicket(saleData);
            }
        });
    }

    /**
     * Genera el HTML del ticket para mostrar en el modal
     */
    generateTicketHTML(saleData) {
        // En una aplicación real, se haría una llamada API a /templates/ticket.php
        // para renderizar el ticket del lado del servidor o se usaría una plantilla JS
        
        let html = `<div class="ticket-preview">`;
        html += `<h5 class="text-center">TICKET DE VENTA</h5>`;
        html += `<p><strong>Fecha:</strong> ${new Date(saleData.timestamp).toLocaleString('es-SV')}</p>`;
        html += `<p><strong>Cliente:</strong> ${saleData.customer ? saleData.customer.name : 'Consumidor Final'}</p>`;
        html += `<hr style="border-top: 1px dashed #333;">`;
        
        html += `<table>`;
        html += `<tr><th style="width: 50%">Producto</th><th>Cant.</th><th>Total</th></tr>`;
        saleData.items.forEach(item => {
             html += `<tr>
                        <td>${item.name}</td>
                        <td class="text-end">${item.quantity}</td>
                        <td class="text-end">$${(item.price * item.quantity).toFixed(2)}</td>
                      </tr>`;
        });
        html += `</table>`;

        html += `<hr style="border-top: 1px dashed #333;">`;
        html += `<p class="text-end"><strong>SUBTOTAL:</strong> $${saleData.totals.subtotal.toFixed(2)}</p>`;
        html += `<p class="text-end"><strong>IVA:</strong> $${saleData.totals.tax.toFixed(2)}</p>`;
        html += `<h4 class="text-end text-success">TOTAL: $${saleData.totals.total.toFixed(2)}</h4>`;
        
        if (saleData.payment.method === 'cash') {
            html += `<p class="text-end"><strong>Efectivo Recibido:</strong> $${(saleData.payment.cash_received || 0).toFixed(2)}</p>`;
            html += `<p class="text-end"><strong>Cambio:</strong> $${(saleData.payment.change || 0).toFixed(2)}</p>`;
        }
        
        html += `<hr style="border-top: 1px dashed #333;">`;
        html += `<p class="text-center"><small>¡Gracias por su compra!</small></p>`;
        
        if (saleData.dte) {
            html += `<div class="alert alert-info p-2 mt-2">
                         DTE: ${saleData.dte.identificacion.codigoGeneracion}<br>
                         <small>Documento Tributario Electrónico</small>
                     </div>`;
        }
        
        html += `</div>`;
        return html;
    }

    /**
     * Simula la impresión del ticket
     */
    async printTicket(saleData) {
        this.posSystem.ui.showLoading('Generando documento para impresión...');
        
        // 1. Obtener el HTML del ticket desde el servidor
        try {
            const ticketHTML = await this.posSystem.api.getTicketHTML(saleData);
            
            if (ticketHTML) {
                // 2. Abrir una nueva ventana de impresión
                const printWindow = window.open('', 'PrintTicket', 'height=600,width=400');
                
                printWindow.document.write('<html><head><title>Ticket de Venta</title>');
                // Incluir los estilos del ticket.php
                printWindow.document.write('<style>@media print { @page { margin: 0; size: 80mm auto; } } body { font-family: "Courier New", monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 5mm; } /* ... otros estilos de ticket.php */</style>'); 
                printWindow.document.write('</head><body>');
                printWindow.document.write(ticketHTML);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                
                // Esperar a que la ventana cargue y luego imprimir
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.close();
                };
                
                this.posSystem.ui.showNotification('Impresión enviada', 'success', 2000);
            } else {
                this.posSystem.ui.showNotification('Error al obtener plantilla de ticket', 'error');
            }
        } catch (error) {
            console.error('Error durante la impresión:', error);
            this.posSystem.ui.showNotification('Error de impresión: ' + error.message, 'error');
        } finally {
            this.posSystem.ui.hideLoading();
        }
    }
}
