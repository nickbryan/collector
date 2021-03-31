.DEFAULT_GOAL := help

# Colours used in help
GREEN    := $(shell tput -Txterm setaf 2)
WHITE    := $(shell tput -Txterm setaf 7)
YELLOW   := $(shell tput -Txterm setaf 3)
RESET    := $(shell tput -Txterm sgr0)

HELP_FUN = %help; \
	while(<>) { push @{$$help{$$2 // 'Misc'}}, [$$1, $$3] \
	if /^([a-zA-Z\-]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
	for (sort keys %help) { \
	print "${WHITE}$$_${RESET}\n"; \
	for (@{$$help{$$_}}) { \
	$$sep = " " x (32 - length $$_->[0]); \
	print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; \
	}; \
	print "\n"; } \
	$$sep = " " x (32 - length "help"); \
	print "${WHITE}Options${RESET}\n"; \
	print "  ${YELLOW}help${RESET}$$sep${GREEN}Prints this help${RESET}\n";

help:
	@echo "\nUsage: make ${YELLOW}<target>${RESET}\n\nThe following targets are available:\n";
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)

composer: ##@Composer
	docker exec -it collector-php-fpm /bin/bash -c 'composer ${command}'

composer-remove: ##@Composer
	docker exec -it collector-php-fpm /bin/bash -c 'composer remove ${package}'

composer-require: ##@Composer
	docker exec -it collector-php-fpm /bin/bash -c 'composer require ${package}'

composer-require-dev: ##@Composer
	docker exec -it collector-php-fpm /bin/bash -c 'composer require --dev ${package}'

composer-update: ##@Composer
	docker exec -it collector-php-fpm /bin/bash -c 'composer update ${package}'

console:
	docker exec -it collector-php-fpm /bin/bash -c './bin/console ${command}'

docker-up: ##@Local-development
	docker-compose -f devops/docker-compose.yaml up -d

docker-down: ##@Local-development
	docker-compose -f devops/docker-compose.yaml down

docker-logs: ##@Local-development
	docker-compose -f devops/docker-compose.yaml logs -f

migrate: ##@Migrations
	docker exec -it collector-php-fpm /bin/bash -c './bin/console doctrine:migrations:migrate --em=${module}'

migrate-first: ##@Migrations
	docker exec -it collector-php-fpm /bin/bash -c './bin/console doctrine:migrations:migrate --em=${module} first'

create-migration: ##@Migrations
	docker exec -it collector-php-fpm /bin/bash -c './bin/console doctrine:migrations:generate --em=${module}'

unit-test: ##@Test
	docker exec -it collector-php-fpm /bin/bash -c 'php bin/phpunit ${args}'

phpstan: ##@Quality-Analysis
	docker exec -it collector-php-fpm /bin/bash -c 'composer run-script phpstan'