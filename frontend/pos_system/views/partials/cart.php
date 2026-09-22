<?php
/**
 * Sistema POS - Parcial de Item del Carrito (Cart Item Template)
 * ================================================================
 * Plantilla HTML para un solo producto en la lista del carrito.
 * Este contenido será manipulado principalmente por pos_cart.js para renderizar los items.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */
?>
<div class="pos-cart-item d-flex align-items-center p-2 mb-2 bg-light border rounded" 
     data-product-id="{{product_id}}" 
     id="cart-item-{{product_id}}">
    
    <div class="item-quantity-control me-2">
        <button class="btn btn-sm btn-outline-primary item-qty-decrease" data-id="{{product_id}}">
            <i class="fas fa-minus"></i>
        </button>
        <span class="badge bg-primary mx-1 item-qty" data-id="{{product_id}}">
            {{quantity}}
        </span>
        <button class="btn btn-sm btn-outline-primary item-qty-increase" data-id="{{product_id}}">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <div class="item-details flex-grow-1 me-3">
        <h6 class="mb-0 item-name">{{name}}</h6>
        <small class="text-muted item-price-unit">$<span class="unit-price-value">{{price}}</span> c/u</small>
    </div>
    
    <div class="item-total-price text-end d-flex align-items-center">
        <strong class="text-dark item-subtotal me-2">$<span class="subtotal-value">{{subtotal}}</span></strong>
        <button class="btn btn-sm btn-outline-danger item-remove-btn" data-id="{{product_id}}" title="Eliminar">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>