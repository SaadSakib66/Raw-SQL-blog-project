# Raw SQL Blog Project

A simple blog application built with raw PHP and MySQL.

## Features
- User registration and login
- User dashboard to manage blogs
- Create, Edit, Publish/Unpublish blogs
- Image upload for blogs
- Public read-only view for all published blogs
- Commenting system on published blogs

## Prerequisites
- XAMPP (or any local server with PHP and MySQL)

## Setup Instructions
1. Download or clone this repository to your `xampp/htdocs` folder.
2. Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
3. Open `http://localhost/phpmyadmin` in your browser.
4. Import the `database.sql` file provided in the repository. This will automatically create the `raw_sql_blog` database and necessary tables (`users`, `blogs`, `comments`).
5. Access the application in your browser at `http://localhost/Raw-SQL-blog-project/` (or whatever you named the folder).

## Architecture
- **PHP Data Objects (PDO / MySQLi)**: Used `mysqli` for raw database connection.
- **Authentication**: Password hashing using `password_hash` and verification with `password_verify`.
- **Session Management**: Native PHP session handled in `config.php`.

## Author
Saad Sakib
