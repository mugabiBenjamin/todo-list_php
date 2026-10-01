# To-Do List Application

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net/downloads)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15%2B-336791.svg)](https://www.postgresql.org/)
[![Composer](https://img.shields.io/badge/Composer-2.0%2B-orange.svg)](https://getcomposer.org/)
[![Docker](https://img.shields.io/badge/Docker-28%2B-2496ED.svg)](https://www.docker.com/)

A lightweight To-Do List application built with vanilla PHP 8.2+, following a clean MVC-inspired architecture.  
Backed by PostgreSQL via [Neon](https://neon.tech) serverless Postgres, deployed on [Render](https://render.com) using Docker, and secured with CSRF protection, rate limiting, Argon2id password hashing, input sanitization, and strict HTTP security headers.

[**Live demo:**](https://todo-list-php.onrender.com)

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
    - [Development - PHP built-in server](#development---php-built-in-server)
    - [Production - Docker](#production---docker)
  - [Deploying to Render](#deploying-to-render)
    - [Running migrations on Render (free tier)](#running-migrations-on-render-free-tier)
    - [Key deployment notes](#key-deployment-notes)
  - [CI/CD](#cicd)
  - [Usage](#usage)
  - [Project Structure](#project-structure)
  - [Routes](#routes)
  - [Security](#security)
  - [Performance](#performance)
  - [Contributing](#contributing)
  - [License](#license)

## Features

- User registration and login with Argon2id password hashing
- Session-based authentication with secure cookie flags
- Create, edit, delete, and toggle completion of personal tasks
- Tasks are strictly scoped to the authenticated user
- CSRF protection on all state-changing requests
- Rate limiting:
  - Login: 5 attempts / 5 minutes
  - Registration: 3 attempts / hour
  - Task creation: 10 requests / 60 seconds
- Input sanitization and server-side validation
- Secure session configuration (HttpOnly, SameSite=Strict, Secure when HTTPS)
- HTTP security headers (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, etc.)
- Session-based task list caching (invalidated on writes)
- OPcache with JIT for reduced PHP overhead in production
- Dockerized with multi-stage builds for lean production images
- GitHub Actions CI pipeline with automated Docker build validation

## Technologies Used

| Layer       | Technology                                                |
| ----------- | --------------------------------------------------------- |
| Language    | [PHP 8.2+](https://www.php.net/)                          |
| Database    | [PostgreSQL](https://www.postgresql.org/) via PDO         |
| Hosting DB  | [Neon](https://neon.tech) (serverless Postgres)           |
| Hosting App | [Render](https://render.com) (Docker Web Service)         |
| Deps        | [Composer](https://getcomposer.org/) + `vlucas/phpdotenv` |
| Frontend    | Plain HTML/CSS (no framework, no JavaScript)              |
| Web server  | Nginx + PHP-FPM (production) or PHP built-in server (dev) |
| Container   | Docker with multi-stage build                             |
| CI/CD       | GitHub Actions                                            |

## Requirements

- PHP 8.2+ with `pdo`, `pdo_pgsql`, and `opcache` extensions
- Composer 2.0+
- PostgreSQL 15+ or a Neon serverless Postgres project
- Docker 28+ (production) **or** PHP built-in server (development)

### Install PHP 8.2 and Nginx on Ubuntu

Ubuntu's default repositories may not provide PHP 8.2. Use the Ondřej Surý PHP repository:

```bash
sudo apt update
sudo apt install -y lsb-release ca-certificates curl gnupg

# Add the repository signing key
curl -fsSL https://packages.sury.org/php/apt.gpg \
  | sudo gpg --dearmor -o /usr/share/keyrings/sury-php.gpg

# Add the PHP repository
echo "deb [signed-by=/usr/share/keyrings/sury-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" \
  | sudo tee /etc/apt/sources.list.d/sury-php.list

sudo apt update

# Install Nginx, PHP 8.2-FPM, PostgreSQL support, and OPcache
sudo apt install -y nginx php8.2-fpm php8.2-pgsql php8.2-opcache
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

The application connects over SSL (`sslmode=require`) by default, which is required for Neon and recommended for any remote Postgres instance.

> **Note:** The Neon connection pooler (port 6543) requires outbound TCP on that port. If your network blocks non-standard ports, use the direct connection on port 5432 instead.

## Database Migration

Migrations are **not** run automatically on startup. Run them once manually before first use, and again after any schema changes:

```bash
php migrate.php
```

Expected output:

```plain
[OK] Migrations completed successfully.
```

This creates:

- `users` table
- `tasks` table
- `user_id` foreign key on `tasks` (with `ON DELETE CASCADE`)

The script is safe to re-run (uses `IF NOT EXISTS` / `ADD COLUMN IF NOT EXISTS`).

**On Render (free tier):** the Pre-Deploy Command feature is paywalled, so migrations must be triggered manually when needed. See [Deploying to Render](#deploying-to-render).

## Running the App

### Development - PHP built-in server

```bash
php -S localhost:8000 -t public
```

The app will be available at `http://localhost:8000`.

> `session.cookie_secure` is resolved dynamically in `public/index.php` by checking the `HTTPS` and `X-Forwarded-Proto` headers. On plain HTTP in local development the secure flag is automatically disabled.

### Production - Docker

Build and run the container locally:

```bash
docker build -t todo-list-php .
docker run -p 8080:8080 --env-file .env todo-list-php
```

The app will be available at `http://localhost:8080`.

The container runs Nginx + PHP-FPM. The `PORT` environment variable is resolved at startup via the entrypoint script (defaults to `8080` if not set).

## Deploying to Render

1. Push your code to GitHub.
2. Create a new **Web Service** on [Render](https://render.com).
3. Connect your GitHub repository and select **Docker** as the runtime.
4. Under **Environment → Environment Variables**, add each variable from `.env.example` individually (one key/value pair per field). Do **not** use the file import option.
5. Leave **Docker Command** and **Pre-Deploy Command** blank. Render will use the `ENTRYPOINT` defined in the Dockerfile.
6. Set **Auto-Deploy** to **After CI tests pass** so broken builds never reach production.
7. Set **Health Check Path** to `/`.
8. Deploy. Render builds the Docker image and starts the container automatically.

Render handles TLS termination at the edge — no SSL configuration is needed inside the container.

### Running migrations on Render (free tier)

The Pre-Deploy Command is a paid feature. To run a migration manually:

1. Go to **Settings → Docker Command** and set it to `php migrate.php`.
2. Trigger a manual deploy from the **Deploys** tab. You will see `[OK] Migrations completed successfully.` followed by `Application exited early` — this is expected.
3. Go back to **Settings → Docker Command** and **clear it** (leave it blank).
4. Trigger another manual deploy. The container now starts normally using the `ENTRYPOINT`.

> **Important:** Always clear the Docker Command after the migration deploy. Leaving `php migrate.php` there will cause every subsequent deploy to exit immediately.

### Key deployment notes

- Environment variables must be set in the Render dashboard (not via a `.env` file upload). The PHP-FPM pool is configured with `clear_env = no` so that Render's injected variables are visible to PHP workers via `$_ENV`.
- The `php-fpm` binary in the `php:8.2-fpm` Docker image is named `php-fpm` (not `php-fpm8.2`).
- The PHP-FPM Unix socket is at `/run/php/php-fpm.sock`. Both `docker/php-fpm.conf` and `docker/nginx.conf` reference this path.

## CI/CD

A GitHub Actions pipeline runs on every push to `main` and on all pull requests targeting `main`.

**`validate` job:**

- Validates `composer.json`
- Installs Composer dependencies
- Lints all PHP files with `php -l`
- Verifies `.env.example` and `Dockerfile` are present

**`build` job** (runs only if `validate` succeeds):

- Builds the Docker image using the production `Dockerfile`
- Uses GitHub Actions layer caching (`type=gha`) to speed up subsequent builds

Render is configured to auto-deploy **after CI tests pass**, so a failed pipeline blocks the deployment automatically.

## Usage

1. Open the app in your browser.
2. Register a new account or log in.
3. Click **+ Add New Task** to create a task (3-255 characters).
4. Use **Edit** to modify a task’s name or completion status.
5. Toggle completion inline with **Mark Complete / Mark Incomplete**.
6. **Delete** a task (with confirmation prompt).
7. Log out when finished.

## Project Structure

```plaintext
├── .github/
│   └── workflows/            # GitHub Actions CI pipeline
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Database/
│   │   └── Migrations/
│   ├── Helpers/
│   ├── Interfaces/
│   ├── Middleware/
│   │   └── AuthMiddleware.php
│   ├── Models/
│   ├── Repositories/
│   ├── Routes/
│   ├── Services/
│   ├── Validators/
│   └── Views/
│       ├── Auth/
│       ├── Errors/
│       └── Tasks/
├── docker/
├── public/
│   ├── index.php                   # Front controller
│   └── css/                        # OKLCH palette, fluid type, no framework
├── Dockerfile                      # Multi-stage production build
├── docker-entrypoint.sh            # PORT substitution + PHP-FPM + Nginx startup
├── migrate.php                     # Standalone CLI migration script
├── .env.example
├── composer.json
└── README.md
```

## Routes

| Method | Route          | Description                            | Auth required |
| ------ | -------------- | -------------------------------------- | ------------- |
| GET    | `/login`       | Show login form                        | No            |
| POST   | `/login`       | Authenticate user                      | No            |
| GET    | `/register`    | Show registration form                 | No            |
| POST   | `/register`    | Create new user account                | No            |
| POST   | `/logout`      | Destroy session                        | Yes           |
| GET    | `/`            | Display the authenticated user’s tasks | Yes           |
| GET    | `/create`      | Show task creation form                | Yes           |
| POST   | `/tasks`       | Store a new task                       | Yes           |
| GET    | `/edit/{id}`   | Show edit form for a task              | Yes           |
| POST   | `/update/{id}` | Update task name / completion status   | Yes           |
| POST   | `/delete/{id}` | Delete a task                          | Yes           |

## Security

| Measure               | Implementation                                                                             |
| --------------------- | ------------------------------------------------------------------------------------------ |
| CSRF protection       | `CsrfGuard` - token per session, verified with `hash_equals`                               |
| Password hashing      | `PasswordHasher` - Argon2id with tuned memory/time/threads costs + rehash support          |
| Input sanitization    | `InputSanitizer` - trim, strip_tags, htmlspecialchars                                      |
| Rate limiting         | `RateLimiter` - session-based (login, register, task creation)                             |
| Prepared statements   | All queries via PDO with `ATTR_EMULATE_PREPARES = false`                                   |
| Ownership checks      | Every task operation requires matching `user_id`                                           |
| Secure session config | HttpOnly, SameSite=Strict, Secure (env-aware), 1-hour lifetime, regenerate on login/logout |
| HTTP security headers | CSP, X-Frame-Options DENY, X-Content-Type-Options, Referrer-Policy, Cache-Control          |
| TLS                   | Terminated at Render’s edge                                                                |
| Error suppression     | `display_errors = 0`; errors logged, never exposed                                         |
| Nginx hardening       | Blocks `.env`, `.log`, `.json`, dotfiles, etc.                                             |

## Performance

| Optimisation         | Detail                                                           |
| -------------------- | ---------------------------------------------------------------- |
| Persistent PDO       | `ATTR_PERSISTENT = true` - FPM workers reuse DB connections      |
| Session task cache   | `all()` reads from `$_SESSION`; invalidated on every write       |
| OPcache + JIT        | Bytecode cached; tracing JIT with 64 MB buffer                   |
| Static asset caching | Nginx serves CSS with 30-day `Cache-Control`                     |
| Migration on demand  | CLI-only; never runs on HTTP requests                            |
| PHP-FPM dynamic pool | 2-6 spare workers, recycled after 500 requests                   |
| Docker multi-stage   | Composer stage excluded from final image                         |
| CI layer caching     | GitHub Actions caches Docker layers across builds via `type=gha` |

> **OPcache in development:** `opcache.validate_timestamps = 0` is a production setting. The PHP built-in server does not load the Docker OPcache configuration, so this has no effect locally.

## Contributing

Contributions are welcome. Please open an issue before submitting a pull request for significant changes.

## License

This project is licensed under the MIT License. See [LICENSE](./LICENSE) for details.

[Back to Top](#to-do-list-application)
