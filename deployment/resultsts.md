[ec2-user@ip-172-31-1-105 ~]$ echo '============================================================'
echo ' ORION FRONTEND - FUENTE DE AUTENTICACION'
echo '============================================================'

echo
echo '===== CONFIG ====='
sudo sed -n '1,300p' \
    /var/www/orion.nebuladet.website/public/app/config.php

echo
echo '===== AUTH ROOT ====='
sudo sed -n '1,500p' \
    /var/www/orion.nebuladet.website/public/auth_root.php

echo
echo '===== SESSION MANAGER ====='
sudo sed -n '1,400p' \
    /var/www/orion.nebuladet.website/public/session_manager.php

echo
echo '===== SEARCH AUTH / DATABASE REFERENCES ====='
sudo grep -RniE \
    'mysql|mysqli|PDO|postgres|supabase|SUPABASE|DATABASE|DB_HOST|DB_NAME|DB_USE    | head -300 \n.nebuladet.website/public/session_manager.php \
============================================================
 ORION FRONTEND - FUENTE DE AUTENTICACION
============================================================

===== CONFIG =====
<?php
declare(strict_types=1);

function frontend_env(string $key, ?string $default=null): ?string {
    $value=getenv($key);
    return ($value===false||$value==='')?$default:$value;
}

return [
    'app_name'=>frontend_env('NEBULA_APP_NAME','Nebula DET'),
    'orion_base_url'=>rtrim((string)frontend_env('ORION_BASE_URL','https://orion.nebuladet.website'),'/'),
    'orion_connect_timeout'=>(int)frontend_env('ORION_CONNECT_TIMEOUT','5'),
    'orion_timeout'=>(int)frontend_env('ORION_TIMEOUT','60'),
];
===== AUTH ROOT =====
<?php
declare(strict_types=1);
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/session_manager.php';
require_once __DIR__ . '/services/OrionClient.php';

function auth_root_attempt_login(string $usuario,string $password): array {
    $email=trim($usuario);
    if($email===''||$password==='') return ['exito'=>false,'mensaje'=>'Usuario o contraseña vacíos'];
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) return ['exito'=>false,'mensaje'=>'Ingrese el correo electrónico de la cuenta.'];
    try {
        $data=(new OrionClient(require __DIR__ . '/app/config.php'))->login($email,$password);
        $user=(array)($data['user']??[]);
        if(empty($data['token'])||empty($data['refresh_token'])||empty($user['id'])) return ['exito'=>false,'mensaje'=>'Orion devolvió una sesión incompleta.'];
        SessionManager::regenerate();
        $tenant=preg_replace('/\D+/','',(string)($user['empresa_nit']??''));
        SessionManager::setMultiple([
            'orion_access_token'=>(string)$data['token'],'orion_refresh_token'=>(string)$data['refresh_token'],
            'orion_expires_at'=>time()+(int)($data['expires_in']??7200),
            'user_root'=>['id'=>$user['id']??null,'usuario'=>$user['usuario']??null,'nombre'=>$user['nombres']??null,'email'=>$user['email']??null],
            'usuario_id'=>$user['id']??null,'usuario'=>$user['usuario']??null,'email'=>$user['email']??null,
            'rol'=>$user['rol']??'usuario','role'=>$user['rol']??'usuario','es_administrador'=>(bool)($user['es_administrador']??false),
            'empresa_uuid'=>$user['empresa_uuid']??null,'empresa_id'=>$user['empresa_uuid']??null,'empresa_nit'=>$tenant,'tenant_id'=>$tenant,
            'empresa_nombre'=>$user['empresa_nombre']??($user['empresa_razon_social']??null),'login_time'=>time()
        ]);
        return ['exito'=>true,'mensaje'=>'Autenticación exitosa'];
    } catch(Throwable $e) { error_log('auth_root_attempt_login: '.$e->getMessage()); return ['exito'=>false,'mensaje'=>$e->getMessage()]; }
}
function auth_root_require_login(): void {
    if(!SessionManager::isAuthenticated()){ require_once __DIR__.'/mapper.php'; RouteMapper::redirect('login.php'); }
}
function auth_root_logout(): void {
    try { $r=(string)SessionManager::get('orion_refresh_token',''); if($r!=='') (new OrionClient(require __DIR__.'/app/config.php'))->logout($r); }
    catch(Throwable $e){ error_log('auth_root_logout: '.$e->getMessage()); }
    finally { SessionManager::destroy(); }
}
===== SESSION MANAGER =====
<?php
/**
 * Session Manager - Gestiona sesiones duales (raíz y tests)
 * Permite que un técnico tenga sesiones independientes sin conflictos
 */

class SessionManager {

    const CONTEXT_ROOT = 'root';
    const CONTEXT_TESTS = 'tests';

    private static $context = null;

    /**
     * Inicializa la sesión y detecta el contexto
     */
    public static function initialize() {
        if (session_status() === PHP_SESSION_NONE) {
            // Asegurar que no haya salida antes de los headers
            if (!headers_sent()) {
                ob_start();
            }
            $secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => $secureCookie,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }

        // Detectar contexto basado en la ruta actual
        self::$context = self::detectContext();

        // Inicializar namespaces de sesión si no existen
        if (empty($_SESSION['_ctx'])) {
            $_SESSION['_ctx'] = self::$context;
        }

        if (empty($_SESSION['_namespaces'])) {
            $_SESSION['_namespaces'] = [
                self::CONTEXT_ROOT => [],
                self::CONTEXT_TESTS => []
            ];
        }

        // Sincronizar el namespace actual hacia el superglobal $_SESSION
        $ctx = self::getContext();
        if (!empty($_SESSION['_namespaces'][$ctx]) && is_array($_SESSION['_namespaces'][$ctx])) {
            foreach ($_SESSION['_namespaces'][$ctx] as $k => $v) {
                // Mantener compatibilidad con código existente que usa $_SESSION directly
                $_SESSION[$k] = $v;
            }
        }

        // Además, si existen valores en $_SESSION (legacy) volcarlos al namespace actual
        foreach ($_SESSION as $k => $v) {
            if ($k === '_namespaces' || $k === '_ctx') continue;
            // No sobreescribir valores ya definidos en el namespace
            if (!isset($_SESSION['_namespaces'][$ctx][$k])) {
                $_SESSION['_namespaces'][$ctx][$k] = $v;
            }
        }
    }

    /**
     * Detecta el contexto actual basado en la ruta
     */
    private static function detectContext() {
        $script_path = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';

        // Normalizar separadores de ruta para compatibilidad entre sistemas
        $script_path = str_replace('\\', '/', $script_path);

        if (stripos($script_path, '/tests/') !== false) {
            return self::CONTEXT_TESTS;
        }

        return self::CONTEXT_ROOT;
    }

    /**
     * Obtiene el contexto actual
     */
    public static function getContext() {
        if (self::$context === null) {
            self::initialize();
        }
        return self::$context;
    }

    /**
     * Establece un valor de sesión en el contexto actual
     */
    public static function set($key, $value) {
        self::initialize();
        $context = self::getContext();

        if (!isset($_SESSION['_namespaces'][$context])) {
            $_SESSION['_namespaces'][$context] = [];
        }

        $_SESSION['_namespaces'][$context][$key] = $value;
        // También mantener sincronizado el superglobal para compatibilidad
        $_SESSION[$key] = $value;
    }

    /**
     * Obtiene un valor de sesión del contexto actual
     */
    public static function get($key, $default = null) {
        self::initialize();
        $context = self::getContext();

        if (isset($_SESSION['_namespaces'][$context][$key])) {
            return $_SESSION['_namespaces'][$context][$key];
        }

        return $default;
    }

    /**
     * Verifica si existe un valor en la sesión del contexto actual
     */
    public static function has($key) {
        self::initialize();
        $context = self::getContext();

        return isset($_SESSION['_namespaces'][$context][$key]);
    }

    /**
     * Elimina un valor de la sesión del contexto actual
     */
    public static function forget($key) {
        self::initialize();
        $context = self::getContext();

        if (isset($_SESSION['_namespaces'][$context][$key])) {
            unset($_SESSION['_namespaces'][$context][$key]);
        }
    }

    /**
     * Obtiene todos los datos de la sesión del contexto actual
     */
    public static function all() {
        self::initialize();
        $context = self::getContext();

        return $_SESSION['_namespaces'][$context] ?? [];
    }

    /**
     * Limpia la sesión del contexto actual
     */
    public static function flush() {
        self::initialize();
        $context = self::getContext();

        $_SESSION['_namespaces'][$context] = [];
    }

    /**
     * Destruye completamente la sesión (ambos contextos)
     */
    public static function destroy() {
        // Limpiar namespaces y datos
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Vaciar array de sesión
            $_SESSION = [];

            // Borrar cookie de sesión si aplica
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params['path'], $params['domain'], $params['secure'], $params['httponly']
                );
            }

            // Destruir la sesión
            session_destroy();
        }

        self::$context = null;
    }

    /**
     * Regenera el ID de sesión para evitar Session Fixation
     */
    public static function regenerate() {
        session_regenerate_id(true);
    }

    /**
     * Establece múltiples valores a la vez
     */
    public static function setMultiple(array $data) {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
    }

    /**
     * Verifica si el usuario está autenticado en el contexto actual
     */
    public static function isAuthenticated() {
        return self::has('usuario_id') || self::has('user_root');
    }

    /**
     * Obtiene información del usuario autenticado
     */
    public static function getUser() {
        if (self::has('user_root')) {
            return self::get('user_root');
        }

        return [
            'id' => self::get('usuario_id'),
            'usuario' => self::get('usuario'),
            'email' => self::get('email'),
            'nombre' => self::get('nombre')
        ];
    }

    /**
     * Obtiene el token CSRF del contexto actual
     */
    public static function getCsrfToken() {
        $token = self::get('csrf_token');
        if (empty($token)) {
            $token = bin2hex(random_bytes(32));
            self::set('csrf_token', $token);
        }
        return $token;
    }

    /**
     * Verifica un token CSRF
     */
    public static function verifyCsrfToken($token) {
        return hash_equals(self::getCsrfToken(), $token);
    }
}

// Inicializar automáticamente
SessionManager::initialize();
?>

===== SEARCH AUTH / DATABASE REFERENCES =====
/var/www/orion.nebuladet.website/public/app/auth.php:3: * File name: app/auth.php
/var/www/orion.nebuladet.website/public/app/auth.php:4: * Route: shared authentication facade
/var/www/orion.nebuladet.website/public/app/auth.php:6: * Related: auth_root.php, session_manager.php
/var/www/orion.nebuladet.website/public/app/auth.php:11:require_once __DIR__ . '/../auth_root.php';
/var/www/orion.nebuladet.website/public/app/auth.php:13:final class FrontendAuth {
/var/www/orion.nebuladet.website/public/app/auth.php:14:    public static function requireLogin(): void {
/var/www/orion.nebuladet.website/public/app/auth.php:15:        if (!SessionManager::isAuthenticated()) {
/var/www/orion.nebuladet.website/public/app/auth.php:17:            RouteMapper::redirect('login.php');
/var/www/orion.nebuladet.website/public/app/auth.php:21:    public static function isAuthenticated(): bool {
/var/www/orion.nebuladet.website/public/app/auth.php:22:        return SessionManager::isAuthenticated();
/var/www/orion.nebuladet.website/public/app/auth.php:26:        auth_root_logout();
/var/www/orion.nebuladet.website/public/app/bootstrap.php:19:require_once __DIR__ . '/../auth_root.php';
/var/www/orion.nebuladet.website/public/app/tenant.php:13:        if (!SessionManager::isAuthenticated()) {
/var/www/orion.nebuladet.website/public/auth_root.php:7:function auth_root_attempt_login(string $usuario,string $password): array {
/var/www/orion.nebuladet.website/public/auth_root.php:12:        $data=(new OrionClient(require __DIR__ . '/app/config.php'))->login($email,$password);
/var/www/orion.nebuladet.website/public/auth_root.php:24:            'empresa_nombre'=>$user['empresa_nombre']??($user['empresa_razon_social']??null),'login_time'=>time()
/var/www/orion.nebuladet.website/public/auth_root.php:27:    } catch(Throwable $e) { error_log('auth_root_attempt_login: '.$e->getMessage()); return ['exito'=>false,'mensaje'=>$e->getMessage()]; }
/var/www/orion.nebuladet.website/public/auth_root.php:29:function auth_root_require_login(): void {
/var/www/orion.nebuladet.website/public/auth_root.php:30:    if(!SessionManager::isAuthenticated()){ require_once __DIR__.'/mapper.php'; RouteMapper::redirect('login.php'); }
/var/www/orion.nebuladet.website/public/auth_root.php:32:function auth_root_logout(): void {
/var/www/orion.nebuladet.website/public/auth_root.php:34:    catch(Throwable $e){ error_log('auth_root_logout: '.$e->getMessage()); }
/var/www/orion.nebuladet.website/public/session_manager.php:210:    public static function isAuthenticated() {
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$ echo '===== POSIBLES FUENTES DE USUARIOS ====='

sudo grep -RniE \
    'users|usuarios|user_root|email|password_hash|password|tenant|empresa' \
    /var/www/orion.nebuladet.website/public \
    --include='*.php' \
    2>/dev/null \
    | head -400
===== POSIBLES FUENTES DE USUARIOS =====
/var/www/orion.nebuladet.website/public/forms/document.php:5: * Function: Formularios humanos para FE/CCF, sin edición del tenant fiscal.
/var/www/orion.nebuladet.website/public/forms/document.php:10:require_once __DIR__ . '/../app/tenant.php';
/var/www/orion.nebuladet.website/public/forms/document.php:13:$tenant=TenantContext::require();
/var/www/orion.nebuladet.website/public/forms/document.php:33:<strong>Empresa activa:</strong> <?=htmlspecialchars((string)($tenant['empresa_nombre']??'Empresa'))?>
/var/www/orion.nebuladet.website/public/forms/document.php:34:<br><strong>Tenant fiscal:</strong> <?=htmlspecialchars((string)$tenant['tenant_id'])?>
/var/www/orion.nebuladet.website/public/forms/document.php:72:<label>Correo<input id="receptorCorreo" type="email"></label>
/var/www/orion.nebuladet.website/public/forms/form_definitions.php:12:  ['name'=>'receptor_correo','label'=>'Correo del cliente','path'=>'receptor.correo','required'=>false,'type'=>'email'],
/var/www/orion.nebuladet.website/public/forms/nota.php:6: * Security: tenant fiscal y emisor se toman exclusivamente de sesión.
/var/www/orion.nebuladet.website/public/forms/nota.php:11:require_once __DIR__ . '/../app/tenant.php';
/var/www/orion.nebuladet.website/public/forms/nota.php:14:$tenant=TenantContext::require();
/var/www/orion.nebuladet.website/public/forms/nota.php:34:<div class="notice"><strong>Empresa activa:</strong> <?=htmlspecialchars((string)($tenant['empresa_nombre']??'Empresa'))?><br><strong>Tenant fiscal:</strong> <?=htmlspecialchars((string)$tenant['tenant_id'])?></div>
/var/www/orion.nebuladet.website/public/forms/nota.php:74:<label>Correo<input id="receptorCorreo" type="email"></label>
/var/www/orion.nebuladet.website/public/scripts/register_process.php:4:$data=['usuario'=>trim((string)($_POST['usuario']??'')),'email'=>strtolower(trim((string)($_POST['email']??''))),'nombres'=>trim((string)($_POST['nombres']??'')),'apellidos'=>trim((string)($_POST['apellidos']??'')),'nit'=>trim((string)($_POST['nit']??'')),'empresa_nit'=>trim((string)($_POST['empresa_nit']??'')),'empresa_nrc'=>trim((string)($_POST['empresa_nrc']??'')),'empresa_nombre'=>trim((string)($_POST['empresa_nombre']??'')),'password'=>(string)($_POST['password']??'')];
/var/www/orion.nebuladet.website/public/services/DteEngine.php:7: * Security: emisor.nit siempre procede del tenant fiscal autenticado.
/var/www/orion.nebuladet.website/public/services/DteEngine.php:13:    public function build(string $type, array $input, array $tenant, array $schema): array {
/var/www/orion.nebuladet.website/public/services/DteEngine.php:18:        $nit = preg_replace('/\D+/', '', (string)($tenant['tenant_id'] ?? ''));
/var/www/orion.nebuladet.website/public/services/DteEngine.php:20:            throw new RuntimeException('Tenant fiscal inválido: se requiere NIT de 14 dígitos.');
/var/www/orion.nebuladet.website/public/services/DteEngine.php:27:        $emisor = $this->issuer($tenant, $nit);
/var/www/orion.nebuladet.website/public/services/DteEngine.php:78:    private function issuer(array $tenant, string $nit): array {
/var/www/orion.nebuladet.website/public/services/DteEngine.php:80:            'nrc'=>'empresa_nrc',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:81:            'nombre'=>'empresa_nombre',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:82:            'codActividad'=>'empresa_cod_actividad',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:83:            'descActividad'=>'empresa_desc_actividad',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:84:            'nombreComercial'=>'empresa_nombre_comercial',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:85:            'telefono'=>'empresa_telefono',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:86:            'correo'=>'empresa_correo',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:87:            'codEstable'=>'empresa_cod_estable',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:88:            'codPuntoVenta'=>'empresa_cod_punto_venta',
/var/www/orion.nebuladet.website/public/services/DteEngine.php:93:            $value = $tenant[$sessionKey] ?? SessionManager::get($sessionKey);
/var/www/orion.nebuladet.website/public/services/DteEngine.php:94:            if ($json === 'nombreComercial' && ($value === '' || $value === null)) $value = $tenant['empresa_nombre'] ?? null;
/var/www/orion.nebuladet.website/public/services/DteEngine.php:99:            'departamento'=>$tenant['empresa_departamento'] ?? SessionManager::get('empresa_departamento'),
/var/www/orion.nebuladet.website/public/services/DteEngine.php:100:            'municipio'=>$tenant['empresa_municipio'] ?? SessionManager::get('empresa_municipio'),
/var/www/orion.nebuladet.website/public/services/DteEngine.php:101:            'distrito'=>$tenant['empresa_distrito'] ?? SessionManager::get('empresa_distrito'),
/var/www/orion.nebuladet.website/public/services/DteEngine.php:102:            'complemento'=>$tenant['empresa_direccion'] ?? SessionManager::get('empresa_direccion'),
/var/www/orion.nebuladet.website/public/services/OrionClient.php:6:    public function login(string $email,string $password):array{return $this->request('POST','/api/v1/auth/login',['email'=>$email,'password'=>$password],false);}
/var/www/orion.nebuladet.website/public/admin_dashboard.php:13:?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Nebula DET — Administración</title><link rel="stylesheet" href="main.css"></head><body><main class="container" style="max-width:1000px;margin:60px auto;padding:24px"><h1>Administración Nebula DET</h1><p>Sesión: <strong><?=htmlspecialchars((string)($user['usuario']??''))?></strong></p><p>El panel administrativo está separado del flujo de emisión multitenant.</p><a href="logout.php">Cerrar sesión</a></main></body></html>
/var/www/orion.nebuladet.website/public/auth_root.php:7:function auth_root_attempt_login(string $usuario,string $password): array {
/var/www/orion.nebuladet.website/public/auth_root.php:8:    $email=trim($usuario);
/var/www/orion.nebuladet.website/public/auth_root.php:9:    if($email===''||$password==='') return ['exito'=>false,'mensaje'=>'Usuario o contraseña vacíos'];
/var/www/orion.nebuladet.website/public/auth_root.php:10:    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) return ['exito'=>false,'mensaje'=>'Ingrese el correo electrónico de la cuenta.'];
/var/www/orion.nebuladet.website/public/auth_root.php:12:        $data=(new OrionClient(require __DIR__ . '/app/config.php'))->login($email,$password);
/var/www/orion.nebuladet.website/public/auth_root.php:16:        $tenant=preg_replace('/\D+/','',(string)($user['empresa_nit']??''));
/var/www/orion.nebuladet.website/public/auth_root.php:20:            'user_root'=>['id'=>$user['id']??null,'usuario'=>$user['usuario']??null,'nombre'=>$user['nombres']??null,'email'=>$user['email']??null],
/var/www/orion.nebuladet.website/public/auth_root.php:21:            'usuario_id'=>$user['id']??null,'usuario'=>$user['usuario']??null,'email'=>$user['email']??null,
/var/www/orion.nebuladet.website/public/auth_root.php:23:            'empresa_uuid'=>$user['empresa_uuid']??null,'empresa_id'=>$user['empresa_uuid']??null,'empresa_nit'=>$tenant,'tenant_id'=>$tenant,
/var/www/orion.nebuladet.website/public/auth_root.php:24:            'empresa_nombre'=>$user['empresa_nombre']??($user['empresa_razon_social']??null),'login_time'=>time()
/var/www/orion.nebuladet.website/public/credito_fiscal.php:43:$formEmisorCorreo = trim((string)($config['DTE_EMISOR_CORREO'] ?? ($config['DTE_EMISOR_EMAIL'] ?? ($config['SMTP_EMAIL'] ?? ($emp_email ?? '')))));
/var/www/orion.nebuladet.website/public/credito_fiscal.php:69:    $sessionEmail = trim((string) SessionManager::get('email', ''));
/var/www/orion.nebuladet.website/public/credito_fiscal.php:71:    if ($sessionEmail !== '') {
/var/www/orion.nebuladet.website/public/credito_fiscal.php:73:            $userStmt = $pdo->prepare('SELECT id::text FROM auth.users WHERE email = ? LIMIT 1');
/var/www/orion.nebuladet.website/public/credito_fiscal.php:74:            $userStmt->execute([$sessionEmail]);
/var/www/orion.nebuladet.website/public/credito_fiscal.php:75:            $supabaseUserId = $userStmt->fetchColumn() ?: null;
/var/www/orion.nebuladet.website/public/credito_fiscal.php:77:            error_log('No se pudo resolver auth.users para catalogos CCF: ' . $userLookupError->getMessage());
/var/www/orion.nebuladet.website/public/credito_fiscal.php:398:                            <input class="form-control" name="receptor[nombre]" placeholder="Nombre completo de la empresa" required>
/var/www/orion.nebuladet.website/public/credito_fiscal.php:486:                            <input type="email" class="form-control" name="receptor[correo]" placeholder="cliente@correo.com" required>
/var/www/orion.nebuladet.website/public/factura_pro.php:476:                            <input type="email" class="form-control" name="receptor[correo]" value="503software@gmail.com">
/var/www/orion.nebuladet.website/public/factura_pro.php:492:            $users = SupabaseConnector::consultar("dte_usuarios?id=eq.$userId");
/var/www/orion.nebuladet.website/public/factura_pro.php:493:            $userData = $users[0] ?? null;
/var/www/orion.nebuladet.website/public/factura_pro.php:547:                                            <input type="email" class="form-control" name="email" value="<?= $userData['email'] ?? '' ?>" required>
/var/www/orion.nebuladet.website/public/factura_pro.php:555:                                            <input type="text" class="form-control opacity-50" value="<?= $userData['empresa_nrc'] ?? '' ?>" readonly>
/var/www/orion.nebuladet.website/public/factura_pro.php:564:                                            <input type="password" class="form-control" name="new_password" placeholder="Dejar vaco si no cambia">
/var/www/orion.nebuladet.website/public/factura_pro.php:568:                                            <input type="password" class="form-control" name="confirm_password" placeholder="Repita contrasea">
/var/www/orion.nebuladet.website/public/factura_pro.php:1238:        const clientEmail = document.querySelector('input[name="receptor[correo]"]');
/var/www/orion.nebuladet.website/public/factura_pro.php:1241:        if(clientName) clientName.value = "Empresa de Prueba S.A. de C.V.";
/var/www/orion.nebuladet.website/public/factura_pro.php:1242:        if(clientEmail) clientEmail.value = "demo@nebula.com";
/var/www/orion.nebuladet.website/public/forgot_password.php:3: * File name: forgot_password.php
/var/www/orion.nebuladet.website/public/forgot_password.php:4: * Route: /forgot_password.php
/var/www/orion.nebuladet.website/public/forgot_password.php:14:<form id="recover"><input id="email" type="email" required autocomplete="email"><button>Enviar enlace</button></form>
/var/www/orion.nebuladet.website/public/forgot_password.php:20: e.preventDefault();const email=document.getElementById('email').value.trim();
/var/www/orion.nebuladet.website/public/forgot_password.php:21: const {error}=await sb.auth.resetPasswordForEmail(email,{redirectTo:new URL('reset_password.php',location.href).href});
/var/www/orion.nebuladet.website/public/formulario_anulacion.php:459:                            <label>Nombre Empresa <span class="required">*</span></label>
/var/www/orion.nebuladet.website/public/formulario_anulacion.php:461:                                   placeholder="Mi Empresa S.A." required>
/var/www/orion.nebuladet.website/public/formulario_anulacion.php:494:                            <input type="email" name="correo_emisor"
/var/www/orion.nebuladet.website/public/formulario_anulacion.php:495:                                   placeholder="contacto@miempresa.sv" required>
/var/www/orion.nebuladet.website/public/formulario_anulacion.php:669:            form.nombre_emisor.value = 'Mi Empresa S.A.';
/var/www/orion.nebuladet.website/public/formulario_anulacion.php:674:            form.correo_emisor.value = 'contacto@miempresa.sv';
/var/www/orion.nebuladet.website/public/login.php:19:        $res = auth_root_attempt_login((string)($_POST['usuario'] ?? ''), (string)($_POST['password'] ?? ''));
/var/www/orion.nebuladet.website/public/login.php:36:<label>Contraseña<input type="password" name="password" autocomplete="current-password" required></label>
/var/www/orion.nebuladet.website/public/login.php:39:<p><a href="forgot_password.php">¿Olvidaste tu contraseña?</a> · <a href="register.php">Crear cuenta</a></p>
/var/www/orion.nebuladet.website/public/register.php:6: * Security: sesión/CSRF server-side; tenant nunca es seleccionado por el navegador.
/var/www/orion.nebuladet.website/public/register.php:129:                    <input type="email" name="email" class="form-control" placeholder="usuario@empresa.com" required>
/var/www/orion.nebuladet.website/public/register.php:144:                    <label class="compact-label">NIT Empresa</label>
/var/www/orion.nebuladet.website/public/register.php:145:                    <input type="text" name="empresa_nit" class="form-control" placeholder="NIT de la empresa" required>
/var/www/orion.nebuladet.website/public/register.php:148:                    <label class="compact-label">NRC Empresa</label>
/var/www/orion.nebuladet.website/public/register.php:149:                    <input type="text" name="empresa_nrc" class="form-control" placeholder="Opcional">
/var/www/orion.nebuladet.website/public/register.php:153:                    <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" minlength="8" required>
/var/www/orion.nebuladet.website/public/reset_password.php:7:<form id="reset"><input id="password" type="password" minlength="8" required autocomplete="new-password" placeholder="Nueva contraseña"><button>Actualizar contraseña</button></form><p id="msg"></p>
/var/www/orion.nebuladet.website/public/reset_password.php:13: const {error}=await sb.auth.updateUser({password:document.getElementById('password').value});
/var/www/orion.nebuladet.website/public/session_manager.php:211:        return self::has('usuario_id') || self::has('user_root');
/var/www/orion.nebuladet.website/public/session_manager.php:218:        if (self::has('user_root')) {
/var/www/orion.nebuladet.website/public/session_manager.php:219:            return self::get('user_root');
/var/www/orion.nebuladet.website/public/session_manager.php:225:            'email' => self::get('email'),
/var/www/orion.nebuladet.website/public/start.php:5: * Function: Selector principal de documentos para el tenant autenticado.
/var/www/orion.nebuladet.website/public/start.php:6: * Security: tenant/usuario vienen exclusivamente de sesión.
/var/www/orion.nebuladet.website/public/start.php:11:require_once __DIR__ . '/app/tenant.php';
/var/www/orion.nebuladet.website/public/start.php:24:$tenant = TenantContext::require();
/var/www/orion.nebuladet.website/public/start.php:57:<p>Empresa activa: <strong><?=htmlspecialchars((string)($tenant['empresa_nombre'] ?? $tenant['empresa_nit'] ?? 'Empresa'))?></strong></p>
/var/www/orion.nebuladet.website/public/api/emit.php:6: * Security: tenant fiscal = NIT emisor de sesión; nunca se acepta desde cliente.
/var/www/orion.nebuladet.website/public/api/emit.php:11:require_once __DIR__ . '/../app/tenant.php';
/var/www/orion.nebuladet.website/public/api/emit.php:16:$tenant = TenantContext::require();
/var/www/orion.nebuladet.website/public/api/emit.php:21:TenantContext::assertRequestHasNoTenantOverride($input);
/var/www/orion.nebuladet.website/public/api/emit.php:31:        $document = $engine->build($type, $input['form'], $tenant, $schema);
/var/www/orion.nebuladet.website/public/api/emit.php:35:         * si su emisor.nit coincide exactamente con el tenant fiscal.
/var/www/orion.nebuladet.website/public/api/emit.php:38:        TenantContext::assertEmitterNit($document, (string)$tenant['tenant_id']);
/var/www/orion.nebuladet.website/public/api/emit.php:49:    TenantContext::assertEmitterNit($document, (string)$tenant['tenant_id']);
/var/www/orion.nebuladet.website/public/api/schema.php:9:require_once __DIR__ . '/../app/tenant.php';
/var/www/orion.nebuladet.website/public/api/schema.php:12:TenantContext::require();
/var/www/orion.nebuladet.website/public/app/auth.php:30:        return (array)SessionManager::get('user_root', []);
/var/www/orion.nebuladet.website/public/app/bootstrap.php:6: * Related: SessionManager, TenantContext, SchemaService
/var/www/orion.nebuladet.website/public/app/tenant.php:4: * File name: app/tenant.php
/var/www/orion.nebuladet.website/public/app/tenant.php:5: * Route: tenant context
/var/www/orion.nebuladet.website/public/app/tenant.php:6: * Function: Contexto fiscal multitenant inmutable durante la petición.
/var/www/orion.nebuladet.website/public/app/tenant.php:7: * Rule: tenant_id = NIT fiscal del emisor.
/var/www/orion.nebuladet.website/public/app/tenant.php:8: * Security: nunca acepta tenant_id/empresa_id/empresa_uuid/empresa_nit desde GET/POST/JSON.
/var/www/orion.nebuladet.website/public/app/tenant.php:11:final class TenantContext {
/var/www/orion.nebuladet.website/public/app/tenant.php:17:        $nit = preg_replace('/\D+/', '', (string)SessionManager::get('empresa_nit',''));
/var/www/orion.nebuladet.website/public/app/tenant.php:19:            Security::json(['success'=>false,'error'=>'El tenant fiscal no tiene un NIT de emisor válido'],403);
/var/www/orion.nebuladet.website/public/app/tenant.php:23:         * Identidad fiscal inmutable del tenant:
/var/www/orion.nebuladet.website/public/app/tenant.php:24:         * tenant_id == NIT del emisor.
/var/www/orion.nebuladet.website/public/app/tenant.php:26:         * empresa_uuid / empresa_id siguen siendo identificadores internos
/var/www/orion.nebuladet.website/public/app/tenant.php:27:         * de base de datos, pero nunca sustituyen al tenant fiscal.
/var/www/orion.nebuladet.website/public/app/tenant.php:30:            'tenant_id'    => $nit,
/var/www/orion.nebuladet.website/public/app/tenant.php:31:            'empresa_nit'  => $nit,
/var/www/orion.nebuladet.website/public/app/tenant.php:32:            'empresa_uuid' => SessionManager::get('empresa_uuid'),
/var/www/orion.nebuladet.website/public/app/tenant.php:33:            'empresa_id'   => SessionManager::get('empresa_id'),
/var/www/orion.nebuladet.website/public/app/tenant.php:34:            'empresa_nombre'=>SessionManager::get('empresa_nombre'),
/var/www/orion.nebuladet.website/public/app/tenant.php:37:            'empresa_nrc'=>SessionManager::get('empresa_nrc'),
/var/www/orion.nebuladet.website/public/app/tenant.php:38:            'empresa_cod_actividad'=>SessionManager::get('empresa_cod_actividad'),
/var/www/orion.nebuladet.website/public/app/tenant.php:39:            'empresa_desc_actividad'=>SessionManager::get('empresa_desc_actividad'),
/var/www/orion.nebuladet.website/public/app/tenant.php:40:            'empresa_nombre_comercial'=>SessionManager::get('empresa_nombre_comercial'),
/var/www/orion.nebuladet.website/public/app/tenant.php:41:            'empresa_telefono'=>SessionManager::get('empresa_telefono'),
/var/www/orion.nebuladet.website/public/app/tenant.php:42:            'empresa_correo'=>SessionManager::get('empresa_correo'),
/var/www/orion.nebuladet.website/public/app/tenant.php:43:            'empresa_departamento'=>SessionManager::get('empresa_departamento'),
/var/www/orion.nebuladet.website/public/app/tenant.php:44:            'empresa_municipio'=>SessionManager::get('empresa_municipio'),
/var/www/orion.nebuladet.website/public/app/tenant.php:45:            'empresa_distrito'=>SessionManager::get('empresa_distrito'),
/var/www/orion.nebuladet.website/public/app/tenant.php:46:            'empresa_direccion'=>SessionManager::get('empresa_direccion'),
/var/www/orion.nebuladet.website/public/app/tenant.php:47:            'empresa_cod_estable'=>SessionManager::get('empresa_cod_estable'),
/var/www/orion.nebuladet.website/public/app/tenant.php:48:            'empresa_cod_punto_venta'=>SessionManager::get('empresa_cod_punto_venta'),
/var/www/orion.nebuladet.website/public/app/tenant.php:52:    public static function assertRequestHasNoTenantOverride(array $input): void {
/var/www/orion.nebuladet.website/public/app/tenant.php:53:        foreach (['tenant_id','empresa_id','empresa_uuid','empresa_nit'] as $key) {
/var/www/orion.nebuladet.website/public/app/tenant.php:57:                    'error'=>'No está permitido enviar contexto de empresa desde el cliente'
/var/www/orion.nebuladet.website/public/app/tenant.php:63:    public static function assertEmitterNit(array $document, string $tenantNit): void {
/var/www/orion.nebuladet.website/public/app/tenant.php:65:        if ($documentNit === '' || $documentNit !== $tenantNit) {
/var/www/orion.nebuladet.website/public/app/tenant.php:68:                'error'=>'El NIT del emisor no coincide con el tenant fiscal autenticado'
/var/www/orion.nebuladet.website/public/app/tenant.php:76:            'tenant_id'=>$t['tenant_id'],
/var/www/orion.nebuladet.website/public/app/tenant.php:77:            'empresa_id'=>$t['empresa_id'],
/var/www/orion.nebuladet.website/public/app/tenant.php:78:            'empresa_uuid'=>$t['empresa_uuid'],
/var/www/orion.nebuladet.website/public/app/tenant.php:79:            'empresa_nit'=>$t['empresa_nit'],
/var/www/orion.nebuladet.website/public/app/tenant.php:80:            'empresa_nombre'=>$t['empresa_nombre'],
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$
[ec2-user@ip-172-31-1-105 ~]$ curl -k -I https://orion.nebuladet.website/main.css
HTTP/1.1 200 OK
Server: nginx/1.28.2
Date: Wed, 23 Sep 2026 18:13:19 GMT
Content-Type: text/css
Content-Length: 6248
Last-Modified: Sun, 20 Sep 2026 04:34:10 GMT
Connection: keep-alive
ETag: "6aaf6242-1868"
Accept-Ranges: bytes

[ec2-user@ip-172-31-1-105 ~]$ curl -k https://orion.nebuladet.website/main.css | head -40
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100  6248 100  6248   0     0 926727     0  --:--:-- --:--::root {:--     0
            --primary-gradient: linear-gradient(135deg, #6e8efb, #a777e3);
            --secondary-gradient: linear-gradient(135deg, #a777e3, #6e8efb);
            --dark-purple: #4a2c82;
            --light-purple: #a67df2;
            --shadow-lg: 0 15px 30px rgba(0, 0, 0, 0.2);
            --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        body {
            background: url('https://source.unsplash.com/random/1920x1080/?abstract,blur') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
           - background: rgba(0, 0, 0, 0.6);
-            z-index: -1;
         }
-
-        .modal-overlay {
:            position: fixed;
-            top: 0;
-            left: 0;
:            right: 0;
-            bottom: 0;
-            background: rgba(0, 0, 0, 0.7);
  1016k
curl: Failed writing body
[ec2-user@ip-172-31-1-105 ~]$




