<?php
/**
 * Sistema POS - Parcial de Pie de Página (Footer)
 * ================================================
 * Pie de página genérico para vistas de gestión/dashboard.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// Se utiliza APP_NAME y APP_VERSION de constants.php
?>
<footer class="pos-footer text-center py-2 bg-light border-top mt-auto">
    <div class="container-fluid">
        <small class="text-muted">
            &copy; <?= date('Y') ?> <?= htmlspecialchars(APP_NAME) ?> | Versión <?= htmlspecialchars(APP_VERSION) ?> | Desarrollado con 💙 por Nebula DET Team.
        </small>
    </div>
</footer>