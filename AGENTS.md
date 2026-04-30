# ANGENTS.md
## Guidelines for Coding Agents

This document defines the architectural, coding, and quality standards that **all coding agents** (human or AI-assisted) must follow when working on this project.  
Consistency, maintainability, and long-term evolvability are higher priorities than short-term speed.

---

## 1. Technology Stack

### Backend
- **PHP** (modern version compatible with the chosen Symfony LTS)
- **Symfony Framework – LTS version only**
  - Do not introduce non-LTS Symfony versions
  - Follow official Symfony best practices

### Frontend
- **Twig** for server-side rendering
- **Native JavaScript** preferred
- Avoid frontend frameworks unless explicitly approved
- Progressive enhancement over heavy client-side logic

### Testing
- **PHPUnit** for unit and integration tests
- Symfony testing tools where applicable

---

## 2. Architecture Overview

The project follows **Domain-Driven Design (DDD)** and is structured into **four explicit layers**: UI, Application, Domain, and Infrastructure.

### Dependency Rule (Strict)
> **Dependencies may only point inward**

UI → Application → Domain
Infrastructure → Application → Domain

- The **Domain layer has zero dependencies** on Symfony or infrastructure
- No circular dependencies between layers
- Framework code must never leak into Domain logic

---

## 3. Layer Responsibilities

### 3.1 Domain Layer
**The heart of the system**

Contains:
- Entities
- Value Objects
- Domain Services
- Domain Events
- Domain Interfaces (e.g. repositories, gateways)

Rules:
- No Symfony, Doctrine, or framework annotations
- No I/O, HTTP, DB, filesystem, or external services
- Pure PHP logic only
- Fully unit-testable in isolation

---

### 3.2 Application Layer
**Use cases and orchestration**

Contains:
- Application Services (Use Cases)
- Commands / Queries (CQRS if applicable)
- DTOs
- Interfaces required by the use cases

Rules:
- Coordinates domain objects
- Does **not** contain business rules (those belong to Domain)
- Talks to Domain via method calls
- Talks to Infrastructure only through interfaces
- Framework-agnostic as much as reasonably possible

---

### 3.3 Infrastructure Layer
**Technical details and integrations**

Contains:
- Doctrine implementations
- Symfony adapters
- External service integrations
- Filesystem, email, HTTP clients, message buses

Rules:
- Implements interfaces defined in Domain or Application
- Can depend on Symfony and third-party libraries
- Must not contain business logic
- Replaceable without changing Domain logic

---

### 3.4 UI Layer
**User interaction**

Contains:
- Symfony Controllers
- Twig templates
- Form types
- HTTP request/response handling

Rules:
- Thin controllers only
- No business logic
- Delegates all work to Application layer
- Handles validation and presentation concerns only

---

## 4. SOLID Principles

All code must adhere to **SOLID**:

- **S**ingle Responsibility  
  One reason to change per class

- **O**pen/Closed  
  Extend behavior without modifying existing code

- **L**iskov Substitution  
  Interfaces must be safely replaceable

- **I**nterface Segregation  
  Small, focused interfaces

- **D**ependency Inversion  
  Depend on abstractions, not implementations

Violations must be justified explicitly in code review.

---

## 5. PSR Standards

The project follows **all applicable and approved PSR standards**, including but not limited to:

- PSR-1 – Basic Coding Standard
- PSR-4 – Autoloading
- PSR-12 – Extended Coding Style
- PSR-3 – Logger Interface
- PSR-6 / PSR-16 – Caching
- PSR-7 / PSR-17 / PSR-18 (if HTTP abstractions are used)

All new code must comply automatically via tooling (PHP-CS-Fixer, PHPStan, etc.).

---

## 6. Symfony Usage Rules

- Prefer **Symfony core components** and **official Symfony bundles**
- External packages require strong justification
- Avoid “magic” features that reduce clarity
- Configuration over convention when it improves readability
- Use Dependency Injection everywhere

Examples of preferred tools:
- Symfony Validator
- Symfony Messenger
- Symfony Security
- Symfony Serializer
- Symfony Cache

---

## 7. Frontend Rules

- Twig templates must be:
  - Simple
  - Readable
  - Free of complex logic
- JavaScript:
  - Vanilla JS preferred
  - No framework unless explicitly approved
  - Use Stimulus only if already present in the project

Accessibility and performance matter.

---

## 8. Testing Requirements

### Mandatory
- **All new features must include unit tests**
- Domain logic must be tested independently
- Application services must be covered with use-case tests

### Guidelines
- One test class per production class (where applicable)
- Descriptive test names
- Tests must be deterministic and fast
- Avoid testing framework internals

No tests → no merge.

---

## 9. Code Quality Expectations

- Code must be readable before it is clever
- Favor explicitness over brevity
- Small, composable classes
- Meaningful naming (no abbreviations)
- Dead code must be removed

---

## 10. AI / Agent-Specific Instructions

When acting as an AI coding agent:

- Do not invent new architecture patterns
- Do not bypass layers “for convenience”
- Do not introduce new dependencies without approval
- Follow existing conventions in the codebase
- Ask for clarification instead of guessing when in doubt

The goal is **long-term maintainability**, not fast output.

---

## 11. Final Rule

> **If a solution feels quick but messy — it is wrong.**  
> Clean, explicit, boring code is preferred.
