# IAEC University Togo Certificate Verification System

A clean PHP/MySQL certificate verification system with a public verification page and a session-protected admin panel.

## Features

- Public matric/reference number verification
- Verified and not verified result screens
- Admin login with PHP sessions
- Add, view, search, edit, and delete graduate records
- Superadmin admin-account management
- PDO prepared statements
- `password_hash` / `password_verify`
- CSRF protection on admin forms
- Responsive university-style UI

## Quick Setup

1. Import `database/schema.sql` into MySQL.
2. Update database credentials in `includes/config.php`.
3. Place the project in your web root, such as cPanel `public_html`.
4. Open `/index.php` for verification or `/admin/login.php` for administration.

Default superadmin:

- Email: `admin@iaec-university.tg`
- Password: `ChangeMe123!`

Change the default password immediately after first login.

Full cPanel instructions are in `docs/CPANEL_UPLOAD.md`.

