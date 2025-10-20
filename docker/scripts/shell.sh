#!/bin/bash
# Open interactive shell in specified PHP version container
# Usage: ./docker/scripts/shell.sh [php56|php74|php82]

set -e

PHP_VERSION=${1:-php56}

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
        echo "Usage: $0 [php56|php74|php82]"
        exit 1
        ;;
esac

echo "Opening shell in $CONTAINER..."
docker-compose exec $CONTAINER /bin/bash
