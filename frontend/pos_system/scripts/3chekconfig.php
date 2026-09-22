<?php
/**
 * Descubre la configuración REAL que funciona
 */

echo "<h1>Descubriendo configuración REAL</h1>";

// Cargar ProductService para ver cómo se conecta
require_once __DIR__ . '/../includes/ProductService.php';

// Usar Reflection para inspeccionar
$reflector = new ReflectionClass('ProductService');
$productService = new ProductService();

echo "<h2>1. Propiedades de ProductService</h2>";
foreach ($reflector->getProperties() as $property) {
    $property->setAccessible(true);
    $value = $property->getValue($productService);
    
    if ($value instanceof PDO) {
        echo "<p style='color:green'>✅ Encontrada conexión PDO</p>";
        
        // Obtener detalles
        try {
            $status = $value->getAttribute(PDO::ATTR_CONNECTION_STATUS);
            echo "<pre>Status: " . htmlspecialchars($status) . "</pre>";
            
            // Intentar obtener host de DSN
            $dsn = $value->getAttribute(PDO::ATTR_CONNECTION_STATUS);
            if (preg_match('/host=([^;]+)/', $dsn, $matches)) {
                echo "<p><strong>Host REAL usado:</strong> " . $matches[1] . "</p>";
            }
        } catch (Exception $e) {
            echo "<p>No se pudo obtener detalles: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h2>2. Configuración alternativa</h2>";
echo "<p>Tu ProductService funciona, pero CustomerService no. Posibles razones:</p>";
echo "<ol>";
echo "<li>CustomerService está usando una configuración diferente</li>";
echo "<li>CustomerService tiene el caché de esquema corrupto</li>";
echo "<li>Los parámetros de conexión son diferentes</li>";
echo "</ol>";

echo "<h2>3. Solución RÁPIDA</h2>";
echo "<p>Como la columna 'active' ya existe en la base de datos pero PHP no la reconoce,</p>";
echo "<p>la solución más rápida es <strong>ELIMINAR 'active' DE LA CONSULTA</strong>.</p>";

echo "<hr>";
echo "<h3>📋 CÓDIGO PARA ARREGLAR CustomerService.php</h3>";

echo <<<HTML
<pre>
// EN CustomerService.php, busca la función create():

public function create(\$data) {
    // ENCUENTRA esto (o similar):
    \$sql = "INSERT INTO mh_cliente_consumidor 
            (p_nombre, p_apellido, dui, nit, telefono, email, direccion, municipio, departamento, active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // CAMBIALO A (elimina 'active'):
    \$sql = "INSERT INTO mh_cliente_consumidor 
            (p_nombre, p_apellido, dui, nit, telefono, email, direccion, municipio, departamento) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Y en los parámetros:
    \$params = [
        \$data['nombres'],
        \$data['apellidos'],
        \$this->cleanNumber(\$data['dui']),
        \$this->cleanNumber(\$data['nit']),
        \$this->cleanNumber(\$data['telefono']),
        \$data['email'],
        \$data['direccion'],
        \$this->getMunicipioCode(\$data['municipio']),
        \$this->getDepartamentoCode(\$data['departamento'])
        // NO incluyas 'active' aquí
    ];
    
    // Función auxiliar para limpiar números:
    private function cleanNumber(\$str) {
        return preg_replace('/[^0-9]/', '', \$str);
    }
}
</pre>
HTML;

echo '<p><a href="../test_customer.php" style="background:green;color:white;padding:10px;text-decoration:none;">🔄 Probar CustomerService después del cambio</a></p>';
?>