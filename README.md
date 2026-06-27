# Calendar Generator

A web application for generating calendars.

## Tech Stack

- **PHP** 8.2+
- **Symfony** 7.4 (LTS)
- **Doctrine DBAL** — database interactions (only DBAL, no ORM)
- **MySQL** 8.4
- **FrankenPHP** — application server
- **Twig** — server-side templating
- **AssetMapper** — frontend asset management
- **Stimulus** & **Symfony UX Turbo** — enhanced interactivity via native JavaScript

## Development

The project uses Docker Compose for local development. All commands (tests, composer, console) should be run inside the PHP container using `docker compose exec php`.

## Disclaimer

This software is provided "as is", without warranty of any kind. Use it at your own risk. The authors are not responsible for any data loss, system damage, financial loss, or other consequences resulting from the use of this software.
