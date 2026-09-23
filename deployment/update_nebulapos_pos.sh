#!/usr/bin/env bash
set -Eeuo pipefail

# NebulaPOS 7.0 - production POS update
# Team MYTS
# Updates the existing installation only. Does NOT reinstall nginx/PHP-FPM.

REPO="git@github.com:RabbitSoftStudios/nebulapos.git"
BRANCH="release/nebulapos-pos-sqlite"
SSH_KEY="/home/ec2-user/.ssh/id_ed25519"
SSH_CMD="ssh -i ${SSH_KEY} -o IdentitiesOnly=yes"

APP_ROOT="/opt/nebulapos"
SOURCE_ROOT="${APP_ROOT}/release-source"
WEB_ROOT="/var/www/pos.nebuladet.website"
BACKEND_ROOT="${APP_ROOT}/backend"
DB_PATH="${BACKEND_ROOT}/database.sqlite"
PHP_SOCKET="/run/php-fpm/www.sock"
NGINX_CONF="/etc/nginx/conf.d/pos.nebuladet.website.conf"
BACKUP_ROOT="${APP_ROOT}/backups/$(date +%Y%m%d_%H%M%S)"

if [[ "$(id -u)" -ne 0 ]]; then
  echo "ERROR: ejecutar con sudo."
  exit 1
fi

for f in "$SSH_KEY" "$PHP_SOCKET" "$NGINX_CONF"; do
  [[ -e "$f" ]] || { echo "ERROR: no existe $f"; exit 1; }
done

command -v git >/dev/null || { echo "ERROR: git no instalado."; exit 1; }
command -v rsync >/dev/null || { echo "ERROR: rsync no instalado."; exit 1; }
command -v php >/dev/null || { echo "ERROR: PHP no instalado."; exit 1; }
command -v nginx >/dev/null || { echo "ERROR: nginx no instalado."; exit 1; }

mkdir -p "$BACKUP_ROOT"

echo "===== BACKUP ====="
cp -a "$WEB_ROOT" "$BACKUP_ROOT/webroot"
cp -a "$BACKEND_ROOT" "$BACKUP_ROOT/backend"
cp -a "$NGINX_CONF" "$BACKUP_ROOT/nginx.conf"

if [[ -d "$SOURCE_ROOT/.git" ]]; then
  GIT_SSH_COMMAND="$SSH_CMD" git -C "$SOURCE_ROOT" fetch --prune origin "$BRANCH"
  git -C "$SOURCE_ROOT" checkout -f "$BRANCH"
  git -C "$SOURCE_ROOT" reset --hard "origin/$BRANCH"
else
  rm -rf "$SOURCE_ROOT"
  GIT_SSH_COMMAND="$SSH_CMD" git clone --branch "$BRANCH" --single-branch "$REPO" "$SOURCE_ROOT"
fi

COMMIT="$(git -C "$SOURCE_ROOT" rev-parse HEAD)"
echo "Commit: $COMMIT"

[[ -d "$SOURCE_ROOT/frontend" ]] || { echo "ERROR: falta frontend/."; exit 1; }
[[ -f "$SOURCE_ROOT/frontend/pos_system/includes/sqlite_schema.php" ]] || { echo "ERROR: falta sqlite_schema.php."; exit 1; }

# Runtime frontend only. Documentation and tests remain in Git but are NOT deployed into webroot.
# This prevents exposing development documentation/tests publicly.
rsync -a --delete \
  --exclude='documentacion/' \
  --exclude='test/' \
  "$SOURCE_ROOT/frontend/" "$WEB_ROOT/"

# Production backend endpoints from the same release.
install -m 0644 "$SOURCE_ROOT/backend/auth.php" "$BACKEND_ROOT/auth.php"
install -m 0644 "$SOURCE_ROOT/backend/db.php" "$BACKEND_ROOT/db.php"
install -m 0644 "$SOURCE_ROOT/backend/curl_helper.php" "$BACKEND_ROOT/curl_helper.php"
install -m 0644 "$SOURCE_ROOT/backend/wompi.php" "$BACKEND_ROOT/wompi.php"

# Keep SQLite outside the webroot and initialize/migrate non-destructively.
if [[ ! -f "$DB_PATH" ]]; then
  install -m 0660 /dev/null "$DB_PATH"
fi

php -r '
require $argv[1];
$pdo = new PDO("sqlite:" . $argv[2]);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
initializeNebulaPOSSchema($pdo);
echo "SQLite schema OK\n";
' "$SOURCE_ROOT/frontend/pos_system/includes/sqlite_schema.php" "$DB_PATH"

# Ownership/permissions: nginx serves the frontend; apache executes PHP-FPM.
chown -R nginx:nginx "$WEB_ROOT"
find "$WEB_ROOT" -type d -exec chmod 0755 {} +
find "$WEB_ROOT" -type f -exec chmod 0644 {} +
chown -R apache:apache "$BACKEND_ROOT"
find "$BACKEND_ROOT" -type d -exec chmod 0755 {} +
find "$BACKEND_ROOT" -type f -exec chmod 0644 {} +
chown apache:apache "$DB_PATH"
chmod 0660 "$DB_PATH"

# Do not publish runtime secrets or internal database files.
rm -f "$WEB_ROOT/.env" "$WEB_ROOT/pos_system/.env" "$WEB_ROOT/pos_system/storage/temp/token.json"

# Verify that NGINX configuration is still valid before reload.
nginx -t
systemctl reload nginx

# Basic local verification.
echo
echo "===== VERIFY ====="
php -v | head -1
php -m | grep -Ei '^(PDO|pdo_sqlite|sqlite3|curl)$' | sort
printf 'PHP-FPM socket: '; test -S "$PHP_SOCKET" && echo OK || echo FAIL
printf 'SQLite: '; test -f "$DB_PATH" && echo OK || echo FAIL
printf 'SQLite outside webroot: '; [[ "$DB_PATH" != "$WEB_ROOT"/* ]] && echo OK || echo FAIL
printf 'Frontend index: '; test -f "$WEB_ROOT/index.html" && echo OK || echo FAIL
printf 'POS entry: '; test -f "$WEB_ROOT/pos_system/index.php" && echo OK || echo FAIL
printf 'Login: '; test -f "$WEB_ROOT/pos_system/login.php" && echo OK || echo FAIL
printf 'Documentation not deployed: '; test ! -d "$WEB_ROOT/pos_system/documentacion" && echo OK || echo FAIL
printf 'Tests not deployed: '; test ! -d "$WEB_ROOT/pos_system/test" && echo OK || echo FAIL

echo
echo "UPDATE COMPLETED"
echo "Commit: $COMMIT"
echo "Backup: $BACKUP_ROOT"
echo "URL: https://pos.nebuladet.website"
