#!/usr/bin/env sh
set -eu

: "${PORT:=10000}"
export PORT

envsubst '${PORT}' < /etc/apache2/ports.conf.template > /etc/apache2/ports.conf
envsubst '${PORT}' < /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
