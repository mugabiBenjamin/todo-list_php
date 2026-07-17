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
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Usage](#usage)
  - [Project Structure](#project-structure)
  - [Routes](#routes)
  - [Security](#security)
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
- Automatic database migration on startup

## Technologies Used

| Layer       | Technology                                                     |
| ----------- | -------------------------------------------------------------- |
| Language    | [PHP 8.2+](https://www.php.net/)                               |
| Database    | [PostgreSQL](https://www.postgresql.org/) via PDO              |
| Hosting     | [Neon](https://neon.tech) (serverless Postgres) or self-hosted |
| Deps        | [Composer](https://getcomposer.org/) + `vlucas/phpdotenv`      |
| Frontend    | Plain HTML/CSS (no framework, no JS)                           |
| Dev server  | PHP built-in server                                            |

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

4. **Configure your database in `.env`** (see [Configuration](#configuration)).

5. **Start the development server:**

   ```bash
   php -S localhost:8000 -t public
   ```

   The app will be available at `http://localhost:8000`.

   > The `tasks` table is created automatically on first request via `CreateTasksTable::up()` — no manual migration step needed.

## Configuration

Edit `.env` with your PostgreSQL credentials:

```env
DB_HOST=your-project.region.aws.neon.tech
DB_PORT=5432
DB_USER=your_db_user
DB_PASSWORD=your_db_password
DB_NAME=your_db_name
```

The app connects over SSL (`sslmode=require`) by default, which is required for Neon and recommended for any remote Postgres instance. To use a local Postgres instance without SSL, update the DSN in `app/Database/DatabaseManager.php`.

## Usage

1. Open `http://localhost:8000` in your browser.
2. Click **+ Add New Task** to create a task (3–255 characters).
3. Use **Edit** to modify a task's name or completion status.
4. Toggle completion inline with **Mark Complete / Mark Incomplete**.
5. **Delete** a task with confirmation prompt.

## Project Structure

```plaintext
.
├── app
│   ├── Config
│   ├── Controllers
│   ├── Database
│   │   └── Migrations
│   ├── Helpers
│   ├── Interfaces
│   ├── Models
│   ├── Repositories
│   ├── Routes
│   ├── Validators
│   └── Views
│       ├── Errors
│       └── Tasks
└── public
    └── css
```

## Routes

| Method | Route            | Description                      |
| ------ | ---------------- | -------------------------------- |
| GET    | `/`              | Display all tasks                |
| GET    | `/create`        | Show task creation form          |
| POST   | `/tasks`         | Store a new task                 |
| GET    | `/edit/{id}`     | Show edit form for a task        |
| POST   | `/update/{id}`   | Update task name / status        |
| POST   | `/delete/{id}`   | Delete a task                    |

## Security

| Measure                  | Implementation                                         |
| ------------------------ | ------------------------------------------------------ |
| CSRF protection          | `CsrfGuard` — token per session, `hash_equals` verify  |
| Input sanitization       | `InputSanitizer` — trim, strip_tags, htmlspecialchars  |
| Rate limiting            | `RateLimiter` — session-based, 10 req/60 s on create   |
| Password hashing         | `PasswordHasher` — Argon2id with tuned cost params     |
| Prepared statements      | All queries via PDO with `ATTR_EMULATE_PREPARES=false` |
| Secure session config    | HttpOnly, SameSite=Strict, Secure, 1 h lifetime        |
| HTTP security headers    | CSP, HSTS, X-Frame-Options, X-Content-Type-Options     |
| Error suppression        | `display_errors=0`; errors logged, not exposed         |

## Contributing

Contributions are welcome. Please open an issue before submitting a pull request for significant changes.

## License

This project is licensed under the MIT License. See [LICENSE](./LICENSE) for details.

[Back to Top](#to-do-list-application)
