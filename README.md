<!-- README.md | 2026-09-21 -->

# Curib Employee

A CodeIgniter 3 MVC employee management application with generated-password authentication, secure sessions, MySQL, and Bootstrap 5.

## Authentication flow

The registration and first-login flow is:

1. User registers with first name, last name, birthday, address, contact number, and email.
2. The server validates the data and checks that the email is unique.
3. The server generates a strong 16-character password.
4. Only the password hash is stored in MySQL.
5. A one-time Bootstrap modal shows the generated password.
6. **Copy Password & Go to Login** copies it and opens Login.
7. The email is prefilled on Login; the user signs in using the generated password.
8. On the first successful login only, Dashboard opens a **Change Password** modal.
9. The user can either change the password or skip.
10. After either Change or Skip, the modal does not appear on future logins.

If the user never chooses Change or Skip, the database flag remains pending.

## First-login flag

The users table contains:

~~~text
must_change_password TINYINT(1) NOT NULL DEFAULT 1
~~~

New users begin with 1. Both successful password change and Skip set it to 0.

## Security

- Generated passwords use random_int().
- Passwords are hashed with password_hash(..., PASSWORD_DEFAULT).
- Login verifies credentials with password_verify().
- Plaintext passwords are never stored in MySQL.
- The generated-password page uses no-store/no-cache response headers.
- Session ID is regenerated on successful authentication.
- Login uses persistent database-backed throttling by hashed email identifier and IP after repeated failures.
- CSRF protection is globally enabled.
- Logout, password change, password skip, and Employee CRUD mutations use POST.
- CodeIgniter Query Builder handles database values.
- User-facing output is escaped.
- Cookies are HTTP-only and SameSite=Lax.

## Main routes

~~~text
/login
/register
/dashboard
/employees
/password/change
/password/skip
/logout
~~~

Authenticated pages use a top navigation only: **Dashboard**, **Employees**, user identity, and **Logout**.

## Database

Database: Curib

### employee

~~~text
Id
firstname
lastname
birthday
address
contactno
~~~

Age is derived from birthday; it is not stored.

### users

~~~text
Id
firstname
lastname
birthday
address
contactno
email
password
must_change_password
~~~

Email is unique. Password contains only the password hash.

## Fresh installation

~~~bash
git clone https://github.com/curib123/Curib_Employee.git
cd Curib_Employee
composer install
~~~

Import:

~~~text
database/curib.sql
~~~

## Existing database migration

For an employee-only installation, import:

~~~text
database/add_user_auth.sql
~~~

For an installation where the users table already exists but does not yet have the first-login flag, import:

~~~text
database/add_first_login_password_prompt.sql
~~~

That migration adds must_change_password without deleting existing users.

## Modal components

All modal markup is under:

~~~text
application/views/components/modals/
├── alert.php
├── change_password.php
├── employee.php
├── generated_password.php
└── logout.php
~~~

The first-login password modal cannot be dismissed by clicking the backdrop or pressing Escape; the user must choose either **Change Password** or **Skip**.

## UI

- Bootstrap 5.3.3 from the pinned jsDelivr CDN
- Dashboard and Employees top navigation
- Responsive layouts
- External assets/css/app.css
- No inline CSS
- Lightweight page/modal/button animations
- Reduced-motion support

## Environment variables

~~~text
DB_HOST
DB_USER
DB_PASS
DB_NAME
APP_BASE_URL
CI_ENCRYPTION_KEY
CI_ENV
COOKIE_SECURE
~~~

For production, use HTTPS, CI_ENV=production, a strong encryption key, COOKIE_SECURE=true, and a database account with only the required permissions.


## Database sessions

CodeIgniter sessions are stored in the MySQL table:

~~~text
ci_sessions
~~~

Configuration:

~~~text
sess_driver = database
sess_save_path = ci_sessions
sess_expiration = 7200
sess_regenerate_destroy = TRUE
~~~

The authenticated session contains values such as the logged-in user ID, name, email, login state, and first-login password-prompt flag.

The generated plaintext registration password is **not** stored in `ci_sessions`. It is rendered directly in the registration response and then discarded from server-side application state.

On Logout, `$this->session->sess_destroy()` removes the current authenticated session row and clears the session cookie. After the redirect to Login, CodeIgniter may create a new anonymous session row for the new Login request; that row is a different session and does not contain the authenticated user state.

For an existing installation that does not yet have the session/throttling tables, import:

~~~text
database/add_database_sessions.sql
~~~


## Bootstrap and custom styling

The interface uses Bootstrap 5.3.3 for its responsive grid, navigation, forms, tables, buttons, validation states, and modal behavior.

Bootstrap is loaded from the pinned jsDelivr 5.3.3 URLs. The Apache Content-Security-Policy explicitly allows only that CDN in addition to same-origin assets.

All project-specific visual design and animation stays in:

~~~text
assets/css/app.css
~~~

There is no inline or internal CSS. JavaScript behavior remains in external files under assets/js/.
