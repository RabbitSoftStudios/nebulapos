@ip-172-31-1-105 kernell]$ cd /home/ec2-user/orion/kernell

curl -k -i --max-time 15 \
  --resolve orion.nebuladet.website:443:127.0.0.1 \
  -X POST \
  -H 'Content-Type: application/json' \
  -d '{"email":"garantechsv@gmail.com","password":"@Thundercats4310"}' \
  https://orion.nebuladet.website/api/v1/auth/login
HTTP/1.1 500 Internal Server Error
Server: nginx/1.28.2
Date: Wed, 23 Sep 2026 23:26:33 GMT
Content-Type: application/json
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/8.5.3

{
    "success": false,
    "message": "No fue posible procesar la autenticación.",
    "timestamp": "2026-09-23T23:26:33+00:00",
    "errors": []
}[ec2-user@ip-172-31-1-105 kernell]$cd /home/ec2-user/orion/kernelll

echo '===== ORION TOKEN TABLE ====='
php -r '
require "vendor/autoload.php";

$dsn=trim((string)env("ORION_AUTH_DB_DSN",""));
echo "DSN_CONFIGURED=" . ($dsn !== "" ? "YES" : "NO") . PHP_EOL;
echo "JWT_SECRET=" . (trim((string)env("JWT_SECRET","")) !== "" ? "YES" : "NO") . PHP_EOL;
'

echo
echo '===== TOKEN TABLE TEST ====='
===== ORION TOKEN TABLE =====
DSN_CONFIGURED=NO
JWT_SECRET=NO

===== TOKEN TABLE TEST =====
[ec2-user@ip-172-31-1-105 kernell]$ sudo tail -n 100 /var/log/php-fpm/www-error.log
[23-Sep-2026 19:43:02 UTC] TOKEN_OBTAINED_OK: Expira en 2026-09-24 19:43:02
[23-Sep-2026 19:43:02 UTC] PHP Warning:  file_put_contents(/var/www/nebuladet.website/public/clients/fagustin/signer/axelcrashed_debug.log): Failed to open stream: Permission denied in /var/www/nebuladet.website/public/clients/fagustin/signer/signer_goes.php on line 54
[23-Sep-2026 19:43:03 UTC] PHP Deprecated:  Function curl_close() is deprecated since 8.5, as it has no effect since PHP 8.0 in /var/www/nebuladet.website/public/clients/fagustin/signer/curl_client.php on line 42
[23-Sep-2026 19:43:03 UTC] PHP Warning:  file_put_contents(/var/www/nebuladet.website/public/clients/fagustin/signer/axelcrashed_debug.log): Failed to open stream: Permission denied in /var/www/nebuladet.website/public/clients/fagustin/signer/signer_goes.php on line 54
[23-Sep-2026 19:43:03 UTC] PHP Warning:  file_put_contents(/var/www/nebuladet.website/public/clients/fagustin/signer/axelcrashed_debug.log): Failed to open stream: Permission denied in /var/www/nebuladet.website/public/clients/fagustin/signer/signer_goes.php on line 54
[23-Sep-2026 19:43:03 UTC] MH_NO_CONFIRM [DCFA665D-E4E1-4C41-BB41-5A4D2B6C80C3]: {"success":false,"estado":"RECHAZADO","sello":null,"codigoGeneracion":"DCFA665D-E4E1-4C41-BB41-5A4D2B6C80C3","codigoMsg":"020","descripcionMsg":"[resumen.totalIva] CALCULO INCORRECTO","observaciones":null,"respuesta":{"version":2,"ambiente":"01","versionApp":2,"estado":"RECHAZADO","codigoGeneracion":"DCFA665D-E4E1-4C41-BB41-5A4D2B6C80C3","selloRecibido":null,"fhProcesamiento":"23\/09\/2026 13:43:03","clasificaMsg":"16","codigoMsg":"020","descripcionMsg":"[resumen.totalIva] CALCULO INCORRECTO","observaciones":[]}}
[23-Sep-2026 19:43:03 UTC] DB_SYNC_OK [DCFA665D-E4E1-4C41-BB41-5A4D2B6C80C3]
[23-Sep-2026 15:24:25 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 15:24:25 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 15:24:25 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 15:46:41 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 15:46:41 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 15:46:41 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 15:52:08 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 15:52:08 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 15:52:08 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 15:52:22 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 15:52:22 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 15:52:22 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 16:03:59 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 16:03:59 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 16:03:59 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 16:04:11 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 16:04:11 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 16:04:11 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 16:25:16 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 16:25:16 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 16:25:16 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 17:02:44 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 17:02:44 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 17:02:44 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 17:02:44 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 17:02:44 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 17:02:44 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 17:02:44 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 17:02:44 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 17:02:44 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 22:10:41 UTC] Orion auth login: Class "App\Services\Auth\LoginService" not found
[23-Sep-2026 17:15:44 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 17:15:44 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 17:15:44 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[23-Sep-2026 22:20:42 UTC] Orion auth login: Class "App\Services\Auth\LoginService" not found
[23-Sep-2026 22:24:19 UTC] Orion auth login: Class "App\Services\Auth\LoginService" not found
[23-Sep-2026 22:34:10 UTC] Orion auth login: Class "App\Services\Auth\LoginService" not found
[23-Sep-2026 22:37:07 UTC] Orion auth login: Class "App\Services\Auth\LoginService" not found
[23-Sep-2026 23:11:18 UTC] Orion auth login: SQLSTATE[23503]: Foreign key violation: 7 ERROR:  insert or update on table "orion_token" violates foreign key constraint "orion_token_user_id_fkey"
DETAIL:  Key (user_id)=(36cdfec0-6d0d-4468-9b80-c9c9391158ae) is not present in table "users".
[23-Sep-2026 23:26:33 UTC] Orion auth login: SQLSTATE[23503]: Foreign key violation: 7 ERROR:  insert or update on table "orion_token" violates foreign key constraint "orion_token_user_id_fkey"
DETAIL:  Key (user_id)=(fb0dc95b-06f2-4889-995c-f4d2b4465de2) is not present in table "users".
[23-Sep-2026 18:28:10 America/Bogota] Database connection failed: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
[23-Sep-2026 18:28:10 America/Bogota] DSN attempted: mysql:host=localhost;port=3306;dbname=myts_universalstore;charset=utf8mb4
[23-Sep-2026 18:28:10 America/Bogota] PHP Fatal error:  Uncaught Error: Class "StoreFramework\RouterAdvance" not found in /var/www/agromarket.nebuladet.website/core/framework/Router.php:24
Stack trace:
#0 /var/www/agromarket.nebuladet.website/index.php(152): StoreFramework\Router->__construct()
#1 {main}
  thrown in /var/www/agromarket.nebuladet.website/core/framework/Router.php on line 24
[ec2-user@ip-172-31-1-105 kernell]$ sudo journalctl -u php-fpm --since "5 minutes ago" --no-pager
-- No entries --
[ec2-user@ip-172-31-1-105 kernell]$
