.PHONY: build
build: cs static test install ## Runs cs, static, and test targets

# https://www.gnu.org/software/make/manual/html_node/Force-Targets.html
always:

.PHONY: help
help:
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: install
install: ## Install depedencies
	docker compose run phpfpm rm -rf composer.lock
	docker compose run phpfpm composer install

.PHONY: cs
cs: ## Fixes coding standard issues with laravel/pint
	docker compose run phpfpm vendor/bin/pint --repair

.PHONY: coverage
coverage: ## Collects coverage with phpunit
	docker compose run phpfpm vendor/bin/phpunit --coverage-text --coverage-clover=.build/logs/clover.xml

.PHONY: test
test: ## Runs tests with phpunit
	docker compose run -e AZURE_STORAGE_CONNECTION_STRING="DefaultEndpointsProtocol=http;AccountName=devstoreaccount1;AccountKey=Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==;BlobEndpoint=http://azurite:10000/devstoreaccount1;QueueEndpoint=http://azurite:10001/devstoreaccount1;TableEndpoint=http://azurite:10002/devstoreaccount1;" phpfpm vendor/bin/phpunit

.PHONY: static
static: ## Runs static analyzers
	docker compose run phpfpm vendor/bin/phpstan --memory-limit=2G

.PHONY: baseline
baseline: ## Generate baseline files
	docker compose run phpfpm vendor/bin/phpstan --memory-limit=2G --generate-baseline

.PHONY: clean
clean:   ## Cleans up build and vendor files
	rm -rf vendor composer.lock .build

.PHONY: bc
bc: ## Check for breaking changes since last release
	docker run --env GITHUB_REPOSITORY="Azure-OSS/azure-storage-php-adapter-laravel" -u $(shell id -u) -v $(shell pwd):/app nyholm/roave-bc-check-ga
