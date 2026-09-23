AIO_SERVICE]$ echo '===== PHP-FPM LOG ====='
sudo journalctl -u php-fpm --since "10 minutes ago" --no-pager \
  | tail -120

echo
echo '===== NGINX ERROR LOG ====='
sudo tail -120 /var/log/nginx/error.log

echo
echo '===== ORION LOGS ====='
sudo find /home/ec2-user/orion/kernell/storage/logs \
  -type f -maxdepth 1 -printf '%TY-%Tm-%Td %TH:%TM:%TS %p\n' 2>/dev/null \
  | sort -r | head -10
===== PHP-FPM LOG =====
-- No entries --

===== NGINX ERROR LOG =====
2026/09/23 17:40:35 [error] 3461396#3461396: *300168 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //images/images/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:40:51 [error] 3461396#3461396: *300171 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //cgi-bin/cgi-bin/cgi-bin/cgi-bin/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:41:04 [error] 3461396#3461396: *300174 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //images/images/images/images/images/images/images/images/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:41:17 [error] 3461396#3461396: *300177 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //images/images/images/images/images/images/images/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:41:31 [error] 3461396#3461396: *300180 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //images/images/images/images/images/images/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:41:43 [error] 3461397#3461397: *300194 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //images/images/images/images/images/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:41:56 [error] 3461397#3461397: *300197 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //images/images/images/images/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:42:09 [error] 3461397#3461397: *300200 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //images/images/images/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:42:24 [error] 3461397#3461397: *300203 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //wp-content/plugins/plugins/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:42:31 [error] 3461397#3461397: *300206 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //assets/images/images/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:42:47 [error] 3461397#3461397: *300209 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //blogs/media/media/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 17:43:04 [error] 3461396#3461396: *300212 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 152.42.164.85, server: nebuladet.website, request: "GET //cache/cache/cache.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "www.google.com"
2026/09/23 18:57:29 [error] 3461397#3461397: *300768 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 104.23.223.14, server: nebuladet.website, request: "GET /wp-admin/install.php?step=1 HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website"
2026/09/23 19:20:27 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /phpmyadmin/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:27 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /phpMyAdmin/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:28 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /PMA/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:29 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /pma/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:30 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /phpMyAdmin-2/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:30 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /phpMyAdmin2/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:31 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /phpmyadmin2/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:31 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /mysql-admin/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:32 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /php-my-admin/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:33 [error] 3461397#3461397: *300814 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /php-myadmin/index.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website", referrer: "https://www.sukaharja-rajadesa.desa.id:443/phpmyadmin/"
2026/09/23 19:20:39 [error] 3461397#3461397: *300825 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /adminer.php?username=postgres HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website"
2026/09/23 19:20:40 [error] 3461397#3461397: *300825 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /adminer/adminer.php?username=postgres HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website"
2026/09/23 19:20:43 [error] 3461397#3461397: *300825 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 37.19.210.5, server: nebuladet.website, request: "GET /admin/adminer.php?username=postgres HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website"
2026/09/23 19:42:54 [warn] 3461396#3461396: *301164 a client request body is buffered to a temporary file /var/lib/nginx/tmp/client_body/0000000148, client: 190.87.160.72, server: nebuladet.website, request: "POST /clients/fagustin/process_bridge.php HTTP/1.1", host: "nebuladet.website", referrer: "https://nebuladet.website/clients/fagustin/factura_pro.php"
2026/09/23 19:43:11 [notice] 3507816#3507816: signal process started
2026/09/23 19:43:11 [notice] 3663277#3663277: signal 1 (SIGHUP) received from 3507816, reconfiguring
2026/09/23 19:43:11 [notice] 3663277#3663277: reconfiguring
2026/09/23 19:43:11 [notice] 3663277#3663277: using the "epoll" event method
2026/09/23 19:43:11 [notice] 3663277#3663277: start worker processes
2026/09/23 19:43:11 [notice] 3663277#3663277: start worker process 3507819
2026/09/23 19:43:11 [notice] 3663277#3663277: start worker process 3507820
2026/09/23 19:43:11 [notice] 3461397#3461397: gracefully shutting down
2026/09/23 19:43:11 [notice] 3461396#3461396: gracefully shutting down
2026/09/23 19:43:11 [notice] 3461397#3461397: exiting
2026/09/23 19:43:11 [notice] 3461396#3461396: exiting
2026/09/23 19:43:11 [notice] 3461397#3461397: exit
2026/09/23 19:43:11 [notice] 3461396#3461396: exit
2026/09/23 19:43:11 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3461397
2026/09/23 19:43:11 [notice] 3663277#3663277: worker process 3461397 exited with code 0
2026/09/23 19:43:11 [notice] 3663277#3663277: signal 29 (SIGIO) received
2026/09/23 19:43:11 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3461396
2026/09/23 19:43:11 [notice] 3663277#3663277: worker process 3461396 exited with code 0
2026/09/23 19:43:11 [notice] 3663277#3663277: signal 29 (SIGIO) received
2026/09/23 20:00:06 [notice] 3511313#3511313: signal process started
2026/09/23 20:00:06 [notice] 3663277#3663277: signal 1 (SIGHUP) received from 3511313, reconfiguring
2026/09/23 20:00:06 [notice] 3663277#3663277: reconfiguring
2026/09/23 20:00:06 [notice] 3663277#3663277: using the "epoll" event method
2026/09/23 20:00:06 [notice] 3663277#3663277: start worker processes
2026/09/23 20:00:06 [notice] 3663277#3663277: start worker process 3511316
2026/09/23 20:00:06 [notice] 3663277#3663277: start worker process 3511317
2026/09/23 20:00:07 [notice] 3507819#3507819: gracefully shutting down
2026/09/23 20:00:07 [notice] 3507819#3507819: exiting
2026/09/23 20:00:07 [notice] 3507819#3507819: exit
2026/09/23 20:00:07 [notice] 3507820#3507820: gracefully shutting down
2026/09/23 20:00:07 [notice] 3507820#3507820: exiting
2026/09/23 20:00:07 [notice] 3507820#3507820: exit
2026/09/23 20:00:07 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3507820
2026/09/23 20:00:07 [notice] 3663277#3663277: worker process 3507820 exited with code 0
2026/09/23 20:00:07 [notice] 3663277#3663277: signal 29 (SIGIO) received
2026/09/23 20:00:07 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3507819
2026/09/23 20:00:07 [notice] 3663277#3663277: worker process 3507819 exited with code 0
2026/09/23 20:00:07 [notice] 3663277#3663277: signal 29 (SIGIO) received
2026/09/23 20:37:59 [notice] 3513527#3513527: signal process started
2026/09/23 20:37:59 [notice] 3663277#3663277: signal 1 (SIGHUP) received from 3513527, reconfiguring
2026/09/23 20:37:59 [notice] 3663277#3663277: reconfiguring
2026/09/23 20:37:59 [notice] 3663277#3663277: using the "epoll" event method
2026/09/23 20:37:59 [notice] 3663277#3663277: start worker processes
2026/09/23 20:37:59 [notice] 3663277#3663277: start worker process 3513528
2026/09/23 20:37:59 [notice] 3663277#3663277: start worker process 3513529
2026/09/23 20:37:59 [notice] 3511316#3511316: gracefully shutting down
2026/09/23 20:37:59 [notice] 3511316#3511316: exiting
2026/09/23 20:37:59 [notice] 3511316#3511316: exit
2026/09/23 20:37:59 [notice] 3511317#3511317: gracefully shutting down
2026/09/23 20:37:59 [notice] 3511317#3511317: exiting
2026/09/23 20:37:59 [notice] 3511317#3511317: exit
2026/09/23 20:37:59 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3511317
2026/09/23 20:37:59 [notice] 3663277#3663277: worker process 3511316 exited with code 0
2026/09/23 20:37:59 [notice] 3663277#3663277: worker process 3511317 exited with code 0
2026/09/23 20:37:59 [notice] 3663277#3663277: signal 29 (SIGIO) received
2026/09/23 20:37:59 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3511316
2026/09/23 20:55:00 [error] 3513528#3513528: *301362 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 104.23.223.15, server: nebuladet.website, request: "GET /wp-admin/install.php?step=1 HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/www.sock:", host: "nebuladet.website"
2026/09/23 21:06:05 [notice] 3515150#3515150: signal process started
2026/09/23 21:06:05 [notice] 3663277#3663277: signal 1 (SIGHUP) received from 3515150, reconfiguring
2026/09/23 21:06:05 [notice] 3663277#3663277: reconfiguring
2026/09/23 21:06:05 [notice] 3663277#3663277: using the "epoll" event method
2026/09/23 21:06:05 [notice] 3663277#3663277: start worker processes
2026/09/23 21:06:05 [notice] 3663277#3663277: start worker process 3515151
2026/09/23 21:06:05 [notice] 3663277#3663277: start worker process 3515152
2026/09/23 21:06:05 [notice] 3513529#3513529: gracefully shutting down
2026/09/23 21:06:05 [notice] 3513528#3513528: gracefully shutting down
2026/09/23 21:06:05 [notice] 3513529#3513529: exiting
2026/09/23 21:06:05 [notice] 3513528#3513528: exiting
2026/09/23 21:06:05 [notice] 3513529#3513529: exit
2026/09/23 21:06:05 [notice] 3513528#3513528: exit
2026/09/23 21:06:05 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3513529
2026/09/23 21:06:05 [notice] 3663277#3663277: worker process 3513528 exited with code 0
2026/09/23 21:06:05 [notice] 3663277#3663277: worker process 3513529 exited with code 0
2026/09/23 21:06:05 [notice] 3663277#3663277: signal 29 (SIGIO) received
2026/09/23 21:06:05 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3513528
2026/09/23 22:00:32 [notice] 3520504#3520504: signal process started
2026/09/23 22:00:32 [notice] 3663277#3663277: signal 1 (SIGHUP) received from 3520504, reconfiguring
2026/09/23 22:00:32 [notice] 3663277#3663277: reconfiguring
2026/09/23 22:00:32 [notice] 3663277#3663277: using the "epoll" event method
2026/09/23 22:00:32 [notice] 3663277#3663277: start worker processes
2026/09/23 22:00:32 [notice] 3663277#3663277: start worker process 3520526
2026/09/23 22:00:32 [notice] 3663277#3663277: start worker process 3520529
2026/09/23 22:00:32 [notice] 3515152#3515152: gracefully shutting down
2026/09/23 22:00:32 [notice] 3515152#3515152: exiting
2026/09/23 22:00:32 [notice] 3515152#3515152: exit
2026/09/23 22:00:32 [notice] 3515151#3515151: gracefully shutting down
2026/09/23 22:00:32 [notice] 3515151#3515151: exiting
2026/09/23 22:00:32 [notice] 3515151#3515151: exit
2026/09/23 22:00:32 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3515151
2026/09/23 22:00:32 [notice] 3663277#3663277: worker process 3515151 exited with code 0
2026/09/23 22:00:32 [notice] 3663277#3663277: worker process 3515152 exited with code 0
2026/09/23 22:00:32 [notice] 3663277#3663277: signal 29 (SIGIO) received
2026/09/23 22:00:32 [notice] 3663277#3663277: signal 17 (SIGCHLD) received from 3515152

===== ORION LOGS =====
[ec2-user@ip-172-31-1-105 ZFREEZED_AIO_SERVICE]$

$ echo '===== AUTH ERRORS ====='

sudo grep -RniE \
  'auth|authentication|PDO|database|exception|fatal|LoginService|No fue posible procesar' \
  /home/ec2-user/orion/kernell/storage/logs \
  /var/log/php-fpm \
  /var/log/nginx \
  2>/dev/null \
  | tail -150
===== AUTH ERRORS =====
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2871:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /public/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2872:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /web/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2873:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2874:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /config/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2875:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /root/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2880:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /srv/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2881:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /htdocs/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2882:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /files/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2883:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /backup/.config/codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2884:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /backup/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2885:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /var/www/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2886:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /wwwroot/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2887:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /opt/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2888:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /uploads/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2889:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /tmp/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2890:34.92.32.76 - - [22/Sep/2026:11:38:51 +0000] "GET /home/.codex/auth.json HTTP/1.1" 404 153 "-" "crusader-worker/1.0"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:2969:34.140.132.132 - - [22/Sep/2026:11:45:15 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3037:34.140.132.132 - - [22/Sep/2026:11:45:15 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3038:34.140.132.132 - - [22/Sep/2026:11:45:15 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3056:34.140.132.132 - - [22/Sep/2026:11:45:15 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3241:34.52.229.253 - - [22/Sep/2026:11:46:09 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3308:34.52.229.253 - - [22/Sep/2026:11:46:09 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3309:34.52.229.253 - - [22/Sep/2026:11:46:09 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3328:34.52.229.253 - - [22/Sep/2026:11:46:10 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3513:35.241.239.86 - - [22/Sep/2026:12:02:34 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3580:35.241.239.86 - - [22/Sep/2026:12:02:34 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3581:35.241.239.86 - - [22/Sep/2026:12:02:34 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3600:35.241.239.86 - - [22/Sep/2026:12:02:34 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3794:34.52.133.111 - - [22/Sep/2026:13:24:23 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3860:34.52.133.111 - - [22/Sep/2026:13:24:24 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3861:34.52.133.111 - - [22/Sep/2026:13:24:24 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:3881:34.52.133.111 - - [22/Sep/2026:13:24:24 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4067:34.140.132.132 - - [22/Sep/2026:13:35:37 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4133:34.140.132.132 - - [22/Sep/2026:13:35:37 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4134:34.140.132.132 - - [22/Sep/2026:13:35:37 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4154:34.140.132.132 - - [22/Sep/2026:13:35:37 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4247:34.140.132.132 - - [22/Sep/2026:13:35:38 +0000] "GET /database.zip HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4344:34.77.137.207 - - [22/Sep/2026:14:31:22 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4411:34.77.137.207 - - [22/Sep/2026:14:31:23 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4412:34.77.137.207 - - [22/Sep/2026:14:31:23 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4431:34.77.137.207 - - [22/Sep/2026:14:31:23 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4614:34.52.133.111 - - [22/Sep/2026:14:41:02 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4681:34.52.133.111 - - [22/Sep/2026:14:41:02 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4683:34.52.133.111 - - [22/Sep/2026:14:41:02 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4701:34.52.133.111 - - [22/Sep/2026:14:41:03 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4887:34.156.22.151 - - [22/Sep/2026:14:53:53 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4953:34.156.22.151 - - [22/Sep/2026:14:53:53 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4954:34.156.22.151 - - [22/Sep/2026:14:53:53 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:4973:34.156.22.151 - - [22/Sep/2026:14:53:53 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5157:35.241.202.92 - - [22/Sep/2026:15:13:13 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5224:35.241.202.92 - - [22/Sep/2026:15:13:14 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5226:35.241.202.92 - - [22/Sep/2026:15:13:14 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5244:35.241.202.92 - - [22/Sep/2026:15:13:14 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5429:34.38.113.44 - - [22/Sep/2026:15:21:22 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5495:34.38.113.44 - - [22/Sep/2026:15:21:22 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5496:34.38.113.44 - - [22/Sep/2026:15:21:22 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5515:34.38.113.44 - - [22/Sep/2026:15:21:23 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5704:34.156.249.47 - - [22/Sep/2026:16:04:53 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5771:34.156.249.47 - - [22/Sep/2026:16:04:54 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5774:34.156.249.47 - - [22/Sep/2026:16:04:54 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5792:34.156.249.47 - - [22/Sep/2026:16:04:54 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:5979:34.156.249.47 - - [22/Sep/2026:16:38:53 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6046:34.156.249.47 - - [22/Sep/2026:16:38:54 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6047:34.156.249.47 - - [22/Sep/2026:16:38:54 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6066:34.156.249.47 - - [22/Sep/2026:16:38:54 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6256:34.52.229.253 - - [22/Sep/2026:17:50:50 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6322:34.52.229.253 - - [22/Sep/2026:17:50:50 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6323:34.52.229.253 - - [22/Sep/2026:17:50:50 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6343:34.52.229.253 - - [22/Sep/2026:17:50:51 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6442:34.52.229.253 - - [22/Sep/2026:17:50:52 +0000] "GET /oauth-credentials.json HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6535:34.140.132.132 - - [22/Sep/2026:18:50:40 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6601:34.140.132.132 - - [22/Sep/2026:18:50:41 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6602:34.140.132.132 - - [22/Sep/2026:18:50:41 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6622:34.140.132.132 - - [22/Sep/2026:18:50:41 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6809:34.140.234.80 - - [22/Sep/2026:18:50:52 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6876:34.140.234.80 - - [22/Sep/2026:18:50:53 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6877:34.140.234.80 - - [22/Sep/2026:18:50:53 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:6896:34.140.234.80 - - [22/Sep/2026:18:50:53 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7087:34.52.146.37 - - [22/Sep/2026:19:51:53 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7154:34.52.146.37 - - [22/Sep/2026:19:51:53 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7156:34.52.146.37 - - [22/Sep/2026:19:51:53 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7175:34.52.146.37 - - [22/Sep/2026:19:51:54 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7359:34.140.234.80 - - [22/Sep/2026:19:53:35 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7426:34.140.234.80 - - [22/Sep/2026:19:53:35 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7427:34.140.234.80 - - [22/Sep/2026:19:53:35 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7446:34.140.234.80 - - [22/Sep/2026:19:53:35 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7631:34.52.229.253 - - [22/Sep/2026:20:03:41 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7699:34.52.229.253 - - [22/Sep/2026:20:03:42 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7700:34.52.229.253 - - [22/Sep/2026:20:03:42 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7735:34.52.229.253 - - [22/Sep/2026:20:03:42 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7865:35.240.58.49 - - [22/Sep/2026:20:03:42 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7969:35.240.58.49 - - [22/Sep/2026:20:03:43 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7970:35.240.58.49 - - [22/Sep/2026:20:03:43 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:7988:35.240.58.49 - - [22/Sep/2026:20:03:43 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8172:34.77.137.207 - - [22/Sep/2026:20:05:19 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8238:34.77.137.207 - - [22/Sep/2026:20:05:20 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8239:34.77.137.207 - - [22/Sep/2026:20:05:20 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8259:34.77.137.207 - - [22/Sep/2026:20:05:20 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8442:35.240.100.200 - - [22/Sep/2026:20:39:10 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8508:35.240.100.200 - - [22/Sep/2026:20:39:10 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8509:35.240.100.200 - - [22/Sep/2026:20:39:10 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8529:35.240.100.200 - - [22/Sep/2026:20:39:10 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8719:34.77.137.207 - - [22/Sep/2026:20:46:02 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8786:34.77.137.207 - - [22/Sep/2026:20:46:03 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8787:34.77.137.207 - - [22/Sep/2026:20:46:03 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8806:34.77.137.207 - - [22/Sep/2026:20:46:03 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:8992:34.140.234.80 - - [22/Sep/2026:20:59:03 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9059:34.140.234.80 - - [22/Sep/2026:20:59:04 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9060:34.140.234.80 - - [22/Sep/2026:20:59:04 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9079:34.140.234.80 - - [22/Sep/2026:20:59:04 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9164:34.140.234.80 - - [22/Sep/2026:20:59:04 +0000] "GET /auth.json HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9179:34.140.234.80 - - [22/Sep/2026:20:59:04 +0000] "GET /storage/oauth-public.key HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9268:34.156.206.32 - - [22/Sep/2026:21:06:29 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9334:34.156.206.32 - - [22/Sep/2026:21:06:29 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9335:34.156.206.32 - - [22/Sep/2026:21:06:29 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9354:34.156.206.32 - - [22/Sep/2026:21:06:29 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9542:35.240.100.200 - - [22/Sep/2026:21:29:40 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9608:35.240.100.200 - - [22/Sep/2026:21:29:40 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9609:35.240.100.200 - - [22/Sep/2026:21:29:40 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9629:35.240.100.200 - - [22/Sep/2026:21:29:40 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9815:34.77.137.207 - - [22/Sep/2026:21:35:58 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9882:34.77.137.207 - - [22/Sep/2026:21:35:58 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9883:34.77.137.207 - - [22/Sep/2026:21:35:58 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:9902:34.77.137.207 - - [22/Sep/2026:21:35:59 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10085:35.241.202.92 - - [22/Sep/2026:21:43:06 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10152:35.241.202.92 - - [22/Sep/2026:21:43:07 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10153:35.241.202.92 - - [22/Sep/2026:21:43:07 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10172:35.241.202.92 - - [22/Sep/2026:21:43:07 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10356:34.140.132.132 - - [22/Sep/2026:21:45:55 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10422:34.140.132.132 - - [22/Sep/2026:21:45:55 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10423:34.140.132.132 - - [22/Sep/2026:21:45:55 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10443:34.140.132.132 - - [22/Sep/2026:21:45:56 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10630:35.240.58.49 - - [22/Sep/2026:21:55:16 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10696:35.240.58.49 - - [22/Sep/2026:21:55:16 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10697:35.240.58.49 - - [22/Sep/2026:21:55:16 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10717:35.240.58.49 - - [22/Sep/2026:21:55:16 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10907:35.240.100.200 - - [22/Sep/2026:23:08:07 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10974:35.240.100.200 - - [22/Sep/2026:23:08:07 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10975:35.240.100.200 - - [22/Sep/2026:23:08:07 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:10994:35.240.100.200 - - [22/Sep/2026:23:08:07 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11183:34.156.206.32 - - [22/Sep/2026:23:14:44 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11250:34.156.206.32 - - [22/Sep/2026:23:14:45 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11251:34.156.206.32 - - [22/Sep/2026:23:14:45 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11270:34.156.206.32 - - [22/Sep/2026:23:14:45 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11353:34.156.206.32 - - [22/Sep/2026:23:14:47 +0000] "GET /database.bak HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11454:207.175.220.59 - - [22/Sep/2026:23:56:22 +0000] "GET /.ssh/authorized_keys HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11521:207.175.220.59 - - [22/Sep/2026:23:56:23 +0000] "GET /config/database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11522:207.175.220.59 - - [22/Sep/2026:23:56:23 +0000] "GET /config/databases.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11541:207.175.220.59 - - [22/Sep/2026:23:56:23 +0000] "GET /database.sql HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
/var/log/nginx/agromarket.nebuladet.website-access.log-20260923:11632:207.175.220.59 - - [22/Sep/2026:23:56:24 +0000] "GET /database.yml HTTP/2.0" 404 153 "-" "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
[ec2-user@ip-172-31-1-105 ZFREEZED_AIO_SERVICE]$



REEZED_AIO_SERVICE]$ echo '===== ORION ENV KEYS ====='

sudo awk -F= '
/^[A-Za-z_][A-Za-z0-9_]*=/ {
    key=$1
    if (key ~ /PASSWORD|SECRET|KEY|TOKEN/) {
        print key "=<REDACTED>"
    } else {
        print key "=<SET>"
    }
}' /home/ec2-user/orion/kernell/.env
===== ORION ENV KEYS =====
APP_ENV=<SET>
APP_DEBUG=<SET>
APP_URL=<SET>
ORION_AUTH_DB_DSN=<SET>
ORION_AUTH_DB_USERNAME=<SET>
ORION_AUTH_DB_PASSWORD=<REDACTED>
JWT_SECRET=<REDACTED>
[ec2-user@ip-172-31-1-105 ZFREEZED_AIO_SERVICE]$



E]$ echo '===== PRODUCTION ENV PERMISSIONS ====='
sudo ls -l /etc/orion/orion.env
sudo ls -l /home/ec2-user/orion/kernell/.env
===== PRODUCTION ENV PERMISSIONS =====
-rw-r-----. 1 root nginx 351 Sep 20 06:42 /etc/orion/orion.env
-rw-r-----. 1 ec2-user nginx 351 Sep 23 22:00 /home/ec2-user/orion/kernell/.env
[ec2-user@ip-172-31-1-105 ZFREEZED_AIO_SERVICE]$



