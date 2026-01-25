@echo off
docker-compose -f .devcontainer/docker-compose.yml run --rm php74 vendor/bin/rector %*
