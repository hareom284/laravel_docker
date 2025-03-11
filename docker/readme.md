# Project Setup with Docker

Here are the simplifies setup, ensures environment parity, and follows best practices for performance and security.

## Table of Contents

- [Project Structure](#project-structure)
  - [Directory Structure](#directory-structure)
  - [Development Environment](#development-environment)
  - [Production Environment](#production-environment)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Clone the Repository](#clone-the-repository)
  - [Set Up the Development Environment](#set-up-the-development-environment)
- [Usage](#usage)
- [Production Environment](#production-environment-1)
  - [Building and Deploying](#building-and-deploying)
- [Technical Details](#technical-details)

## Project Structure

This project combines a standard Laravel application with Docker configurations, organized in a `docker` directory. Two Docker Compose files manage the environments:

- **`compose.dev.yaml`**: Configures the development environment.
- **`compose.prod.yaml`**: Configures the production environment.

### Directory Structure

```
project-root/
├── src/                  # Laravel application source code
├── docker/               # Docker configurations
│   ├── common/           # Shared settings across environments
│   ├── development/      # Development-specific Docker files
│   ├── production/       # Production-specific Docker files
├── compose.dev.yaml      # Docker Compose for development
├── compose.prod.yaml     # Docker Compose for production
├── .env.example          # Sample environment variables
└── ...                   # Other Laravel files (e.g., public/, routes/)
```

This layout separates shared and environment-specific configurations for maintainability.

### Development Environment

The development environment, defined in `compose.dev.yaml`, builds on the production setup to ensure consistency while adding developer-friendly features.

**Key Features:**
- **Parity with Production**: Mirrors production to reduce deployment surprises.
- **Development Tools**: Includes Xdebug and writable volume permissions.
- **Hot Reloading**: Code changes reflect instantly via volume mounts.
- **Services**: PHP-FPM, Nginx, Redis, Mysql, Node.js (via NVM).
- **Custom Dockerfiles**: Adds development tools to shared configurations.

See [Set Up the Development Environment](#set-up-the-development-environment) for setup instructions.

### Production Environment

The production environment, defined in `compose.prod.yaml`, prioritizes performance and security using multi-stage builds and minimal dependencies.

**Key Features:**
- **Optimized Images**: Multi-stage builds reduce size and enhance security.
- **Pre-Built Assets**: Assets are compiled during the build for instant deployment.
- **Health Checks**: Monitors service status for reliability.
- **Security Practices**: Excludes unnecessary packages; runs as non-root where possible.
- **Services**: Nginx, PHP-FPM, Redis, Mysql.


## Getting Started

Follow these steps to set up the project locally.

### Prerequisites

- **Docker**: [Install Docker](https://docs.docker.com/get-docker/)
- **Docker Compose**: [Install Docker Compose](https://docs.docker.com/compose/install/)

Verify installation:
```bash
docker --version
docker compose version
```

### Clone the Repository

```bash
git clone git@github.com:chenming1337/design-den.git
cd design-den
```

### Set Up the Development Environment

1. **Configure Environment Variables:**
   ```bash
   cp .env.example .env
   ```
   - Tip: Set `UID` and `GID` in `.env` to match your user ID (`id -u`) and group ID (`id -g`) for permission alignment.

2. **Start Docker Services:**
   ```bash
   docker compose -f compose.dev.yaml up -d
   ```

3. **Install Dependencies:**
   ```bash
   docker compose -f compose.dev.yaml exec workspace bash -c "composer install"
   ```

4. **Run Migrations:**
   ```bash
   docker compose -f compose.dev.yaml exec workspace php artisan migrate
   ```

5. **Access the Application:**
   Open [http://localhost:5001](http://localhost:5001) in your browser.

## Usage

Common commands for managing the development environment:

- **Access the Workspace Container:**
  ```bash
  docker compose -f compose.dev.yaml exec workspace bash
  ```
  - Includes Composer, Node.js, NPM, and Laravel tools.

- **Run Artisan Commands:**
  ```bash
  docker compose -f compose.dev.yaml exec workspace php artisan <command>
  ```
  - Example: `php artisan migrate`

- **Rebuild Containers:**
  ```bash
  docker compose -f compose.dev.yaml up -d --build
  ```

- **Stop Containers:**
  ```bash
  docker compose -f compose.dev.yaml down
  ```

- **View Logs:**
  ```bash
  docker compose -f compose.dev.yaml logs -f
  ```
  - For a specific service: `docker compose -f compose.dev.yaml logs -f web`

## Production Environment

The production setup emphasizes security and efficiency.

**Key Features:**
- **Optimized Images**: Multi-stage builds minimize size and vulnerabilities.
- **Environment Variables**: Securely manages sensitive data (e.g., API keys).
- **Non-Root Users**: Enhances security by limiting privileges.
- **Health Checks**: Ensures services run smoothly.
- **HTTPS**: Recommended for production (configure SSL separately).

### Building and Deploying

1. **Build the Production Image:**
   ```bash
   docker compose -f compose.prod.yaml build
   ```

2. **Run the Production Environment:**
   ```bash
   docker compose -f compose.prod.yaml up -d
   ```

<!-- 3. **Deploy:** -->
   <!-- Push the built image to a Docker-compatible host (e.g.. Digital Ocean) and configure environment variables via `.env` or your platform’s tools. -->

## Technical Details

- **PHP**: 8.2 FPM (optimized for Laravel)
- **MySQL**: 8.0 (database)
- **Redis**: Caching and session management
- **Nginx**: Web server for HTTP requests
- **Docker Compose**: Service orchestration
- **Health Checks**: Built into Docker Compose and Laravel

---

