## Variables
include .env
PHP_CONTAINER_NAME = sequra-laravel_app
DOCKER_COMPOSE = docker-compose -f ./docker-compose.yml

.PHONY: start stop status destroy build urls ssh-php tests migrate generate-data help phpstan

help:
	@echo "\033[1;32mUsage:\033[0m"
	@echo "  \033[1;34mstart\033[0m         🚀 Starts the docker containers."
	@echo "  \033[1;34mstop\033[0m          🛑 Stops the docker containers."
	@echo "  \033[1;34mstatus\033[0m        📊 Displays the status of the docker containers."
	@echo "  \033[1;34mdestroy\033[0m       💥 Destroys the docker containers and volumes."
	@echo "  \033[1;34mbuild\033[0m         🔧 Builds the docker images and starts the containers."
	@echo "  \033[1;34mssh-php\033[0m       💻 SSH into the PHP container."
	@echo "  \033[1;34mtests\033[0m         🧪 Runs the Laravel tests."
	@echo "  \033[1;34mmigrate\033[0m       🗃️ Runs the database migrations."
	@echo "  \033[1;34mgenerate-data\033[0m 📈 Generates dummy data."
	@echo "  \033[1;34mhelp\033[0m          🔧 Run phpstan."
	@echo "  \033[1;34mhelp\033[0m          🆘 Displays this help message."

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
	@${MAKE} generate-data

check-env:
	@if [ ! -f ".env" ] || [ ! -f ".env.test" ]; then \
		echo 'One or more configuration files are missing. Please, create them following the README instructions.'; \
		exit 1; \
	fi

ssh-php:
	@docker exec -ti $(PHP_CONTAINER_NAME) bash

tests:
	@echo "---------\nRunning Laravel tests...\n----------\n"
	@docker exec -t $(PHP_CONTAINER_NAME) php artisan test

phpstan:
	@docker exec -t $(PHP_CONTAINER_NAME) ./vendor/bin/phpstan analyse

migrate:
	@echo "Running migrations..."
	@docker exec -t $(PHP_CONTAINER_NAME) php artisan migrate

generate-data:
	@echo "Generating data..."
	@docker exec -t $(PHP_CONTAINER_NAME) ./generate-data.sh
