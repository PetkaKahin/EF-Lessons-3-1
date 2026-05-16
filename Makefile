.PHONY: init up down shell dump-autoload check-config migrate

init: check-config
	docker compose build
	docker compose run --rm php composer install
	$(migrate)

up:
	docker compose up -d

down:
	docker compose down

shell:
	docker compose exec php sh

dump-autoload:
	docker compose exec php composer dump-autoload

check-config:
	@if not exist config.php ( echo config.php not found. & echo Create config.php from config.example.php and fill in the values. & exit /b 1 )

migrate: check-config
	docker compose run --rm php php Infrastructure/Commands/migrate.php
