DOCKER_EXEC = docker compose exec app

.PHONY: optimize migrate restart-supervisor deploy

optimize:
	$(DOCKER_EXEC) php artisan optimize:clear

update-photo:
	$(DOCKER_EXEC) php artisan media-library:regenerate --only=$(TYPE)

migrate:
	$(DOCKER_EXEC) php artisan migrate --force

restart-supervisor:
	docker compose stop supervisor
	docker compose up -d supervisor

deploy:
	git pull
	$(DOCKER_EXEC) composer install
	$(MAKE) migrate
	$(MAKE) optimize
	$(MAKE) restart-supervisor
