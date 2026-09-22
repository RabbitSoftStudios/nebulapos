<?php
/**
 * Sistema POS - Parcial de Scanner (Modal de Escaneo Avanzado/Búsqueda)
 * =======================================================================
 * Modal que permite activar la cámara para escaneo de códigos de barras (WebRTC)
 * o una interfaz de búsqueda avanzada de productos.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */
?>

<div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-camera me-2"></i> Escáner de Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="scanner-live-area text-center mb-3">
                    <video id="scanner-video" class="w-100 border rounded" style="max-height: 400px; display: none;"></video>
                    <div id="scanner-placeholder" class="alert alert-info py-4">
                        <i class="fas fa-search-plus fa-3x mb-2"></i>
                        <p>Haga clic en 'Iniciar Escáner' para usar la cámara y leer códigos.</p>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mb-3">
                    <button class="btn btn-success" id="start-scanner-btn">
                        <i class="fas fa-play"></i> Iniciar Escáner
                    </button>
                    <button class="btn btn-secondary" id="stop-scanner-btn" disabled>
                        <i class="fas fa-stop"></i> Detener
                    </button>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                    <input type="text" class="form-control" id="scanned-result-input" 
                           placeholder="Código Escaneado o Manualmente Ingresado">
                    <button class="btn btn-primary" id="search-product-modal-btn">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <div id="scanner-product-result" class="mt-3">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="add-scanned-product-btn" disabled>
                    <i class="fas fa-cart-plus"></i> Agregar al Carrito
                </button>
            </div>
        </div>
    </div>
</div>