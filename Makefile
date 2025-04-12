# Nome do seu serviço PHP no docker-compose (ajuste se for diferente)
PHP_SERVICE=app

# Subir os containers
up:
	docker-compose up -d

# Parar os containers
down:
	docker-compose down

# Acessar o container PHP
bash:
	docker-compose exec $(PHP_SERVICE) bash

# Instalar dependências PHP
install:
	docker-compose exec $(PHP_SERVICE) composer install

# Rodar migrations
migrate:
	docker-compose exec $(PHP_SERVICE) php artisan migrate

# Rodar seeder
seed:
	docker-compose exec $(PHP_SERVICE) php artisan db:seed

# Rodar queue worker
queue:
	docker-compose exec $(PHP_SERVICE) php artisan queue:work

# Limpar cache e configs
reset:
	docker-compose exec $(PHP_SERVICE) php artisan optimize:clear

# Testes
test:
	docker-compose exec $(PHP_SERVICE) php artisan test
