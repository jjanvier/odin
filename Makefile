.PHONY: build up down shell test clean help

# Colors for terminal output
YELLOW=\033[0;33m
GREEN=\033[0;32m
NC=\033[0m # No Color

help: ## Show this help
	@echo ""
	@echo "${YELLOW}Odin - The Celestial Planet Generator${NC}"
	@echo "${YELLOW}====================================${NC}"
	@echo ""
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "${GREEN}make %-15s${NC} %s\n", $$1, $$2}'
	@echo ""

build: ## Build the Docker image
	docker compose build

up: ## Start the Docker container
	docker compose up -d

down: ## Stop the Docker container
	docker compose down

shell: ## Open a shell in the Docker container
	docker compose exec odin bash

test: ## Run the tests
	docker compose exec odin ./vendor/bin/phpspec run

example: ## Generate an example planet image
	docker compose exec odin php examples.php

clean: ## Remove all generated images
	rm -rf rendered/*
	mkdir -p rendered
	@echo "Rendered directory cleaned"
