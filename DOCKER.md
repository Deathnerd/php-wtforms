# Docker Development Environment

This document describes how to use Docker for local development of php-wtforms.

## Overview

The Docker setup provides isolated PHP environments for testing the library across multiple PHP versions:
- **PHP 5.6** - Original target version (production)
- **PHP 7.4** - Modern stable version (testing compatibility)
- **PHP 8.2** - Latest version (future compatibility)

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+

## Quick Start

### 1. Build and Start Containers

```bash
# Build all PHP version containers
docker-compose build

# Start all containers in background
docker-compose up -d

# Verify containers are running
docker-compose ps
```

### 2. Run Tests

```bash
# Run tests in PHP 5.6 (default/production environment)
./docker/scripts/test.sh php56

# Run tests in PHP 7.4
./docker/scripts/test.sh php74

# Run tests in PHP 8.2
./docker/scripts/test.sh php82

# Run tests across ALL PHP versions
./docker/scripts/test-all.sh
```

### 3. Run Specific Test Suites

```bash
# Run only validator tests in PHP 5.6
./docker/scripts/test.sh php56 --testsuite="WTForms Validators Test"

# Run only field tests in PHP 7.4
./docker/scripts/test.sh php74 --testsuite="WTForms Field Tests"

# Run with coverage report (PHP 5.6)
./docker/scripts/test.sh php56 --coverage-html tests/_reports/coverage
```

### 4. Composer Commands

```bash
# Install dependencies in PHP 5.6
./docker/scripts/composer.sh php56 install

# Update dependencies in PHP 7.4
./docker/scripts/composer.sh php74 update

# Add a dev dependency
./docker/scripts/composer.sh php56 require --dev phpunit/phpunit:^5.0
```

### 5. Interactive Shell

```bash
# Open shell in PHP 5.6 container
./docker/scripts/shell.sh php56

# Once inside, you can run any command:
$ php -v
$ composer --version
$ ./vendor/bin/phpunit
$ ls -la
```

## Container Architecture

### Services

Each PHP version runs in its own isolated container:

| Service | Container Name | PHP Version | Composer | Purpose |
|---------|---------------|-------------|----------|---------|
| php56 | php-wtforms-php56 | 5.6 CLI | 1.10 | Production environment |
| php74 | php-wtforms-php74 | 7.4 CLI | 2.x | Compatibility testing |
| php82 | php-wtforms-php82 | 8.2 CLI | Latest | Future compatibility |

### Volumes

- **Source Code**: `.` → `/app` (live sync, changes reflect immediately)
- **Composer Cache**: Separate cache volumes per PHP version for faster rebuilds

### File Structure

```
docker/
├── Dockerfile          # PHP 5.6 environment
├── Dockerfile.php7     # PHP 7.4 environment
├── Dockerfile.php8     # PHP 8.2 environment
└── scripts/
    ├── test.sh         # Run tests in specific PHP version
    ├── test-all.sh     # Run tests across all versions
    ├── composer.sh     # Run composer commands
    └── shell.sh        # Open interactive shell

docker-compose.yml      # Service definitions
.dockerignore          # Files to exclude from Docker context
```

## Common Tasks

### Testing

```bash
# Run all tests
./docker/scripts/test.sh php56

# Run specific test file
./docker/scripts/test.sh php56 tests/fields/StringFieldTest.php

# Run with specific filter
./docker/scripts/test.sh php56 --filter testConstruct

# Run with verbose output
./docker/scripts/test.sh php56 --verbose

# Generate coverage report
./docker/scripts/test.sh php56 --coverage-html tests/_reports/coverage
```

### Development Workflow

```bash
# 1. Start containers
docker-compose up -d

# 2. Install dependencies
./docker/scripts/composer.sh php56 install

# 3. Make code changes in your editor (changes sync automatically)

# 4. Run tests to verify
./docker/scripts/test.sh php56

# 5. Test compatibility with PHP 7.4
./docker/scripts/composer.sh php74 install
./docker/scripts/test.sh php74

# 6. Stop containers when done
docker-compose down
```

### Debugging

```bash
# View container logs
docker-compose logs php56

# Follow logs in real-time
docker-compose logs -f php56

# Check PHP configuration
./docker/scripts/shell.sh php56
$ php -i | grep -i memory

# Check installed extensions
./docker/scripts/shell.sh php56
$ php -m
```

### Rebuilding

```bash
# Rebuild specific service after Dockerfile changes
docker-compose build php56

# Rebuild all services
docker-compose build

# Rebuild without cache (clean build)
docker-compose build --no-cache

# Rebuild and restart
docker-compose up -d --build
```

### Cleanup

```bash
# Stop containers
docker-compose down

# Stop and remove volumes (will delete composer cache)
docker-compose down -v

# Remove all images
docker-compose down --rmi all

# Complete cleanup (containers, volumes, images)
docker-compose down -v --rmi all
```

## Advanced Usage

### Running Custom Commands

```bash
# Run any command in container
docker-compose exec php56 php -v
docker-compose exec php74 composer --version

# Run command without TTY (useful for CI)
docker-compose exec -T php56 ./vendor/bin/phpunit
```

### Testing Against Different Dependencies

```bash
# Test with minimum dependencies
./docker/scripts/shell.sh php56
$ composer update --prefer-lowest
$ ./vendor/bin/phpunit

# Test with latest dependencies
$ composer update
$ ./vendor/bin/phpunit
```

### Continuous Integration Simulation

```bash
# Simulate CI pipeline
docker-compose up -d
./docker/scripts/composer.sh php56 install --no-interaction
./docker/scripts/test.sh php56 --coverage-text
docker-compose down
```

## Troubleshooting

### Containers Won't Start

```bash
# Check container status
docker-compose ps

# View error logs
docker-compose logs

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Permission Issues

The containers run as `www-data` user. If you encounter permission issues:

```bash
# Fix permissions on host
sudo chown -R $USER:$USER .

# Or run as root (not recommended for production)
docker-compose exec -u root php56 bash
```

### Out of Disk Space

```bash
# Check Docker disk usage
docker system df

# Clean up unused resources
docker system prune

# Clean up everything (WARNING: removes all unused data)
docker system prune -a --volumes
```

### Slow Performance on macOS/Windows

Docker on macOS/Windows can be slow due to volume mounting. Consider:

1. Using Docker Desktop's performance tuning
2. Enabling file sharing only for required directories
3. Using mutagen or docker-sync for improved performance

### PHP 8.2 Tests Fail

PHP 8.2 support is experimental. The library was designed for PHP 5.6 and may require code changes for full PHP 8 compatibility.

## IDE Integration

### PhpStorm

1. Add Docker Compose interpreter:
   - **Settings** → **PHP** → **CLI Interpreter** → **Add** → **From Docker, Vagrant, VM...**
   - Select **Docker Compose**
   - Choose `php56` service
   - Configuration file: `docker-compose.yml`

2. Configure PHPUnit:
   - **Settings** → **PHP** → **Test Frameworks** → **Add** → **PHPUnit by Remote Interpreter**
   - Choose the Docker interpreter created above
   - Path to phpunit.phar: `/app/vendor/bin/phpunit`
   - Configuration file: `/app/phpunit.xml`

### VS Code

Install the **Remote - Containers** extension and use the container for development:

```json
// .vscode/settings.json
{
  "php.validate.executablePath": "docker-compose exec -T php56 php"
}
```

## Environment Variables

You can customize container behavior with environment variables:

```bash
# Example: .env file
COMPOSE_PROJECT_NAME=php-wtforms
COMPOSE_FILE=docker-compose.yml
```

## Contributing

When contributing:

1. Test changes across all PHP versions: `./docker/scripts/test-all.sh`
2. Ensure no new deprecation warnings in PHP 7.4+
3. Document any Docker setup changes in this file

## References

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [PHP Docker Images](https://hub.docker.com/_/php)
