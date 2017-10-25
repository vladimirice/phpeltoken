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

d_init_blockchain:
	make d_i_db
	docker-compose exec --user=www-data php bin/console app:init_blockchain_command
	docker-compose exec --user=www-data php bin/console app:create_sample_transactions_command
	docker-compose exec --user=www-data php bin/console app:check_blockchain_command
	docker-compose exec --user=www-data php bin/console app:show_available_input_command

d_publish_transaction d_p:
	docker-compose exec --user=www-data php bin/console app:publish_transaction_command

d_mine_block d_m:
	docker-compose exec --user=www-data php bin/console app:mine_new_block_command

d_show_available_input d_s:
	docker-compose exec --user=www-data php bin/console app:show_available_input_command

d_check_blockchain_validity:
	docker-compose exec --user=www-data php bin/console app:check_blockchain_command

d_check_transaction_pool_validity:
	docker-compose exec --user=www-data php bin/console app:check_transaction_pool_command


docker_doctrine_migrations_generate d_dmg:
	docker-compose exec -T --user=www-data php bin/console doctrine:migrations:generate

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
	docker_init_db

docker_init_db d_i_db:
	docker-compose exec -T --user=www-data php bin/console doctrine:database:drop --if-exists --force
	docker-compose exec -T --user=www-data php bin/console doctrine:database:create --if-not-exists
	docker-compose exec -T --user=www-data php bin/console doctrine:migrations:migrate -n
	docker-compose exec -T --user=www-data php bin/console hautelook:fixtures:load -n --purge-with-truncate

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