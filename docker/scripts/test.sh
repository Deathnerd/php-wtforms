#!/bin/bash
# Run PHPUnit tests in specified PHP version container
# Usage: ./docker/scripts/test.sh [php56|php74|php82] [phpunit-args]

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
        echo "Usage: $0 [php56|php74|php82] [phpunit-args]"
        echo "Example: $0 php56 --testsuite=\"WTForms Common Tests\""
        exit 1
        ;;
esac

echo "Running tests in $CONTAINER..."
docker-compose exec -T $CONTAINER ./vendor/bin/phpunit "$@"
