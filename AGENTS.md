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

### Environment
- Use **Docker Compose** for the full stack (PHP/FrankenPHP + MySQL).
- `compose up -d` to start the environment.

### Testing
- Run tests using `bin/phpunit`.
- All new features **must** include unit or integration tests.

### Database
- Use **Doctrine Migrations** for schema changes.

### Deployment/Infrastructure
- Uses **FrankenPHP** as the application server.

---

## 4. Code Quality & Standards

- **SOLID Principles**: Mandatory.
- **PSR Standards**: Compliance with PSR-1, PSR-4, PSR-12.
- **No Magic**: Avoid Symfony "magic" features that reduce clarity. Prefer explicit configuration and Dependency Injection.
- **Clean Code**: Favor explicitness and readability over cleverness.

