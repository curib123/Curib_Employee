<!-- README.md | 2026-09-21 -->

# Curib Employee CRUD

A CodeIgniter 3 MVC employee CRUD application using MySQL and Bootstrap 5.

## Features

- Create and update employees through one reusable **Upsert** modal.
- Delete employees through a confirmation modal.
- View complete employee details through an **Info** modal.
- Show success, error, and validation messages through an **Alert** modal.
- Automatically calculate age from the employee birthday.
- Bootstrap 5 UI with no inline CSS.
- Server-side CodeIgniter Form Validation plus browser/Bootstrap validation.
- CodeIgniter Query Builder for database reads and writes.
- CSRF protection for all forms.
- Escaped HTML output to reduce XSS risk.
- POST-only create, update, and delete actions.

## Employee fields

The MySQL database is named `Curib` and the table is `employee`.

| Column | Type |
| --- | --- |
| Id | INT UNSIGNED AUTO_INCREMENT PRIMARY KEY |
| firstname | VARCHAR(100) |
| lastname | VARCHAR(100) |
| birthday | DATE |
| address | VARCHAR(255) |
| contactno | VARCHAR(20) |

Age is not stored in the table because it is derived from `birthday` whenever the employee list is loaded.

## Requirements

- PHP 7.4 or newer
- Composer
- MySQL or MariaDB
- Apache with `mod_rewrite` enabled, or an equivalent web-server rewrite
- PHP `mysqli` extension

## Installation

1. Clone the repository into your web root.

```bash
git clone https://github.com/curib123/Curib_Employee.git
cd Curib_Employee
```

2. Install CodeIgniter 3.

```bash
composer install
```

3. Import the database script.

```text
database/curib.sql
```

You can import it with phpMyAdmin or the MySQL command line.

4. The default local database configuration is:

```text
Host: localhost
Database: Curib
Username: root
Password: empty
```

You can override these values with server environment variables:

```text
DB_HOST
DB_USER
DB_PASS
DB_NAME
APP_BASE_URL
CI_ENCRYPTION_KEY
CI_ENV
```

5. For a typical XAMPP installation, place the project in:

```text
C:/xampp/htdocs/Curib_Employee
```

Then browse to:

```text
http://localhost/Curib_Employee/
```

The default `APP_BASE_URL` also points to this URL. Change it with the environment variable or edit `application/config/config.php` if your project folder is different.

## MVC structure

```text
application/
├── controllers/
│   └── Employees.php
├── models/
│   └── Employee_model.php
├── views/
│   └── employees/
│       ├── index.html
│       └── modal.php
└── config/
    ├── autoload.php
    ├── config.php
    ├── database.php
    └── routes.php

assets/
└── js/
    └── employees.js

database/
└── curib.sql
```

## Security notes

Employee values are never concatenated into SQL statements. The model uses CodeIgniter Query Builder, which escapes/binds values for the generated queries. IDs are cast to integers, mutation routes accept POST only, CSRF protection is enabled, input is validated on the server, and values rendered into HTML are escaped.

For production, set `CI_ENV=production`, use a strong `CI_ENCRYPTION_KEY`, configure secure database credentials, enable HTTPS, and set the cookie security options appropriately.
