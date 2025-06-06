setup:
	composer install
	cp -n .env.example .env
	php artisan key:gen --ansi
	php artisan migrate
	php artisan db:seed
	npm ci
	npm run build

frontend-dev:
	npm run	dev

start:
	php artisan serve

docker:
	docker compose up -d

stop-docker:
	docker compose down

install:
	composer install

compose-bash:
	docker compose run web bash

compose-setup: compose-build
	docker compose run web make setup

compose-build:
	docker compose build

lint:
	composer exec --verbose phpcs -- --standard=PSR12 app public resources routes

setup-test-db:
	mkdir -p database
	touch database/database.sqlite
	cp .env.example .env || true
	php artisan config:clear
	php artisan key:generate
	php artisan migrate:fresh --env=testing

test:
	php artisan test

test-coverage: setup-test-db
	XDEBUG_MODE=coverage php artisan test --coverage-clover=build/logs/clover.xml
