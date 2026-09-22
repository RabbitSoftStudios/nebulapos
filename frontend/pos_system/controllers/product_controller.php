<?php
/**
 * Controlador de Productos
 * ==========================
 * Gestiona la lógica de obtención de productos para la vista.
 * Team MYTS
 */

require_once __DIR__ . '/../includes/ProductService.php';

/* =========================
   SERVICIO
========================= */
$service = new ProductService();

/* =========================
   PAGINACIÓN
========================= */
$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? (int) $_GET['page']
    : 1;

$perPage = 10;

/* =========================
   OBTENER PRODUCTOS
========================= */
$result_products = $service->list($page, $perPage);

$products = [];
$totalProducts = 0;
$totalPages = 1;

if (isset($result_products['success']) && $result_products['success'] === true) {
    $products = $result_products['data'] ?? [];
    $totalProducts = $result_products['total'] ?? 0;
    $totalPages = (int) ceil($totalProducts / $perPage);
} else {
    error_log(
        'Error fetching products via ProductService: ' .
        ($result_products['error'] ?? 'Unknown error')
    );
}

/* ==========================================================
   TODO: Implementar CategoryService cuando sea necesario
   ----------------------------------------------------------
   Obtener Categorías (comentado temporalmente - requiere CategoryService)

   $db_categories = supabase('pos_categories');
   $result_categories = $db_categories->select('*');
   $categories = [];
   if ($result_categories['success']) {
       foreach ($result_categories['data'] as $c) {
           $categories[] = [
               'id' => $c['id'],
               'name' => $c['nombre'] ?? $c['name'] ?? 'Sin Nombre'
           ];
       }
   }
========================================================== */

/* =========================
   CATEGORÍAS (VACÍO POR AHORA)
========================= */
$categories = [];

/* ==========================================================
   TODO: Implementar CategoryService cuando sea necesario
   ----------------------------------------------------------
   Obtener Categorías (comentado temporalmente - requiere CategoryService)
   
   $db_categories = supabase('pos_categories');
   $result_categories = $db_categories->select('*');
   $categories = [];
   if ($result_categories['success']) {
       foreach ($result_categories['data'] as $c) {
           $categories[] = [
               'id' => $c['id'],
               'name' => $c['nombre'] ?? $c['name'] ?? 'Sin Nombre'
           ];
       }
   }
========================================================== */

/* =========================
   CATEGORÍAS (VACÍO POR AHORA)
========================= */
$categories = [];
