## Variables
include .env
PHP_CONTAINER_NAME = sequra-laravel_app
DOCKER_COMPOSE = docker-compose -f ./docker-compose.yml

.PHONY: start stop status destroy build urls ssh-php tests migrate generate-data

start:
	@echo "Starting services..."
	@${DOCKER_COMPOSE} up -d

stop:
	@echo "Stopping services..."
	@${DOCKER_COMPOSE} stop

status:
	@${DOCKER_COMPOSE} ps

destroy:
	@echo "Destroying containers and volumes..."
	@${DOCKER_COMPOSE} down -v -t 20
	@docker container rm -f $(PHP_CONTAINER_NAME) sequra-postgres_db sequra-postgres_db_test 2>/dev/null || true
	@docker volume rm challenge_pgdata 2>/dev/null || true
	@rm -rf vendor || true

build: check-env destroy
	@echo "Building and starting services..."
	@${DOCKER_COMPOSE} build --no-cache
	@${MAKE} start
	@docker exec -ti $(PHP_CONTAINER_NAME) composer install --no-ansi \
        --no-interaction \
        --no-scripts \
        --no-progress \
        --prefer-dist
	@${MAKE} migrate
	@echo "Waiting for services to be fully up and running..."
	@sleep 15  # Espera 15 segundos antes de ejecutar los tests
	@${MAKE} tests
	@${MAKE} urls

check-env:
	@if [ ! -f ".env" ] || [ ! -f ".env.test" ]; then \
		echo 'One or more configuration files are missing. Please, create them following the README instructions.'; \
		exit 1; \
	fi

urls:
	@echo "\nThe available URL is:\n   http://localhost:8080\n"

ssh-php:
	@docker exec -ti $(PHP_CONTAINER_NAME) bash

tests:
	@echo "---------\nRunning Laravel tests...\n----------\n"
	@docker exec -t $(PHP_CONTAINER_NAME) php artisan test

migrate:
	@echo "Running migrations..."
	@docker exec -t $(PHP_CONTAINER_NAME) php artisan migrate

generate-data:
	@echo "Generating data..."
	@docker exec -t $(PHP_CONTAINER_NAME) ./generate-data.sh
