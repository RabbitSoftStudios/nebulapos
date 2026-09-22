<?php
// config/base_config.php

// Define la ruta base del proyecto.
// En producción, el servidor podría determinar esto o usar una variable de entorno.
define('BASE_URL', '/posys/pos_system'); // Esto se sobreescribirá en producción

// Nota: En un entorno de producción con dominio propio, esta constante podría ser simplemente
// define('BASE_URL', ''); o define('BASE_URL', '/');
?>