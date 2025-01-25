# PHP Todo List Application

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/downloads)

[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com)

A robust and user-friendly Todo List application built with **PHP** and **MySQL**, featuring a responsive interface and secure task management capabilities.

## Table of Contents
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Security Features](#-security-features)
- [Contributing](#contributing)
- [License](#license)
- [Acknowledgments](#acknowledgments)
- [Contact](#contact)

## 🚀 Features

- **Task Management**
  - Create and add new tasks
  - Mark tasks as complete/incomplete
  - Delete unnecessary tasks
  - Real-time status updates
  
- **Security**
  - CSRF protection
  - Input sanitization
  - SQL injection prevention
  - XSS attack prevention

- **User Experience**
  - Responsive design
  - Intuitive interface
  - Animated notifications
  - Real-time updates

## 📋 Prerequisites

Before you begin, ensure you have the following installed on your system:

- [PHP](https://www.php.net/downloads) (version 7.4 or higher)
- [MySQL](https://www.mysql.com/downloads/) (version 5.7 or higher)
- [Apache](https://httpd.apache.org/download.cgi) or [Nginx](https://nginx.org/en/download.html) web server
- [Composer](https://getcomposer.org/download/) (optional, for dependency management)
- Web browser (Chrome, Firefox, Safari, or Edge)

## 🛠️ Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/mugabiBenjamin/todo-list_php.git
   cd todo-list_php
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment variables**
   ```bash
   cp config.env.example .env
   ```
   Edit `.env` with your database credentials:
   ```env
   DB_HOST=localhost
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   DB_NAME=your_database_name
   ```

4. **Set Up the Database**
   - Create a new database (e.g., `todo_list`).
   - Import the provided SQL file into the database:
     ```bash
     mysql -u [username] -p [database_name] < database/todo.sql
     ```

5. **Configure web server**
   - For Apache: Ensure mod_rewrite is enabled
   - For Nginx: Configure URL rewriting

6. **Run the Application**
   - Start your web server.
   - Place the project folder in the server's root directory (e.g., `htdocs` for XAMPP or `www` for WAMP).
   - Access the application via `http://localhost/todo-list_php` in your browser.

## 💻 Usage

1. Access the application through your web browser:
   ```
   http://localhost/todo-list_php
   ```

2. Add tasks using the input field at the top
3. Click the checkbox to mark tasks as complete
4. Use the trash icon to delete tasks
5. Tasks are automatically saved to the database

## 🔧 Project Structure

```
└── todo-list_php/
   ├── README.md                       # Project documentation
   ├── LICENSE                         # License information
   ├── composer.json                   # Composer dependencies and scripts
   ├── config.env.example              # Example environment configuration file
   ├── connection.php                  # Database connection setup
   ├── index.php                       # Main entry point of the application
   ├── css/ 
   │   └── todo.css                    # Stylesheet for the application
   ├── database/  
   │   └── todo.sql                    # SQL file to set up the database schema
   └── tasks/  
      ├── add_task.php                 # Script to add a new task
      ├── delete_task.php              # Script to delete a task
      ├── fetch_task.php               # Script to fetch tasks from the database
      ├── task_validation.php          # Script to validate task input
      └── toggle_completion.php        # Script to toggle task completion status
```

## 🔒 Security Features

- CSRF token validation for forms
- Input sanitization and validation
- Prepared SQL statements
- XSS prevention through HTML escaping

## 🤝 Contributing

Contributions are welcome! If you'd like to improve this project:

1. Fork the repository.
2. **Clone the repository:**
   ```bash
   git clone https://github.com/mugabiBenjamin/todo-list_php.git
   ```
3. **Navigate to the project directory:**
   ```bash
   cd todo-list_php/
   ```
4. **Create a feature branch:**
   ```bash
   git checkout -b feature-name
   ```
5. **Commit your changes:**
   ```bash
   git commit -m 'Add a meaningful message'
   ```
6. **Push to the branch: **
   ```bash
   git push origin feature-name
   ```
7. Open a Pull Request.

## 📝 License

- This project is licensed under the MIT License - see the [LICENSE](./LICENSE) file for details.

## 👥 Authors

- **Mugabi Benjamin**
  - GitHub: [@mugabiBenjamin](https://github.com/mugabiBenjamin)
  - LinkedIn: [Mugabi Benjamin](https://www.linkedin.com/in/mugabi-benjamin-156603224/)
  - Email: mugabiben6@gmail.com

## 🙏 Acknowledgments

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- All contributors who have helped improve this project

## 📞 Support

For support and queries:
- Create an [Issue](https://github.com/mugabiBenjamin/todo-list_php/issues)
- Email: mugabiben6@gmail.com
- LinkedIn: [Mugabi Benjamin](https://www.linkedin.com/in/mugabi-benjamin-156603224/)

⭐ Star this repository if you find it helpful!
