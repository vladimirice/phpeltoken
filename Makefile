docker_compose_up d_up:
	docker-compose up -d --build

docker_machine_start d_m_s:
	docker-machine start

d_up_force:
	docker-compose up -d --build --force-recreate

d_stop:
	docker-compose stop

d_db:
	docker-compose exec --user=postgres db /bin/bash

d_php:
	docker-compose exec --user=www-data php /bin/bash

docker_doctrine_migrations_difference d_dmd:
	docker-compose exec -T --user=www-data php bin/console doctrine:migrations:diff

docker_doctrine_migrations_migrate d_dmm:
	docker-compose exec -T --user=www-data php bin/console doctrine:migrations:migrate

d_ccl:
	docker-compose exec -T --user=www-data php bin/console cache:clear

d_assets_install:
	docker-compose exec -T --user=www-data php bin/console assets:install

d_deploy_prod d_dep:
	vendor/bin/dep deploy -vvv

docker_init_dev d_init_dev:
	docker-compose exec -T --user=www-data php composer install -o
	docker-compose exec -T --user=www-data php bin/console doctrine:database:drop --if-exists --force
	docker-compose exec -T --user=www-data php bin/console doctrine:database:create --if-not-exists

d_fixtures d_f:
	docker-compose exec -T --user=www-data php bin/console hautelook:fixtures:load -n --purge-with-truncate

d-update-dev docker_update_dev:
	docker-compose exec -T --user=www-data php bin/console doctrine:migration:migrate -n

docker_set_hosts_mac:
	sudo /bin/bash ./etc/docker/etchosts.sh update phpeltoken.dev                              192.168.64.3     /private/etc/hosts
	sudo /bin/bash ./etc/docker/etchosts.sh update postgres.phpeltoken.dev                     192.168.64.3     /private/etc/hosts

docker_set_hosts_mac_localhost:
	sudo /bin/bash ./etc/docker/etchosts.sh update phpeltoken.dev                              127.0.0.1     /private/etc/hosts
	sudo /bin/bash ./etc/docker/etchosts.sh update postgres.phpeltoken.dev                     127.0.0.1     /private/etc/hosts

checker_setup:
	./vendor/bin/phpcs --config-set encoding utf-8
	./vendor/bin/phpcs --config-set installed_paths "../../../vendor/endouble/symfony3-custom-coding-standard"
	./vendor/bin/phpcs --config-set default_standard ruleset.xml