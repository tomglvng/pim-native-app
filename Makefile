# Docker targets

## This target run the application container including its dependencies and open a bash session to develop it.
.PHONY: dev
dev: docker-build
	docker-compose run --service-ports fpm --rm bash

## This target start the application and dependencies defined in your docker-compose.yml.
.PHONY: up
up: docker-build
	docker-compose up --remove-orphans -d

## This target stop the application and dependencies defined in your docker-compose.yml.
.PHONY: down
down:
	docker-compose down --remove-orphans

## This target build the application container including its dependencies and create docker volumes
.PHONY: docker-build
docker-build:
	DOCKER_BUILDKIT=1 docker build .
