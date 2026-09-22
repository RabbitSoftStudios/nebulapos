<?php
/**
 * Script de Verificación de Clientes - Corregido
 * Ahora usa datos que coinciden con la estructura real de la tabla
 */

// 1. Localizar y cargar el autoloader y entorno
$rootPath = __DIR__;
while (!file_exists($rootPath . '/vendor/autoload.php') && $rootPath !== dirname($rootPath)) {
    $rootPath = dirname($rootPath);
}

if (file_exists($rootPath . '/vendor/autoload.php')) {
    require_once $rootPath . '/vendor/autoload.php';
}

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// 2. Cargar Dependencias del Sistema
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/CustomerService.php';

echo "<h1>Verificación de CustomerService (mh_cliente_consumidor) - CORREGIDO</h1>";

try {
    $service = new CustomerService();
    
    echo "<h2>1. Crear Cliente</h2>";
    $newCustomer = [
        'nombres' => 'Juan Carlos',  // Se dividirá en p_nombre='Juan', s_nombre='Carlos'
        'apellidos' => 'Perez Lopez', // Se dividirá en p_apellido='Perez', s_apellido='Lopez'
        'dui' => '12345678-9',        // Se convertirá a 123456789
        'nit' => '1234-123456-123-1', // Se convertirá a 12341234561231
        'telefono' => '2222-2222',    // Se convertirá a 22222222
        'email' => 'juan.test@example.com',
        'direccion' => 'Calle Test #1, Colonia Centro',
        'municipio' => 'San Salvador', // Se convertirá a código 1
        'departamento' => 'San Salvador' // Se convertirá a código 1
    ];

    $createResult = $service->create($newCustomer);
    if ($createResult['success']) {
        $createdId = $createResult['data'][0]['id'] ?? null;
        echo "<p style='color:green'>✅ Cliente creado exitosamente. ID: " . ($createdId ?? 'N/A') . "</p>";
    } else {
        echo "<p style='color:red'>❌ Error al crear: " . ($createResult['error'] ?? 'Desconocido') . "</p>";
        $createdId = null;
    }

    if ($createdId) {
        echo "<h2>2. Leer Cliente (GetById)</h2>";
        $readResult = $service->getById($createdId);
        if ($readResult['success']) {
            $cliente = $readResult['data'];
            echo "<p style='color:green'>✅ Cliente encontrado: " . 
                 htmlspecialchars($cliente['nombres']) . " " . 
                 htmlspecialchars($cliente['apellidos']) . "</p>";
            echo "<pre>Datos completos: " . print_r($cliente, true) . "</pre>";
        } else {
            echo "<p style='color:red'>❌ Error al obtener: " . $readResult['error'] . "</p>";
        }

        echo "<h2>3. Actualizar Cliente</h2>";
        $updateData = ['nombres' => 'Juan Updated Carlos'];
        $updateResult = $service->update($createdId, $updateData);
        if ($updateResult['success']) {
            echo "<p style='color:green'>✅ Cliente actualizado exitosamente.</p>";
            
            // Verificar la actualización
            $verifyResult = $service->getById($createdId);
            if ($verifyResult['success']) {
                echo "<p>Nombre actualizado a: " . $verifyResult['data']['nombres'] . "</p>";
            }
        } else {
            echo "<p style='color:red'>❌ Error al actualizar: " . $updateResult['error'] . "</p>";
        }
        
        echo "<h2>4. Listar todos los clientes</h2>";
        $listResult = $service->getAll();
        if ($listResult['success']) {
            echo "<p style='color:green'>✅ Total clientes activos: " . count($listResult['data']) . "</p>";
        } else {
            echo "<p style='color:red'>❌ Error al listar: " . $listResult['error'] . "</p>";
        }
        
        echo "<h2>5. Desactivar cliente (soft delete)</h2>";
        $deleteResult = $service->delete($createdId);
        if ($deleteResult['success']) {
            echo "<p style='color:green'>✅ Cliente desactivado correctamente.</p>";
        } else {
            echo "<p style='color:red'>❌ Error al desactivar: " . $deleteResult['error'] . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error fatal: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}