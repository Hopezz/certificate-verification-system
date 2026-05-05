# Certificate Verification System

## Folder Structure

```text
.
├── admin/
│   ├── add_graduate.php
│   ├── dashboard.php
│   ├── edit_graduate.php
│   ├── login.php
│   ├── logout.php
│   ├── manage_admins.php
│   └── manage_graduates.php
├── assets/
│   └── css/
│       └── style.css
├── database/
│   └── schema.sql
├── docs/
│   └── CPANEL_UPLOAD.md
├── includes/
│   ├── auth.php
│   ├── config.php
│   ├── db.php
│   ├── footer.php
│   ├── functions.php
│   ├── graduate_form.php
│   └── header.php
├── public/
│   ├── index.php
│   └── result.php
└── index.php
```

## cPanel Upload Instructions

1. Create a MySQL database in cPanel, then create a database user and assign it all privileges for that database.
2. Open phpMyAdmin, select the new database, and import `database/schema.sql`.
3. Upload all project folders and files into `public_html`.
4. Edit `includes/config.php` with your cPanel database name, username, and password.
5. Visit `https://yourdomain.com/index.php` for the public verification page.
6. Visit `https://yourdomain.com/admin/login.php` for the admin panel.
7. Log in with the default superadmin account:
   - Email: `admin@iaec-university.tg`
   - Password: `ChangeMe123!`
8. Immediately create a new superadmin account, log in with it, and delete or stop using the default account.

## Notes

- Passwords are hashed with PHP `password_hash`.
- Database queries use PDO prepared statements.
- Admin pages use session authentication and CSRF tokens.
- For a subfolder installation, set `BASE_URL` in `includes/config.php`, for example `/verification`.
- For an existing installation, run `database/add_ref_number.sql` in phpMyAdmin to add reference-number verification support.
- For an existing installation, run `database/settings.sql` in phpMyAdmin to add dynamic school branding.
