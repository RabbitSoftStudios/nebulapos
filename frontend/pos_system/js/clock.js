/**
 * Sistema POS - Módulo de Carrito
 * =====================================
 * Contiene la lógica del manejo de reloj en tiempo real
 * (Las funciones principales están en POSSystem, este archivo podría contener helpers)
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

function actualizarReloj() {
    const ahora = new Date();

    // Obtener horas, minutos y segundos con formato 2 dígitos
    let horas = String(ahora.getHours()).padStart(2, '0');
    let minutos = String(ahora.getMinutes()).padStart(2, '0');
    let segundos = String(ahora.getSeconds()).padStart(2, '0');

    // Mostrar en el elemento HTML
    document.getElementById("current-time").textContent = `${horas}:${minutos}:${segundos}`;
}

// Actualizar cada segundo
setInterval(actualizarReloj, 1000);

// Mostrar inmediatamente al cargar la página
actualizarReloj();