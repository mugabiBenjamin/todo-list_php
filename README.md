## To-Do List Application

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/downloads)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com)
[![Composer](https://img.shields.io/badge/Composer-2.0%2B-orange.svg)](https://getcomposer.org/)


This is a simple To-Do List application built using PHP. The application allows users to create, update, delete, and mark tasks as completed. The project follows an MVC architecture and includes basic security features such as CSRF protection and input validation.

## Table of Contents

1. [Features](#features)
2. [Technologies Used](#technologies-used)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Usage](#usage)
6. [Project Structure](#project-structure)
7. [Routes](#routes)
8. [Contributing](#contributing)
9. [License](#license)
10. [Acknowledgments](#acknowledgments)
11. [Support](#support)

## Features

- Add new tasks
- Edit existing tasks
- Mark tasks as completed or incomplete
- Delete tasks
- Secure input handling
- CSRF protection

## Technologies Used

- [PHP](https://www.php.net/downloads) (Core, OOP)
- [MySQL](https://www.mysql.com/downloads/) (Database)
- [Composer](https://getcomposer.org/download/) (Dependency Management)
- HTML/CSS (Frontend)
- [Apache](https://httpd.apache.org/download.cgi) or [Nginx](https://nginx.org/en/download.html) (Server)

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

4. **Configure database settings in .env:**

   ```env
   DB_HOST=localhost
   DB_USER=your_db_user
   DB_PASS=your_db_password
   DB_NAME=your_db_name
   ```

5. **Run database migrations:**

   ```php
   php -r "require 'app/Database/DatabaseManager.php'; (new App\Database\DatabaseManager(require 'app/Config/Database.php'))->migrate();"
   ```

6. **Start the PHP development server:**

   ```php
   php -S localhost:8000 -t public
   ```

   The application will now be accessible at `http://localhost:8000`

## Configuration

- Update database credentials in `.env`
- Modify `app/Config/Database.php` if needed
- Modify front-end styles in `public/css/styles.css`

## Usage

1. Open `http://localhost:8000` in your browser.
2. Click `Add New Task` to create a task.
3. Use the `Edit` button to modify a task.
4. Mark tasks as complete/incomplete using the provided button.
5. Delete tasks if no longer needed.

## Project Structure

```
├── app/
│   ├── Config/         # Configuration files
│   ├── Controllers/    # Handles application logic
│   ├── Database/       # Database management
│   ├── Helpers/        # Security and utilities
│   ├── Models/         # Database models
│   ├── Routes/         # Application routes
│   └── Views/          # Frontend templates
├── public/             # Public assets (CSS, index.php)
├── .env.example        # Environment variables template
├── composer.json       # Composer dependencies
├── README.md           # Documentation
└── LICENSE             # License information
```

## Routes

| Method | Route          | Description               |
| ------ | -------------- | ------------------------- |
| GET    | `/`            | Display list of tasks     |
| GET    | `/create`      | Show task creation form   |
| POST   | `/tasks`       | Store new task            |
| GET    | `/edit/{id}`   | Show edit form for a task |
| POST   | `/update/{id}` | Update a task             |
| POST   | `/delete/{id}` | Delete a task             |

## Contributing

Contributions are welcome! If you'd like to improve this project:

1. **Fork the repository.**

2. **Create a feature branch:**
   ```bash
   git checkout -b feature/feature-name
   ```
3. **Commit your changes:**
   ```bash
   git commit -m 'Add a meaningful message'
   ```
4. **Push to the branch:**
   ```bash
   git push origin feature/feature-name
   ```
5. **Open a Pull Request.**

## License

This project is licensed under the MIT License. See [LICENSE](./LICENSE) for details.

## Acknowledgments

All contributors who have helped improve this project

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Composer Documenation](https://getcomposer.org/doc/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)

## Support

For support and queries:

- Create an [Issue](https://github.com/mugabiBenjamin/todo-list_php/issues)

⭐ Star this repository if you find it helpful!

[🔝 Back to Top](#to-do-list-application)
