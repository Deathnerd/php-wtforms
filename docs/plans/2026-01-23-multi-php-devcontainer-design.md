# Multi-PHP Devcontainer Design

## Overview

A docker-compose based devcontainer setup with PHP 5.6, 7.4, and 8.4 for upgrading php-wtforms to PHP 8.4 and implementing the Conformity PRD.

## Goals

- Establish a green test baseline on PHP 5.6 (the archived working state)
- Enable side-by-side testing across PHP versions during migration
- Support IDE debugging on PHP 7.4+ via Xdebug 3.x
- Fast container builds using official PHP images and mlocati/php-extension-installer

## Architecture

### File Structure

```
.devcontainer/
├── docker-compose.yml
├── php56/
│   └── Dockerfile
├── php74/
│   └── Dockerfile
├── php84/
│   └── Dockerfile
└── devcontainer.json
```

### Docker-Compose Services

Three services sharing the same volume mount:

```yaml
services:
  php56:
    build: ./php56
    volumes:
      - ..:/workspace:cached
    working_dir: /workspace

  php74:
    build: ./php74
    volumes:
      - ..:/workspace:cached
    working_dir: /workspace

  php84:
    build: ./php84
    volumes:
      - ..:/workspace:cached
    working_dir: /workspace
```

### Dockerfiles

Each Dockerfile uses:
- Official PHP CLI image as base (`php:5.6-cli`, `php:7.4-cli`, `php:8.4-cli`)
- mlocati/php-extension-installer for extensions (bind mount pattern)
- Composer copied from official image

### Extensions

| Extension | 5.6 | 7.4 | 8.4 |
|-----------|-----|-----|-----|
| mbstring | ✓ | ✓ | ✓ |
| xml | ✓ | ✓ | ✓ |
| curl | ✓ | ✓ | ✓ |
| zip | ✓ | ✓ | ✓ |
| pdo_sqlite | ✓ | ✓ | ✓ |
| xdebug | - | 3.x | 3.x |

Xdebug excluded from PHP 5.6 because Xdebug 2.x signatures are rejected by modern IDEs.

### Devcontainer Configuration

Points to docker-compose with a primary service for IDE attachment. Initially php56, updated to php74 then php84 as migration progresses.

## Usage

### Running Tests

```bash
# Single version
docker-compose run php56 vendor/bin/phpunit
docker-compose run php74 vendor/bin/phpunit
docker-compose run php84 vendor/bin/phpunit

# All versions sequentially
docker-compose run php56 vendor/bin/phpunit && \
docker-compose run php74 vendor/bin/phpunit && \
docker-compose run php84 vendor/bin/phpunit

# Coverage (7.4/8.4 only)
docker-compose run -e XDEBUG_MODE=coverage php84 vendor/bin/phpunit --coverage-html tests/_reports/coverage/
```

### Migration Workflow

1. `docker-compose build` - build all three images
2. `docker-compose run php56 composer install` - verify deps on baseline
3. `docker-compose run php56 vendor/bin/phpunit` - establish green baseline
4. Generate coverage report for comparison
5. Update devcontainer.json service to php74 when ready
6. Repeat verification for php74
7. Update to php84, then begin Conformity implementation

## IDE Integration

The devcontainer.json points at whichever service is the current primary. The IDE attaches to that container for IntelliSense and debugging. Debugging available on php74 and php84 via Xdebug 3.x.
