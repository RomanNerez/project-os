DOCKER_EXEC = docker compose exec app
DOCKER_TESTING = docker compose -f docker-compose.testing.yml -p project-os-testing

.PHONY: optimize migrate restart-supervisor deploy seed-projects seed-tasks seed-comments seed-media

local-up:
	docker compose -f docker-compose.yml -f docker-compose.local.yml up -d

local-down:
	docker compose -f docker-compose.yml -f docker-compose.local.yml down

testing-build:
	$(DOCKER_TESTING) build --no-cache

testing-up:
	$(DOCKER_TESTING) up -d

testing-bash:
	$(DOCKER_TESTING) exec app.testing bash

testing-down:
	$(DOCKER_TESTING) up -d

optimize:
	$(DOCKER_EXEC) php artisan optimize:clear

update-photo:
	$(DOCKER_EXEC) php artisan media-library:regenerate --only=$(TYPE)

migrate:
	$(DOCKER_EXEC) php artisan migrate --force

# Dev data: make seed-projects [COUNT=5] [MEMBERS=3] [OWNER=<user id>]
seed-projects:
	$(DOCKER_EXEC) php artisan seed:projects --count=$(or $(COUNT),5) --members=$(or $(MEMBERS),3) $(if $(OWNER),--owner=$(OWNER))

# make seed-tasks PROJECT=<id> [COUNT=50]
seed-tasks:
	$(if $(PROJECT),,$(error PROJECT is required, e.g. make seed-tasks PROJECT=1))
	$(DOCKER_EXEC) php artisan seed:tasks $(PROJECT) --count=$(or $(COUNT),50)

# make seed-comments PROJECT=<id> [COUNT=100] [TASK=<id>]
seed-comments:
	$(if $(PROJECT),,$(error PROJECT is required, e.g. make seed-comments PROJECT=1))
	$(DOCKER_EXEC) php artisan seed:comments $(PROJECT) --count=$(or $(COUNT),100) $(if $(TASK),--task=$(TASK))

# make seed-media PROJECT=<id> [COUNT=20] [TASK=<id>]
seed-media:
	$(if $(PROJECT),,$(error PROJECT is required, e.g. make seed-media PROJECT=1))
	$(DOCKER_EXEC) php artisan seed:media $(PROJECT) --count=$(or $(COUNT),20) $(if $(TASK),--task=$(TASK))

restart-supervisor:
	docker compose stop supervisor
	docker compose up -d supervisor

deploy:
	git pull
	$(DOCKER_EXEC) composer install
    $(DOCKER_EXEC) npm run build
	$(MAKE) migrate
	$(MAKE) optimize
	$(MAKE) restart-supervisor
