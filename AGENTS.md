# AGENTS.md
## Guidelines for Coding Agents

This document defines the architectural, coding, and quality standards that **all coding agents** (human or AI-assisted) must follow when working on this project.

---

## 1. Technology Stack

### Backend
- **PHP 8.2+**
- **Symfony 7.4 (LTS)**
- **Doctrine DBAL** (for database interactions)
- **IMPORTANT**: **Doctrine ORM must not be used** — only DBAL (queries, connections, schema) is permitted.

### Database
- **MySQL 8.4**

### Frontend
- **Twig** for server-side rendering
- **AssetMapper** for asset management
- **Stimulus** & **Turbo** (Symfony UX)
- **Native JavaScript** preferred

### Testing
- **PHPUnit**

---

## 2. Architecture Overview

The project follows **Domain-Driven Design (DDD)** with a strict **Dependency Rule**:
> **Dependencies may only point inward** (UI $\to$ Application $\to$ Domain $\gets$ Infrastructure)

- **Domain Layer**: Pure PHP. No Symfony, no Doctrine, no I/O.
- **Application Layer**: Orchestrates domain objects. Framework-agnostic.
- **Infrastructure Layer**: Implementations of domain/application interfaces (DBAL, Mailer, etc.).
- **UI Layer**: Symfony Controllers, Twig, and HTTP handling.

---

## 3. Development Workflow

### Container vs Local Execution
When Docker or Podman is available, you **must** run all project commands (tests, composer, migrations, console tasks, etc.) inside the running `php` container. If Docker/Podman is not installed, not running, or fails, fall back to running the commands locally on your host.

- **Available software identification**
  - Check if `docker compose`, `podman compose`, `docker-compose`, `podman-compose` are available, use the available command name instead of `docker compose` in following points
- **Starting Environment**:
  - Container: `docker compose up -d`
- **Testing**:
  - Container: `docker compose exec php bin/phpunit`
  - Local: `bin/phpunit`
- **Composer**:
  - Container: `docker compose exec php composer <command>`
  - Local: `composer <command>`
- **Symfony Console**:
  - Container: `docker compose exec php bin/console <command>`
  - Local: `bin/console <command>`

### Environment
- Use **Docker Compose** (or **Podman Compose**) for the full stack (PHP/FrankenPHP + MySQL).
- `docker compose up -d` to start the environment.

### Testing
- Run tests using the container command `docker compose exec php bin/phpunit` when possible. Fall back to local `bin/phpunit` if the container is not available.
- All new features **must** include unit or integration tests.

### Database
- Use **Doctrine Migrations** for schema changes (e.g. via `docker compose exec php bin/console doctrine:migrations:migrate` or locally `bin/console doctrine:migrations:migrate`).

### Deployment/Infrastructure
- Uses **FrankenPHP** as the application server.

---

## 4. Code Quality & Standards

- **SOLID Principles**: Mandatory.
- **PSR Standards**: Compliance with PSR-1, PSR-4, PSR-12.
- **No Magic**: Avoid Symfony "magic" features that reduce clarity. Prefer explicit configuration and Dependency Injection.
- **Clean Code**: Favor explicitness and readability over cleverness.

