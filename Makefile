# Crann Gra Theme — Makefile
.PHONY: help install lint fix

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Install PHP dependencies
	composer install

lint: ## Run PHPCS lint
	composer lint

fix: ## Auto-fix PHPCS issues
	composer fix
