#!/bin/bash
# Run Composer commands in specified PHP version container
# Usage: ./docker/scripts/composer.sh [php56|php74|php82] [composer-command]

set -e

PHP_VERSION=${1:-php56}
shift || true

case $PHP_VERSION in
    php56|5.6|56)
        CONTAINER="php-wtforms-php56"
        ;;
    php74|7.4|74)
        CONTAINER="php-wtforms-php74"
        ;;
    php82|8.2|82)
        CONTAINER="php-wtforms-php82"
        ;;
    *)
        echo "Usage: $0 [php56|php74|php82] [composer-command]"
        echo "Example: $0 php56 install"
        echo "Example: $0 php74 require --dev phpunit/phpunit"
        exit 1
        ;;
esac

echo "Running composer in $CONTAINER..."
docker-compose exec -T $CONTAINER composer "$@"
