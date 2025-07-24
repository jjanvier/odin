# Docker Environment for Odin

This document describes how to use the Docker environment set up for the Odin Celestial Planet Generator.

## Requirements

- Docker
- Docker Compose

## Getting Started

The project includes a Makefile with shortcuts for common operations:

```bash
# Show all available commands
make help

# Build the Docker image
make build

# Start the Docker container
make up

# Open a shell in the Docker container
make shell

# Run the tests
make test

# Generate an example planet image in the rendered directory
make example

# Stop the Docker container
make down

# Clean the rendered directory
make clean
```

## Configuration

- Images will be rendered in the `rendered` directory
- PHP with GD extension is configured in the Docker environment
- The environment variable `ODIN_RENDER_DIR` is set to `/app/rendered`

## Custom Usage

You can also use Docker Compose commands directly:

```bash
# Build and start the container
docker-compose up -d

# Execute a PHP script
docker-compose exec odin php your-script.php

# Stop and remove the container
docker-compose down
```
