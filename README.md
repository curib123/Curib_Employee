<!-- README.md | 2026-09-21 -->

# Curib Employee

A CodeIgniter 3 MVC employee management application with generated-password authentication, secure sessions, MySQL, and Bootstrap 5.

## Main routes

- `/login` — user login
- `/register` — user registration
- `/dashboard` — protected dashboard
- `/employees` — protected employee CRUD

Authenticated application pages use a top navigation only: **Dashboard**, **Employees**, user identity, and **Logout**.

## Authentication flow

Registration copies the employee-profile fields into the `users` table and adds `email` and `password`.

1. User enters first name, last name, birthday, address, contact number, and email.
2. The server validates the fields and confirms the email is unique.
3. The server generates a strong random 14-character password.
4. Only `password_hash(..., PASSWORD_DEFAULT)` is stored in MySQL.
5. The account is signed in and the session ID is regenerated.
6. A one-time Bootstrap modal shows the generated password.
7. **Copy Password & Continue** copies the password and redirects to Dashboard.
8. Refreshing the one-time password route will not reveal the password again.

Login uses `password_verify()`. After five failed attempts in the same session, login is temporarily locked for 60 seconds.

## Features

- CodeIgniter 3.1.13 MVC structure
- User registration and login
- Generated passwords with cryptographically secure random selection
- Password hashing; plaintext passwords are never stored in MySQL
- Protected Dashboard and Employees routes
- Session ID regeneration on authentication
- Logout confirmation modal
- Employee Create/Update through one Upsert modal
- Employee Delete confirmation modal
- Employee Info modal
- Shared success/error/validation alert modal
- One-time generated-password modal
- All modal markup under `application/views/components/modals/`
- Automatic employee age calculation from birthday
- Bootstrap 5 responsive UI
- External modern animation/style sheet; no inline CSS
- Server-side CodeIgniter Form Validation
- Browser/Bootstrap validation
- CodeIgniter Query Builder for database operations
- CSRF protection
- POST-only mutation routes
- Escaped HTML output

## Database

Database name:

```text
Curib
```

### employee

| Column | Type |
| --- | --- |
| Id | INT UNSIGNED AUTO_INCREMENT PRIMARY KEY |
| firstname | VARCHAR(100) |
| lastname | VARCHAR(100) |
| birthday | DATE |
| address | VARCHAR(255) |
| contactno | VARCHAR(20) |

Age is derived from `birthday`; it is not stored.

### users

| Column | Type |
| --- | --- |
| Id | INT UNSIGNED AUTO_INCREMENT PRIMARY KEY |
| firstname | VARCHAR(100) |
| lastname | VARCHAR(100) |
| birthday | DATE |
| address | VARCHAR(255) |
| contactno | VARCHAR(20) |
| email | VARCHAR(190) UNIQUE |
| password | VARCHAR(255), password hash only |

## Fresh installation

Clone the project:

```bash
git clone https://github.com/curib123/Curib_Employee.git
cd Curib_Employee
```

Install CodeIgniter 3:

```bash
composer install
```

Import:

```text
database/curib.sql
```

For XAMPP, a typical project path is:

```text
C:/xampp/htdocs/Curib_Employee
```

Open:

```text
http://localhost/Curib_Employee/
```

The application starts at Login.

## Upgrade an existing employee-only database

If the original `employee` table already exists and contains records, do **not** recreate it.

Import only:

```text
database/add_user_auth.sql
```

This creates the new `users` table without deleting or modifying existing employee rows.

## Default local database settings

```text
Host: localhost
Database: Curib
Username: root
Password: empty
```

Environment variables supported by the project:

```text
DB_HOST
DB_USER
DB_PASS
DB_NAME
APP_BASE_URL
CI_ENCRYPTION_KEY
CI_ENV
COOKIE_SECURE
```

For HTTPS production, set `COOKIE_SECURE` to a truthy value and use a strong `CI_ENCRYPTION_KEY`.

## Project structure

```text
application/
├── config/
│   ├── autoload.php
│   ├── config.php
│   ├── constants.php
│   ├── database.php
│   └── routes.php
├── controllers/
│   ├── Auth.php
│   ├── Dashboard.php
│   └── Employees.php
├── core/
│   └── MY_Controller.php
├── models/
│   ├── Employee_model.php
│   └── User_model.php
└── views/
    ├── auth/
    │   ├── login.php
    │   ├── register.php
    │   └── registration_password.php
    ├── components/
    │   ├── top_nav.php
    │   └── modals/
    │       ├── alert.php
    │       ├── employee.php
    │       ├── generated_password.php
    │       └── logout.php
    ├── dashboard/
    │   └── index.php
    └── employees/
        └── index.html

assets/
├── css/
│   └── app.css
└── js/
    ├── app.js
    ├── auth.js
    └── employees.js

database/
├── add_user_auth.sql
└── curib.sql
```

## Security notes

Database values are passed through CodeIgniter Query Builder rather than concatenated into raw SQL. User and employee fields are validated server-side. IDs are cast to integers. CSRF is enabled globally. Authentication mutations use POST. Session IDs are regenerated after successful authentication, cookies are HTTP-only with SameSite=Lax, and protected controllers redirect unauthenticated users to Login.

For production, use HTTPS, `CI_ENV=production`, a strong encryption key, a non-root database account with only required privileges, and secure deployment-level headers.
