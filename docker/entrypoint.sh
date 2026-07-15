#!/bin/sh

# Set the port dynamically for Render
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

# Run migrations automatically if desired (uncomment if you want auto-migration on deploy)
# php artisan migrate --force

# Optimize Laravel configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Execute the main container command (CMD)
exec "$@"
