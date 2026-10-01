#!/bin/sh
set -e

# Hosts such as Render generate a bare base64 secret; Laravel expects the "base64:" prefix.
case "$APP_KEY" in
  ""|base64:*) ;;
  *) export APP_KEY="base64:$APP_KEY" ;;
esac

if [ -z "$APP_KEY" ]; then
  echo "APP_KEY is not set. Refusing to start." >&2
  exit 1
fi

# Listen on the port the host assigns.
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/app/private/cvs storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force
# The seeder only adds what is missing (admin login, example jobs, starter articles).
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
