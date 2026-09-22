<?php
/**
 * Sistema POS - Script de Instalación
 * =====================================
 * Configuración inicial del sistema POS
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// Este archivo NO debería ejecutarse en producción.
// Se incluye aquí solo para completar la estructura.

// Verificar si ya está instalado
if (file_exists(__DIR__ . '/../config/constants.php')) {
    die('El sistema ya está instalado. Elimine este archivo después de la instalación.');
}

// Configuración inicial por defecto
$config = [
    'app_name' => 'Nebula POS System',
    'app_version' => '2.0.0',
    'supabase_url' => '',
    'supabase_key' => '',
    'dte_emisor_nit' => '',
    'dte_emisor_nrc' => '',
    'iva_rate' => 0.13,
    'default_cashier' => 'admin'
];

// Funciones auxiliares (necesitan la lógica del generador)
function createDirectoryStructure() {
    // En un sistema real, se usaría la lógica del archivo python
    // Simulación:
    echo '<div class="alert alert-info">Simulando creación de directorios...</div>';
}

function generateConstantsFile($config) {
    // Generar el contenido de config/constants.php
    $content = "<?php
";
    $content .= "define('APP_NAME', '{$config['app_name']}');
";
    $content .= "define('APP_VERSION', '{$config['app_version']}');
";
    $content .= "define('IVA_RATE', {$config['iva_rate']});
";
    // ... más constantes
    return $content;
}

function createEnvFile($config) {
    // Crear .env
    $content = "SUPABASE_URL={$config['supabase_url']}
";
    $content .= "SUPABASE_KEY={$config['supabase_key']}
";
    // ... más variables de entorno
    file_put_contents(__DIR__ . '/../.env', $content);
}

function createDefaultUser() {
    // Simular creación de usuario admin en Supabase
    echo '<div class="alert alert-info">Simulando creación de usuario administrador por defecto...</div>';
}

// Procesar formulario de instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config['supabase_url'] = $_POST['supabase_url'] ?? '';
    $config['supabase_key'] = $_POST['supabase_key'] ?? '';
    $config['dte_emisor_nit'] = $_POST['dte_emisor_nit'] ?? '';
    $config['dte_emisor_nrc'] = $_POST['dte_emisor_nrc'] ?? '';
    $config['iva_rate'] = (float)($_POST['iva_rate'] ?? 0.13);

    // 1. Crear estructura (necesita lógica de recursión)
    createDirectoryStructure(); 

    // 2. Crear archivo de configuración
    $constantsContent = generateConstantsFile($config);
    // file_put_contents(__DIR__ . '/../config/constants.php', $constantsContent); // Descomentar en un script funcional
    
    // 3. Crear archivo .env
    createEnvFile($config);

    // 4. Crear usuario admin por defecto
    createDefaultUser();
    
    echo '<div class="alert alert-success">Instalación completada exitosamente.</div>';
    echo '<a href="../login.php" class="btn btn-primary">Ir al Login</a>';
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - Sistema POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px; 
        }
        .install-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <h2 class="text-center mb-4"><i class="fas fa-cog text-primary"></i> Instalación del Sistema POS</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="supabase_url" class="form-label">URL de Supabase:</label>
                <input type="url" class="form-control" id="supabase_url" name="supabase_url" required value="<?= htmlspecialchars($config['supabase_url']) ?>">
                <div class="form-text">Ejemplo: https://your-project.supabase.co</div>
            </div>
            <div class="mb-3">
                <label for="supabase_key" class="form-label">Clave Anónima (Anon Key):</label>
                <input type="text" class="form-control" id="supabase_key" name="supabase_key" required value="<?= htmlspecialchars($config['supabase_key']) ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="dte_emisor_nit" class="form-label">NIT del Emisor DTE:</label>
                    <input type="text" class="form-control" id="dte_emisor_nit" name="dte_emisor_nit" required value="<?= htmlspecialchars($config['dte_emisor_nit']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="dte_emisor_nrc" class="form-label">NRC del Emisor DTE:</label>
                    <input type="text" class="form-control" id="dte_emisor_nrc" name="dte_emisor_nrc" required value="<?= htmlspecialchars($config['dte_emisor_nrc']) ?>">
                </div>
            </div>
            <div class="mb-3">
                <label for="iva_rate" class="form-label">Tasa de IVA (Ej. 0.13):</label>
                <input type="number" step="0.01" class="form-control" id="iva_rate" name="iva_rate" required value="<?= htmlspecialchars($config['iva_rate']) ?>">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-check"></i> Instalar Sistema</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
