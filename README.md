# Complaint Management System

A lightweight PHP-based web application for managing customer/user complaints with separate user and admin experiences.

## Repository Layout

- `complaint-management/` - Main PHP application source code
- `complaint-management/sql/` - Database schema
- `complaint-management/admin/` - Admin dashboard and complaint management pages
- `complaint-management/user/` - User dashboard and complaint workflows

## Features

- User signup and login
- Complaint submission and tracking
- Admin complaint triage and status updates
- Role-based access handling
- MySQL-backed persistence

## Quick Start (XAMPP)

1. Copy `complaint-management` into `htdocs`.
2. Start Apache and MySQL.
3. Open `http://localhost/complaint-management/install.php`.
4. Run installation.
5. Delete `install.php` after setup.

## Default Admin Account

- Email: `admin@cms.local`
- Password: `admin123`

> Change the default admin password immediately after first login.

## Configuration

Update database settings in:

- `complaint-management/config/database.php`

If your local folder name differs from `complaint-management`, update:

- `complaint-management/config/app.php` (`BASE_URL`)

## Development Notes

- This project uses plain PHP (no framework).
- Keep business logic in reusable include/helpers where possible.
- Avoid committing generated files or secrets.

## Security Recommendations

- Remove `install.php` after installation.
- Use strong credentials and restricted database permissions.
- Run the app behind HTTPS in production.
