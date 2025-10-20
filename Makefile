# Makefile for php-wtforms Docker development
.PHONY: help build up down restart logs shell test test-all composer clean

# Default target
.DEFAULT_GOAL := help

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

build: ## Build all Docker containers
	docker-compose build

up: ## Start all containers in background
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

restart: down up ## Restart all containers

logs: ## Show logs from all containers
	docker-compose logs -f

shell: ## Open shell in PHP 5.6 container (use: make shell VERSION=php74 for other versions)
	./docker/scripts/shell.sh $(or $(VERSION),php56)

test: ## Run tests in PHP 5.6 (use: make test VERSION=php74 ARGS="--filter testFoo" for options)
	./docker/scripts/test.sh $(or $(VERSION),php56) $(ARGS)

test-all: ## Run tests across all PHP versions
	./docker/scripts/test-all.sh $(ARGS)

composer: ## Run composer in PHP 5.6 (use: make composer VERSION=php74 CMD="install" for options)
	./docker/scripts/composer.sh $(or $(VERSION),php56) $(or $(CMD),install)

clean: ## Remove all containers, volumes, and images
	docker-compose down -v --rmi all

setup: build up composer test ## Complete setup: build, start, install deps, run tests

# Development shortcuts
install: ## Install composer dependencies in PHP 5.6
	./docker/scripts/composer.sh php56 install

update: ## Update composer dependencies in PHP 5.6
	./docker/scripts/composer.sh php56 update

coverage: ## Generate test coverage report
	./docker/scripts/test.sh php56 --coverage-html tests/_reports/coverage

# Quick test targets
test-common: ## Run common tests only
	./docker/scripts/test.sh php56 --testsuite="WTForms Common Tests"

test-fields: ## Run field tests only
	./docker/scripts/test.sh php56 --testsuite="WTForms Field Tests"

test-validators: ## Run validator tests only
	./docker/scripts/test.sh php56 --testsuite="WTForms Validators Test"

test-widgets: ## Run widget tests only
	./docker/scripts/test.sh php56 --testsuite="WTForms Widgets Tests"
