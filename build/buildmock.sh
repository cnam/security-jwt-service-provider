#!/bin/sh
set -e

sed -i 's/\;date\.timezone\ \=/date\.timezone\ \=\ Europe\/Moscow/g' /etc/php/php.ini
sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' /etc/php/php-fpm.conf

exec "$@"
