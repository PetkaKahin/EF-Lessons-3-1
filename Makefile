.PHONY: init up down migrate retry-webhooks shell check-config

init: check-config
	docker compose build
	docker compose run --rm php composer install

up:
	docker compose up -d

down:
	docker compose down

shell:
	docker compose exec php sh

dump-autoload:
	docker compose exec php composer dump-autoload

check-config:
	@if not exist config.php ( echo config.php не найден. & echo Создай config.php из config.example.php и заполни значения. & exit /b 1 )
