#!/usr/bin/env sh
set -eu

: "${PORT:=10000}"
export PORT

envsubst '${PORT}' < /etc/apache2/ports.conf.template > /etc/apache2/ports.conf
envsubst '${PORT}' < /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf

if [ "${RUN_MIGRATIONS_ON_START:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

if [ "${RUN_DATABASE_SEED_ON_START:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction
fi

exec apache2-foreground
