#!/usr/bin/env bash
set -ex

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")

docker run -ti -v $SCRIPTPATH:/var/www -p 8888:8888 php:7-alpine /bin/sh -c "set -ex; docker-php-ext-install pdo_mysql calendar; php -S 0.0.0.0:8888 -t /var/www/www"
