#!/bin/bash
# Run tests across all PHP versions
# Usage: ./docker/scripts/test-all.sh [phpunit-args]

set -e

echo "=========================================="
echo "Running tests on PHP 5.6"
echo "=========================================="
./docker/scripts/test.sh php56 "$@" || echo "PHP 5.6 tests failed!"
echo ""

echo "=========================================="
echo "Running tests on PHP 7.4"
echo "=========================================="
./docker/scripts/test.sh php74 "$@" || echo "PHP 7.4 tests failed!"
echo ""

echo "=========================================="
echo "Running tests on PHP 8.2"
echo "=========================================="
./docker/scripts/test.sh php82 "$@" || echo "PHP 8.2 tests failed!"
echo ""

echo "=========================================="
echo "All test runs complete!"
echo "=========================================="
