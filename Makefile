setup:
	docker-compose exec php-fpm php bin/console doctrine:database:drop --if-exists --force
	docker-compose exec php-fpm php bin/console doctrine:database:create
	docker-compose exec php-fpm php bin/console doctrine:migrations:migrate --no-interaction
	docker-compose exec php-fpm php bin/console lexik:jwt:generate-keypair
	docker-compose exec php-fpm php bin/console app:init

ca:
	docker-compose run --rm php-fpm php vendor/bin/phpstan analyze src --level max

cq:
	docker-compose run --rm php-fpm php vendor/bin/php-cs-fixer fix

test:
	docker-compose exec php-fpm php bin/phpunit --testdox

reset-db:
	docker-compose exec php-fpm php bin/console doctrine:database:drop --if-exists --force
	docker-compose exec php-fpm php bin/console doctrine:database:create
	docker-compose exec php-fpm php bin/console doctrine:migrations:migrate --no-interaction
	docker-compose exec php-fpm php bin/console app:init