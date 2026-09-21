# Curib Employee

A CodeIgniter 3 + MySQL employee management application with generated-password registration, database-backed sessions, first-login password handling, persistent login throttling, CSRF protection, and a custom responsive UI built with external HTML/CSS/JavaScript only.

## Requirements

- PHP 7.4–8.4 with `mysqli`
- Composer
- MySQL 5.7+ or MySQL 8+
- Apache with `mod_rewrite`, or PHP's built-in development server

## Fresh clone

```bash
git clone https://github.com/curib123/Curib_Employee.git
cd Curib_Employee
composer install
```

Import `database/curib.sql` into MySQL. Local defaults are database `Curib`, user `root`, empty password. You can override them with `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME`.

For a quick local server without Apache:

```bash
php -S 127.0.0.1:8000 router.php
```

Then open `http://127.0.0.1:8000`.

## Authentication flow

1. Register using first name, last name, birthday, address, contact number, and email.
2. The server validates every field and checks email uniqueness.
3. A strong 16-character password is generated with `random_int()` and only its `password_hash()` result is stored.
4. A one-time modal shows the plaintext generated password so it can be copied.
5. Login with the registered email and generated password.
6. On the first successful login, choose **Change password** or **Skip**.
7. Either choice clears `must_change_password`; the prompt does not appear on later logins.
8. Logout destroys the authenticated database session and clears the session cookie.

## Security

- Global CodeIgniter CSRF protection.
- Database-backed `ci_sessions` sessions.
- Session ID regeneration after login and password change.
- Passwords hashed with PHP `PASSWORD_DEFAULT`; plaintext passwords are never stored.
- Persistent per-email-hash + IP login throttling.
- POST-only login mutations, password actions, logout, and employee CRUD mutations.
- Query Builder for database writes and lookups.
- Escaped user-facing output.
- HTTP-only, SameSite=Lax cookies and optional secure cookies.
- Content Security Policy, clickjacking protection, MIME sniffing protection, referrer policy, and permissions policy headers.
- No Bootstrap, no external CDN dependency, no inline CSS, and no inline JavaScript.

For production set `CI_ENV=production`, a strong `CI_ENCRYPTION_KEY`, `COOKIE_SECURE=true`, HTTPS, and a restricted database account.

## Database

Fresh installs use `database/curib.sql`, which creates:

- `employee`
- `users`
- `ci_sessions`
- `login_attempts`

Existing employee-only databases can use `database/add_user_auth.sql`. Existing auth installs that only need the first-login flag can use `database/add_first_login_password_prompt.sql`. Existing installs missing only the session table can use `database/add_database_sessions.sql`.

## Verification

After `composer install`, run:

```bash
composer verify
```

This checks required files, PHP syntax, JavaScript syntax when Node.js is available, CSRF/session/security configuration, password primitives, database schema requirements, and verifies that application views contain no Bootstrap/CDN/internal-style/inline-style/inline-script dependencies.

GitHub Actions also runs a MySQL-backed browser-level smoke flow for registration, generated passwords, login, first-login skip/change behavior, and logout.
