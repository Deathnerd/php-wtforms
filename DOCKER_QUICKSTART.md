# Docker Quick Start Guide

Get up and running with php-wtforms in Docker in under 2 minutes.

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+

## 1. Build and Start (30 seconds)

```bash
# Build all containers
docker-compose build

# Start containers in background
docker-compose up -d

# Verify they're running
docker-compose ps
```

Expected output:
```
NAME                    STATUS
php-wtforms-php56       Up
php-wtforms-php74       Up
php-wtforms-php82       Up
```

## 2. Install Dependencies (30 seconds)

```bash
# Using helper script (recommended)
./docker/scripts/composer.sh php56 install

# OR using make
make install

# OR directly via docker-compose
docker-compose exec php56 composer install
```

## 3. Run Tests (10 seconds)

```bash
# Using helper script (recommended)
./docker/scripts/test.sh php56

# OR using make
make test

# OR directly via docker-compose
docker-compose exec php56 ./vendor/bin/phpunit
```

## Done! 🎉

You now have a working development environment.

## Next Steps

### Run Tests on All PHP Versions

```bash
./docker/scripts/test-all.sh
```

### Open Interactive Shell

```bash
./docker/scripts/shell.sh php56
```

Once inside:
```bash
$ php -v                    # Check PHP version
$ composer --version        # Check Composer version
$ ./vendor/bin/phpunit      # Run tests
$ ls -la                    # Explore filesystem
```

### Run Specific Tests

```bash
# Run only validator tests
./docker/scripts/test.sh php56 --testsuite="WTForms Validators Test"

# Run specific test file
./docker/scripts/test.sh php56 tests/fields/StringFieldTest.php

# Run with filter
./docker/scripts/test.sh php56 --filter testEmail
```

### Using Make Commands

```bash
make help           # Show all available commands
make test           # Run tests in PHP 5.6
make test-all       # Run tests in all PHP versions
make shell          # Open shell in PHP 5.6
make coverage       # Generate coverage report
make clean          # Remove everything
```

## Common Issues

### "Permission denied" executing scripts

```bash
chmod +x docker/scripts/*.sh
```

### Containers won't start

```bash
# View logs
docker-compose logs

# Rebuild from scratch
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Tests fail in PHP 8.2

This is expected. The library was built for PHP 5.6. PHP 8.2 support is experimental.

## Development Workflow

```bash
# 1. Start environment
docker-compose up -d

# 2. Make changes in your editor
# (changes sync automatically to containers)

# 3. Run tests
./docker/scripts/test.sh php56

# 4. Test compatibility
./docker/scripts/test.sh php74

# 5. Stop when done
docker-compose down
```

## Complete Documentation

See [DOCKER.md](DOCKER.md) for full documentation including:
- Advanced usage
- IDE integration
- Troubleshooting
- Environment customization
- CI/CD integration

## One-Line Setup

```bash
make setup
```

This runs: build → start → install dependencies → run tests
