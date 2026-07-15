# Complaint Management System (PHP + XAMPP)

Plain PHP web application — **no Laravel**. Works with XAMPP (Apache + MySQL + PHP 8+).

## Features

- User **Sign Up** and **Login** with secure authentication (sessions + `password_hash`)
- Users can submit complaints, view status, and read admin responses
- **Admin** dashboard: manage all complaints, update status, add notes, view users
- MySQL database with `users` and `complaints` tables

## Setup (XAMPP)

1. Copy the `complaint-management` folder to:
   ```
   C:\xampp\htdocs\complaint-management
   ```

2. Start **Apache** and **MySQL** from XAMPP Control Panel.

3. Open in browser:
   ```
   http://localhost/complaint-management/install.php
   ```
   Click **Run Installation** to create database and admin user.

4. **Delete** `install.php` after setup (recommended for security).

5. Open the app:
   ```
   http://localhost/complaint-management/
   ```

## Default Admin Login

| Field    | Value              |
|----------|--------------------|
| Email    | admin@cms.local    |
| Password | admin123           |

## New User

Use **Sign Up** on the login page to register. After login you can submit complaints.

## Configuration

Edit `config/database.php` if your MySQL username/password is different:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'complaint_db');
```

If the folder name in `htdocs` is not `complaint-management`, update `BASE_URL` in `config/app.php`:

```php
define('BASE_URL', '/your-folder-name');
```

## Manual SQL (optional)

You can also import `sql/database.sql` in phpMyAdmin, then run `install.php` once for the admin account.

## Project Structure

```
complaint-management/
├── admin/          Admin pages
├── user/           User pages
├── assets/css/     Styles
├── config/         Database & app settings
├── includes/       Auth, header, footer
├── sql/            Database script
├── login.php
├── signup.php
├── logout.php
└── index.php
```

## Security Notes

- Passwords are hashed with bcrypt
- SQL uses prepared statements
- Session-based login
- Remove `install.php` after first setup
