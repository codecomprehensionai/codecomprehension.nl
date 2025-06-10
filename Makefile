#!/usr/bin/make
# Makefile readme (en): <https://www.gnu.org/software/make/manual/html_node/index.html#SEC_Contents>

SHELL = /bin/bash
HOST_UID=$(shell id -u)
HOST_GID=$(shell id -g)

# DC_RUN_ARGS = --env-file ./.env.docker
DC_RUN_ARGS =

.PHONY : help build up up-wait update down down-remove-volumes restart logs ps health log shell command tunnel-production-mysql tunnel-production-redis tunnel-production-meilisearch
.DEFAULT_GOAL : help

# This will output the help for each task. thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## Show this help
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {printf "  \033[32m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build image
	docker compose ${DC_RUN_ARGS} build

up: ## Start containers
	docker compose ${DC_RUN_ARGS} up -d --remove-orphans

up-wait: ## Start containers and wait for them to be healthy
	docker compose ${DC_RUN_ARGS} up -d --remove-orphans --wait

update: ## Update containers
	docker compose ${DC_RUN_ARGS} up -d --remove-orphans --build --no-deps

down: ## Stop containers
	docker compose ${DC_RUN_ARGS} down

down-remove-volumes: ## Stop containers and remove volumes
	docker compose ${DC_RUN_ARGS} down -v

restart: ## Restart containers
	docker compose ${DC_RUN_ARGS} restart

logs: ## Tail all container logs
	docker compose ${DC_RUN_ARGS} logs -f

ps: ## List all container statuses
	docker compose ${DC_RUN_ARGS} ps

health: ## Check all container health
	docker compose${DC_RUN_ARGS} ps --format "table {{.Name}}\t{{.Service}}\t{{.Status}}"

log: ## Tail container logs (usage: make log http)
	docker compose ${DC_RUN_ARGS} logs -f $(if $(word 2, $(MAKECMDGOALS)),$(word 2, $(MAKECMDGOALS)),http)

shell: ## Shell into container (usage: make shell http)
	docker compose ${DC_RUN_ARGS} exec $(if $(word 2, $(MAKECMDGOALS)),$(word 2, $(MAKECMDGOALS)),http) sh

command: ## Exec in container (usage: make command http php artisan about)
	docker compose ${DC_RUN_ARGS} exec $(if $(word 2, $(MAKECMDGOALS)),$(word 2, $(MAKECMDGOALS)),http) sh -c "$(shell echo '$(wordlist 3, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS))')"

tunnel-production-mysql: ## Tunnel to mysql-production-flowsave-deploy
	cloudflared access tcp --hostname mysql-production-flowsave-deploy.proculair.net --url 127.0.0.1:13306

tunnel-production-redis: ## Tunnel to redis-production-flowsave-deploy
	cloudflared access tcp --hostname redis-production-flowsave-deploy.proculair.net --url 127.0.0.1:16379

tunnel-production-meilisearch: ## Tunnel to meilisearch-production-flowsave-deploy
	open https://meilisearch-production-flowsave-deploy.proculair.net

# Dummy targets to prevent make from interpreting arguments as targets
%:
	@:
