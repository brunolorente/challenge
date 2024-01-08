## Variables
include .env
PHP_CONTAINER_NAME = sequra-laravel_app
DOCKER_COMPOSE = docker-compose -f ./docker-compose.yml

start:
	@${DOCKER_COMPOSE} up -d
	@if [ -d "vendor" ]; then \
		$(MAKE) urls; \
	fi

stop:
	@${DOCKER_COMPOSE} stop

status:
	@${DOCKER_COMPOSE} ps

destroy:
	@${DOCKER_COMPOSE} down -v -t 20 2>/dev/null
	@if [ ! -z "$$(docker ps -a -q -f name=$(PHP_CONTAINER_NAME))" ]; then \
		docker container rm -f $(PHP_CONTAINER_NAME) 2>/dev/null; \
	fi
	@if [ ! -z "$$(docker ps -a -q -f name=sequra-postgres_db)" ]; then \
		docker container rm -f sequra-postgres_db 2>/dev/null; \
		docker volume rm challenge_pgdata; \
	fi
	@if [ ! -z "$$(docker ps -a -q -f name=sequra-postgres_db_test)" ]; then \
		docker container rm -f sequra-postgres_db_test 2>/dev/null; \
	fi
	@if [ -d "vendor" ]; then \
		rm -rf vendor; \
	fi

build:
	@if [ ! -f ".env" ]; then \
		echo 'The configuration file ".env" does not exist. Please, create it following the README instructions.'; \
		exit 1; \
	fi
	@if [ ! -f ".env.test" ]; then \
		echo 'The configuration file ".env.test" does not exist. Please, create it following the README instructions.'; \
		exit 1; \
	fi
	@$(MAKE) destroy
	@${DOCKER_COMPOSE} build --no-cache
	@${MAKE} start
	@docker exec -ti $(PHP_CONTAINER_NAME) composer install --no-ansi \
        --no-interaction \
        --no-scripts \
        --no-progress \
        --prefer-dist

	@$(MAKE) migrate
	@$(MAKE) tests
	@$(MAKE) urls

urls:
	@echo ""
	@echo "The available URL is:"
	@echo "   http://localhost:8080"

ssh-php:
	@docker exec -ti $(PHP_CONTAINER_NAME) bash

tests:
	@echo "---------\nRunning Laravel tests...\n----------\n"
	@docker exec -t $(PHP_CONTAINER_NAME) php artisan test

migrate:
	@docker exec -t $(PHP_CONTAINER_NAME) php artisan migrate
