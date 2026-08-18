# To-Do List Application

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net/downloads)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15%2B-336791.svg)](https://www.postgresql.org/)
[![Composer](https://img.shields.io/badge/Composer-2.0%2B-orange.svg)](https://getcomposer.org/)
****[![Docker](https://img.shields.io/badge/Docker-28%2B-2496ED.svg)](https://www.docker.com/)

A lightweight To-Do List application built with vanilla PHP 8.2+, following an MVC-inspired architecture. Backed by PostgreSQL via [Neon](https://neon.tech) serverless Postgres, deployed on [Render](https://render.com) using Docker, and secured with CSRF protection, rate limiting, input sanitization, and strict HTTP security headers.

## Table of Contents

- [To-Do List Application](#to-do-list-application)
  - [Table of Contents](#table-of-contents)
  - [Features](#features)
  - [Technologies Used](#technologies-used)
  - [Requirements](#requirements)
    - [Install PHP 8.2 and Nginx on Ubuntu](#install-php-82-and-nginx-on-ubuntu)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Database Migration](#database-migration)
  - [Running the App](#running-the-app)
    - [Development — PHP built-in server](#development--php-built-in-server)
    - [Production — Docker](#production--docker)
  - [Deploying to Render](#deploying-to-render)
  - [CI/CD](#cicd)
  - [Usage](#usage)
  - [Project Structure](#project-structure)
  - [Routes](#routes)
  - [Security](#security)
  - [Performance](#performance)
  - [Contributing](#contributing)
  - [License](#license)

## Features

- Create, edit, and delete tasks
- Mark tasks as complete or incomplete
- CSRF protection on all state-changing requests
- Rate limiting on task creation (10 requests / 60 s)
- Input sanitization and server-side validation
- Secure session configuration (HttpOnly, SameSite Strict, Secure)
- HTTP security headers (CSP, X-Frame-Options, X-Content-Type-Options, etc.)
- Session-based task list caching (invalidated on writes)
- OPcache with JIT for reduced PHP overhead in production
- Dockerized with multi-stage builds for lean production images
- GitHub Actions CI pipeline with automated Docker build validation

## Technologies Used

| Layer      | Technology                                                          |
| ---------- | ------------------------------------------------------------------- |
| Language   | [PHP 8.2+](https://www.php.net/)                                    |
| Database   | [PostgreSQL](https://www.postgresql.org/) via PDO                   |
| Hosting DB | [Neon](https://neon.tech) (serverless Postgres)                     |
| Hosting App| [Render](https://render.com) (Docker Web Service)                   |
| Deps       | [Composer](https://getcomposer.org/) + `vlucas/phpdotenv`           |
| Frontend   | Plain HTML/CSS (no framework, no JS)                                |
| Web server | Nginx + PHP-FPM (production) or PHP built-in server (dev)           |
| Container  | Docker with multi-stage build                                       |
| CI/CD      | GitHub Actions                                                      |

## Requirements

- PHP 8.2+ with `pdo`, `pdo_pgsql`, and `opcache` extensions
- Composer 2.0+
- PostgreSQL 15+ or a Neon serverless Postgres project
- Docker 28+ (production) **or** PHP built-in server (development)

### Install PHP 8.2 and Nginx on Ubuntu

Ubuntu's default repos may not include PHP 8.2. Add the Ondřej Surý PPA first:

```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install nginx php8.2-fpm php8.2-pgsql php8.2-opcache -y
```

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/mugabiBenjamin/todo-list_php.git
   cd todo-list_php
   ```

2. **Install dependencies:**

   ```bash
   composer install
   ```

3. **Set up the environment file:**

   ```bash
   cp .env.example .env
   ```

4. **Configure your database credentials in `.env`** (see [Configuration](#configuration)).

5. **Run the database migration** (see [Database Migration](#database-migration)).

6. **Start the server** (see [Running the App](#running-the-app)).

## Configuration

Edit `.env` with your PostgreSQL credentials:

```env
DB_HOST=your-project.region.aws.neon.tech
DB_PORT=5432
DB_USER=your_db_user
DB_PASSWORD=your_db_password
DB_NAME=your_db_name
```

The app connects over SSL (`sslmode=require`) by default, which is required for Neon and recommended for any remote Postgres instance.

> **Note:** The Neon connection pooler (port 6543) requires outbound TCP on that port. If your network blocks non-standard ports, use the direct connection on port 5432 instead.

## Database Migration

The migration is **not** run automatically on startup. Run it once manually before first use, and again after any schema changes:

```bash
php migrate.php
```

Expected output:

```bash
[OK] Migration completed successfully.
```

This creates the `tasks` table if it does not already exist. It is safe to re-run.

On Render, this is wired as a **pre-deploy command** so it runs automatically against the live Neon database before each deployment (see [Deploying to Render](#deploying-to-render)).

## Running the App

### Development — PHP built-in server

```bash
php -S localhost:8000 -t public
```

The app will be available at `http://localhost:8000`.

> `session.cookie_secure` is resolved dynamically in `public/index.php` by checking the `HTTPS` and `X-Forwarded-Proto` headers. On plain HTTP in local dev, the secure flag will be off automatically — no manual change needed.

### Production — Docker

Build and run the container locally:

```bash
docker build -t todo-list-php .
docker run -p 8080:8080 --env-file .env todo-list-php
```

The app will be available at `http://localhost:8080`.

The container runs Nginx + PHP-FPM. The `PORT` environment variable is resolved at startup via the entrypoint script — defaulting to `8080` if not set.

## Deploying to Render

1. Push your code to GitHub.
2. Create a new **Web Service** on [Render](https://render.com).
3. Connect your GitHub repository and select **Docker** as the runtime.
4. Add all variables from `.env.example` under **Environment → Environment Variables**.
5. Under **Settings → Deploy**, set the **Pre-Deploy Command** to:

   ```php
   php migrate.php
   ```

6. Deploy. Render will build the Docker image, run the migration against Neon, then switch traffic to the new container.

Render handles TLS termination at their edge — no SSL configuration is needed inside the container.

## CI/CD

A GitHub Actions pipeline runs on every push to `main` and on all pull requests targeting `main`.

**`validate` job:**

- Validates `composer.json`
- Installs Composer dependencies
- Lints all PHP files with `php -l`
- Verifies `.env.example` and `Dockerfile` are present

**`build` job** (runs only if `validate` passes):

- Builds the Docker image using the production `Dockerfile`
- Uses GitHub Actions layer caching to speed up subsequent builds

Render auto-deploys on every push to `main` via its GitHub integration, independently of the Actions pipeline.

## Usage

1. Open the app in your browser.
2. Click **+ Add New Task** to create a task (3–255 characters).
3. Use **Edit** to modify a task's name or completion status.
4. Toggle completion inline with **Mark Complete / Mark Incomplete**.
5. **Delete** a task with confirmation prompt.

## Project Structure

```plaintext
├── .github/
│   └── workflows/
│       └── ci.yml              # GitHub Actions CI pipeline
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Database/
│   ├── Helpers/
│   ├── Interfaces/
│   ├── Models/
│   ├── Repositories/
│   ├── Routes/
│   ├── Validators/
│   └── Views/
│       ├── Errors/
│       └── Tasks/
├── docker/
├── public/
│   ├── index.php               # Front controller
│   └── css/
│       └── styles.css          # OKLCH palette, fluid type, no framework
├── Dockerfile                  # Multi-stage production build
├── docker-entrypoint.sh        # PORT substitution + service startup
├── migrate.php                 # Standalone CLI migration script
├── .env.example
├── composer.json
└── README.md
```

## Routes

| Method | Route          | Description               |
| ------ | -------------- | ------------------------- |
| GET    | `/`            | Display all tasks         |
| GET    | `/create`      | Show task creation form   |
| POST   | `/tasks`       | Store a new task          |
| GET    | `/edit/{id}`   | Show edit form for a task |
| POST   | `/update/{id}` | Update task name / status |
| POST   | `/delete/{id}` | Delete a task             |

## Security

| Measure               | Implementation                                                      |
| --------------------- | ------------------------------------------------------------------- |
| CSRF protection       | `CsrfGuard` — token per session, `hash_equals` verify               |
| Input sanitization    | `InputSanitizer` — trim, strip_tags, htmlspecialchars               |
| Rate limiting         | `RateLimiter` — session-based, 10 req/60 s on create                |
| Password hashing      | `PasswordHasher` — Argon2id with tuned cost params                  |
| Prepared statements   | All queries via PDO with `ATTR_EMULATE_PREPARES=false`              |
| Secure session config | HttpOnly, SameSite=Strict, Secure (env-aware), 1 h lifetime         |
| HTTP security headers | CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy       |
| TLS                   | Terminated at Render's edge; HSTS not set inside the container      |
| Error suppression     | `display_errors=0`; errors logged, not exposed                      |
| Nginx hardening       | Blocks `.env`, `.log`, `.json`, dotfiles from being served          |

## Performance

| Optimisation          | Detail                                                                |
| --------------------- | --------------------------------------------------------------------- |
| Persistent PDO        | `ATTR_PERSISTENT=true` — FPM workers reuse DB connections             |
| Session task cache    | `all()` reads from `$_SESSION`; invalidated on write operations       |
| OPcache + JIT         | Bytecode cached; tracing JIT with 64 MB buffer (PHP 8.0+)             |
| Static asset caching  | Nginx serves CSS/JS with 30-day `Cache-Control`                       |
| Migration on demand   | Runs once via Render pre-deploy command — not on every HTTP request   |
| PHP-FPM dynamic pool  | 2–6 spare workers, recycled after 500 requests to prevent bloat       |
| Docker multi-stage    | Composer stage excluded from final image — leaner production artifact |
| CI layer caching      | GitHub Actions caches Docker layers across builds via `type=gha`      |

> **OPcache in development:** `opcache.validate_timestamps` is `0` in `docker/opcache.ini` (production). For local dev with the PHP built-in server, OPcache is not active so this has no effect.

## Contributing

Contributions are welcome. Please open an issue before submitting a pull request for significant changes.

## License

This project is licensed under the MIT License. See [LICENSE](./LICENSE) for details.

[Back to Top](#to-do-list-application)
