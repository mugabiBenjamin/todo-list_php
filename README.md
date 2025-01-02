# Todo List in PHP

This project is a **Todo List Application** built with **PHP**, aimed at helping users efficiently manage and organize tasks. It provides core functionalities such as adding, editing, deleting, and marking tasks as complete, all in a user-friendly web interface.

## Table of Contents
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-Structure)
- [Contibuting](#contibuting)
- [Acknowledgments](#Acknowledgments)
- [Conatct](#conatct)

## Features

- **Add Tasks**: Add new tasks to your todo list.
- **Edit Tasks**: Modify task details with ease.
- **Delete Tasks**: Remove completed or unnecessary tasks.
- **Mark Tasks as Complete**: Keep track of finished tasks.
- **Persistent Storage**: Tasks are stored in a database for durability.
- **Responsive Interface**: A clean, intuitive design for enhanced user experience.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- [PHP](https://www.php.net/downloads) (version 7.4 or higher)
- [MySQL](https://www.mysql.com/downloads/)
- [Apache](https://httpd.apache.org/download.cgi) or any web server
- Composer (optional, for dependency management)

## Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/mugabiBenjamin/todo-list_php.git
   cd todo-list_php
   ```

2. **Set Up the Database**

   - Create a new database (e.g., `todo_list`).
   - Import the provided SQL file into the database:
     ```bash
     mysql -u [username] -p [database_name] < database/todo.sql
     ```


3. **Configure the Database Connection**

   Update the database credentials in `config.php`:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'todo_list');
   ```

4. **Run the Application**

   - Start your web server.
   - Place the project folder in the server's root directory (e.g., `htdocs` for XAMPP or `www` for WAMP).
   - Access the application via `http://localhost/todo-list_php` in your browser.

## Usage

1. Open the application in your web browser.
2. Add tasks using the input form.
3. Mark tasks as completed or delete them as needed.
4. Edit task details directly in the interface.

## Project Structure

```
.
├── css/             # Static files (stylesheets for frontend)
├── database/
│   └── todo.sql     # SQL script to set up the database schema
├── config.php       # Database configuration file
├── tasks/           # Task-related functionality (CRUD operations)
├── connection.php   # Establishes a PDO connection to the database
├── .env             # Environment variables (e.g., database credentials; not included in version control)
├── composer.json    # Composer configuration file
├── index.php        # Main entry point of the application
├── README.md        # Project documentation
```

## Contributing

Contributions are welcome! If you'd like to improve this project:

1. Fork the repository.
2. Create a feature branch (`git checkout -b feature-name`).
3. Commit your changes (`git commit -m 'Add a feature'`).
4. Push to the branch (`git push origin feature-name`).
5. Open a Pull Request.

## Acknowledgments

- Special thanks to contributors and open-source libraries used in this project.

## Contact

For inquiries or support, contact:
- [Mugabi Benjamin](https://github.com/mugabiBenjamin)

---
Visit the [GitHub Repository](https://github.com/mugabiBenjamin/todo-list_php) for more details.
