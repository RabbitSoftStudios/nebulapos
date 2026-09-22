<?php
/**
 * Sistema POS - Parcial de Teclado Numérico Virtual (Keyboard)
 * =============================================================
 * Teclado numérico virtual para facilitar la entrada de datos en tablets/touchscreens.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */
?>

<div class="pos-virtual-keyboard mt-4">
    <div class="row g-2">
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="7">7</button></div>
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="8">8</button></div>
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="9">9</button></div>
        <div class="col-3">
            <button class="btn btn-danger keyboard-btn" data-key="backspace">
                <i class="fas fa-backspace"></i>
            </button>
        </div>
        
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="4">4</button></div>
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="5">5</button></div>
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="6">6</button></div>
        <div class="col-3">
            <button class="btn btn-info keyboard-btn text-white" data-key="qty" title="Cambiar Cantidad">
                <i class="fas fa-sort-numeric-up-alt"></i>
            </button>
        </div>
        
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="1">1</button></div>
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="2">2</button></div>
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="3">3</button></div>
        <div class="col-3">
            <button class="btn btn-secondary keyboard-btn" data-key=".">.</button>
        </div>
        
        <div class="col-3"><button class="btn btn-light keyboard-btn" data-key="0">0</button></div>
        <div class="col-3">
            <button class="btn btn-warning keyboard-btn" data-key="clear" title="Limpiar Entrada">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
        <div class="col-6">
            <button class="btn btn-primary keyboard-btn keyboard-enter" data-key="enter">
                <i class="fas fa-plus"></i> AGREGAR (ENTER)
            </button>
        </div>
    </div>
</div>