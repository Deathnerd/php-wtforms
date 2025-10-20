# Docker Files

This directory contains Docker configuration files for local development.

## Files

- **Dockerfile** - PHP 5.6 CLI environment (production target)
- **Dockerfile.php7** - PHP 7.4 CLI environment (compatibility testing)
- **Dockerfile.php8** - PHP 8.2 CLI environment (future compatibility)
- **scripts/** - Helper scripts for common development tasks

## Usage

See [../DOCKER.md](../DOCKER.md) for complete documentation.

## Quick Reference

```bash
# From project root
docker-compose up -d                    # Start all containers
./docker/scripts/test.sh php56          # Run tests in PHP 5.6
./docker/scripts/shell.sh php56         # Open shell in PHP 5.6
docker-compose down                     # Stop containers
```

## Architecture Notes

### Why CLI Images?

This is a library project, not a web application, so we use PHP CLI images rather than Apache/nginx:

- Faster startup
- Smaller image size
- No web server overhead
- Focus on testing and development

### Multiple PHP Versions

The setup provides three PHP environments:

1. **PHP 5.6** - Original target (composer 1.10, legacy support)
2. **PHP 7.4** - Modern stable (composer 2.x, type hints)
3. **PHP 8.2** - Latest (composer latest, named arguments, enums)

This allows testing compatibility across PHP evolution.

### Volume Mounts

Source code is mounted as a volume rather than copied, so changes on the host immediately reflect in containers. This enables rapid development without rebuilds.

## Customization

To modify PHP configuration:

1. Create `php/conf.d/custom.ini`
2. Add volume mount in `docker-compose.yml`:
   ```yaml
   volumes:
     - ./docker/php/conf.d/custom.ini:/usr/local/etc/php/conf.d/custom.ini
   ```
3. Restart containers: `docker-compose restart`

## Troubleshooting

### Container Fails to Start

Check logs:
```bash
docker-compose logs php56
```

### Permission Errors

Containers run as `www-data`. Fix host permissions:
```bash
sudo chown -R $USER:$USER ../
```

### Build Failures

Clean rebuild:
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```
