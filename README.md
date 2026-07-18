# To-Do List Application

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net/downloads)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15%2B-336791.svg)](https://www.postgresql.org/)
[![Composer](https://img.shields.io/badge/Composer-2.0%2B-orange.svg)](https://getcomposer.org/)

A lightweight To-Do List application built with vanilla PHP 8.2+, following an MVC-inspired architecture. Backed by PostgreSQL (compatible with [Neon](https://neon.tech) serverless Postgres) and secured with CSRF protection, rate limiting, input sanitization, and strict HTTP security headers.

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
    - [Production — Nginx + PHP-FPM](#production--nginx--php-fpm)
      - [1. Create required directories](#1-create-required-directories)
      - [2. Disable the default PHP-FPM pool](#2-disable-the-default-php-fpm-pool)
      - [3. Copy config files](#3-copy-config-files)
      - [4. SSL certificate](#4-ssl-certificate)
      - [5. Start services](#5-start-services)
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
- HTTP security headers (CSP, HSTS, X-Frame-Options, etc.)
- Session-based task list caching (invalidated on writes)
- OPcache with JIT for reduced PHP overhead in production

## Technologies Used

| Layer      | Technology                                                     |
| ---------- | -------------------------------------------------------------- |
| Language   | [PHP 8.2+](https://www.php.net/)                               |
| Database   | [PostgreSQL](https://www.postgresql.org/) via PDO              |
| Hosting    | [Neon](https://neon.tech) (serverless Postgres) or self-hosted |
| Deps       | [Composer](https://getcomposer.org/) + `vlucas/phpdotenv`      |
| Frontend   | Plain HTML/CSS (no framework, no JS)                           |
| Web server | Nginx + PHP-FPM (production) or PHP built-in server (dev)      |

## Requirements

- PHP 8.2+ with `pdo`, `pdo_pgsql`, and `opcache` extensions
- Composer 2.0+
- PostgreSQL 15+ or a Neon serverless Postgres project
- Nginx + PHP-FPM (production) **or** PHP built-in server (development)

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
   git clone https://github.com/yourusername/todo-list-php.git
   cd todo-list-php
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

---

## Running the App

### Development — PHP built-in server

```bash
php -S localhost:8000 -t public
```

The app will be available at `http://localhost:8000`.

> `session.cookie_secure` is set to `1` in `public/index.php`. On plain HTTP the session cookie will not be sent by the browser. For local dev either use the Nginx setup below with a self-signed cert, or temporarily set `session.cookie_secure = 0` in `public/index.php`.

### Production — Nginx + PHP-FPM

#### 1. Create required directories

```bash
sudo mkdir -p /var/log/php-fpm
sudo chown www-data:www-data /var/log/php-fpm

sudo mkdir -p /var/lib/php/sessions/todo-list-php
sudo chown www-data:www-data /var/lib/php/sessions/todo-list-php
```

#### 2. Disable the default PHP-FPM pool

The default `www` pool uses the same socket path as the project pool and will conflict:

```bash
sudo mv /etc/php/8.2/fpm/pool.d/www.conf /etc/php/8.2/fpm/pool.d/www.conf.disabled
```

#### 3. Copy config files

From the project root:

```bash
sudo cp php-fpm.conf /etc/php/8.2/fpm/pool.d/todo-list-php.conf
sudo cp opcache.ini  /etc/php/8.2/fpm/conf.d/99-opcache.ini
sudo cp nginx.conf   /etc/nginx/sites-available/todo-list-php
sudo ln -s /etc/nginx/sites-available/todo-list-php /etc/nginx/sites-enabled/
```

#### 4. SSL certificate

For production, replace the paths in `nginx.conf` with your real certificate and key. For local HTTPS with a self-signed cert:

```bash
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/your-domain.key \
  -out /etc/ssl/certs/your-domain.crt \
  -subj "/CN=localhost"
```

Browsers will show a certificate warning for self-signed certs — click **Advanced → Proceed** to continue.

#### 5. Start services

```bash
sudo nginx -t
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

The app will be available at `https://localhost`.

## Usage

1. Open the app in your browser.
2. Click **+ Add New Task** to create a task (3–255 characters).
3. Use **Edit** to modify a task's name or completion status.
4. Toggle completion inline with **Mark Complete / Mark Incomplete**.
5. **Delete** a task with confirmation prompt.

## Project Structure

```plaintext
├── app/
│   ├── Config/
│   │   ├── Database.php        # Reads DB config from $_ENV
│   │   └── Paths.php           # Filesystem path helpers
│   ├── Controllers/
│   │   └── TaskController.php  # Request handling & orchestration
│   ├── Database/
│   │   ├── DatabaseManager.php         # PDO connection (PostgreSQL, persistent)
│   │   └── Migrations/
│   │       └── CreateTasksTable.php    # Run via migrate.php
│   ├── Helpers/
│   │   ├── CsrfGuard.php       # Token generation & verification
│   │   ├── InputSanitizer.php  # trim / strip_tags / htmlspecialchars
│   │   ├── PasswordHasher.php  # Argon2id hashing (available for auth)
│   │   └── RateLimiter.php     # Session-based rate limiting
│   ├── Interfaces/
│   │   ├── DatabaseConnectionInterface.php
│   │   └── TaskRepositoryInterface.php
│   ├── Models/
│   │   └── Task.php            # Task value object
│   ├── Repositories/
│   │   └── PdoTaskRepository.php   # SQL via PDO, session cache, hydrates Task models
│   ├── Routes/
│   │   ├── Router.php          # Regex-based GET/POST router
│   │   └── web.php             # Route definitions & DI wiring
│   ├── Validators/
│   │   └── TaskValidator.php   # Name length validation
│   └── Views/
│       ├── Errors/
│       │   ├── 404.php
│       │   └── 500.php
│       └── Tasks/
│           ├── index.php       # Task list
│           ├── create.php      # Create form
│           └── edit.php        # Edit form
├── public/
│   ├── index.php               # Front controller
│   └── css/
│       └── styles.css          # OKLCH palette, fluid type, no framework
├── migrate.php                 # Standalone CLI migration script
├── nginx.conf                  # Nginx server block (HTTP → HTTPS + PHP-FPM)
├── php-fpm.conf                # PHP-FPM pool config
├── opcache.ini                 # OPcache + JIT settings
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

| Measure               | Implementation                                         |
| --------------------- | ------------------------------------------------------ |
| CSRF protection       | `CsrfGuard` — token per session, `hash_equals` verify  |
| Input sanitization    | `InputSanitizer` — trim, strip_tags, htmlspecialchars  |
| Rate limiting         | `RateLimiter` — session-based, 10 req/60 s on create   |
| Password hashing      | `PasswordHasher` — Argon2id with tuned cost params     |
| Prepared statements   | All queries via PDO with `ATTR_EMULATE_PREPARES=false` |
| Secure session config | HttpOnly, SameSite=Strict, Secure, 1 h lifetime        |
| HTTP security headers | CSP, HSTS, X-Frame-Options, X-Content-Type-Options     |
| Error suppression     | `display_errors=0`; errors logged, not exposed         |
| Nginx hardening       | Blocks `.env`, `.log`, `.json`, dotfiles from serving  |

## Performance

| Optimisation          | Detail                                                              |
| --------------------- | ------------------------------------------------------------------- |
| Persistent PDO        | `ATTR_PERSISTENT=true` — FPM workers reuse DB connections           |
| Session task cache    | `all()` reads from `$_SESSION`; invalidated on write operations     |
| OPcache + JIT         | Bytecode cached; tracing JIT with 64 MB buffer (PHP 8.0+)           |
| Static asset caching  | Nginx serves CSS/JS with 30-day `Cache-Control`                     |
| Migration on demand   | `migrate.php` runs once via CLI — not on every HTTP request         |
| PHP-FPM dynamic pool  | 2–6 spare workers, recycled after 500 requests to prevent bloat     |

> **OPcache in development:** set `opcache.validate_timestamps=1` locally so PHP picks up file changes without restarting FPM. Keep it `0` in production.

## Contributing

Contributions are welcome. Please open an issue before submitting a pull request for significant changes.

## License

This project is licensed under the MIT License. See [LICENSE](./LICENSE) for details.

[Back to Top](#to-do-list-application)
