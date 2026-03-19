# City Pulses — Laravel 10 Installation Guide

This is a Laravel 10 application converted from Ruby on Rails. Follow these steps to deploy on shared hosting (cPanel / DirectAdmin / Plesk) or a VPS.

---

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.1 or 8.2 |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Composer | 2.x |
| mod_rewrite | Enabled |

PHP extensions required: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`

---

## 1. Upload Files

### Option A — Document root is `public/` (Recommended)

Set your hosting document root (or subdomain root) directly to the `public/` subdirectory of this project.

Example cPanel path: `/home/username/city_pulses_laravel/public`

No root `.htaccess` changes needed — `public/.htaccess` handles all routing.

### Option B — Document root is project root

If you cannot change the document root (e.g., `public_html/`), upload all project files there. The root `.htaccess` will forward requests to `public/` automatically.

---

## 2. Install PHP Dependencies

### On the server (if Composer is available):

```bash
cd /path/to/city_pulses_laravel
composer install --no-dev --optimize-autoloader
```

### Locally then upload:

```bash
composer install --no-dev --optimize-autoloader
```

Then upload the entire `vendor/` directory to the server.

---

## 3. Configure Environment

Copy the example env file:

```bash
cp .env.example .env
```

Edit `.env` with your values:

```ini
APP_NAME="City Pulses"
APP_ENV=production
APP_KEY=                          # Generated in step 4
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Mail (for password reset emails)
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourmailprovider.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="City Pulses"

# Stripe (for subscription payments)
STRIPE_KEY=pk_live_xxxx
STRIPE_SECRET=sk_live_xxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxx

# External API Keys
OPENWEATHER_API_KEY=your_openweather_key
NEWS_API_KEY=your_newsapi_key
AGMARKNET_API_KEY=your_agmarknet_key

# Admin seeder (used by php artisan db:seed)
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD=YourStrongPassword123
```

---

## 4. Generate Application Key

```bash
php artisan key:generate
```

This sets `APP_KEY` in `.env`. **Required before the app will work.**

---

## 5. Run Database Migrations

Create your MySQL database in cPanel first, then:

```bash
php artisan migrate
```

To also seed default cities and a superadmin account:

```bash
php artisan migrate --seed
```

Or separately:

```bash
php artisan db:seed
```

---

## 6. Set File Permissions

Laravel needs write access to `storage/` and `bootstrap/cache/`:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

On shared hosting, `755` usually works:

```bash
chmod -R 755 storage bootstrap/cache
```

---

## 7. Create Storage Symlink (for file uploads)

```bash
php artisan storage:link
```

This creates `public/storage` → `storage/app/public`.

---

## 8. Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

To clear caches:

```bash
php artisan optimize:clear
```

---

## 9. Shared Hosting php.ini Tips

If you have access to `php.ini` or `.htaccess` PHP settings, ensure:

```ini
allow_url_fopen = On        ; Required for external API calls (weather, news, markets)
max_execution_time = 60
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
```

For `.htaccess` equivalent:

```apache
php_value allow_url_fopen On
php_value memory_limit 256M
php_value max_execution_time 60
```

---

## 10. Cron Jobs (Optional — for scheduled tasks)

If your hosting supports cron, add:

```
* * * * * cd /path/to/city_pulses_laravel && php artisan schedule:run >> /dev/null 2>&1
```

This runs the Laravel task scheduler every minute (used for market data imports, etc.).

---

## 11. Hostinger Business Plan — Git Deployment (Recommended)

If you want automatic deploys from GitHub on Hostinger:

1. Push your latest code to GitHub

```bash
git add .
git commit -m "prepare hostinger deploy"
git push origin main
```

2. In Hostinger hPanel:
	- Go to **Advanced → GIT**
	- Add repository URL: `https://github.com/your-username/city_pulses_laravel.git`
	- Branch: `main`
	- Deploy path: e.g. `/home/USERNAME/city_pulses_laravel`

3. Set document root to Laravel `public/`
	- **Websites → Manage → (your domain) → Website details / Document root**
	- Set it to: `/home/USERNAME/city_pulses_laravel/public`

4. SSH into Hostinger and run first deploy

```bash
cd /home/USERNAME/city_pulses_laravel
bash scripts/deploy-hostinger.sh
```

5. Configure environment in server `.env`
	- `APP_ENV=production`
	- `APP_DEBUG=false`
	- `APP_URL=https://yourdomain.com`
	- DB credentials
	- API keys

6. Optional auto migration on deploy
	- Set env before running script:

```bash
export RUN_MIGRATIONS=1
bash scripts/deploy-hostinger.sh
```

7. For each future deploy
	- Push code to GitHub
	- Click **Deploy** in Hostinger Git panel (or pull via SSH) and run:

```bash
bash scripts/deploy-hostinger.sh
```

---

## Directory Structure Reference

```
city_pulses_laravel/
├── app/                  # Controllers, Models, Middleware, Helpers
├── bootstrap/            # App bootstrap & cache
├── config/               # All config files
├── database/
│   ├── migrations/       # Single migration file (all tables)
│   └── seeders/          # DatabaseSeeder (cities + superadmin)
├── lang/en/              # English validation/auth messages
├── public/               # Document root — index.php + assets
│   ├── css/application.css
│   ├── images/icons/
│   └── .htaccess
├── resources/views/      # All Blade templates
├── routes/
│   ├── web.php           # All web routes
│   ├── api.php           # API routes
│   └── console.php       # Artisan console commands
├── storage/              # Logs, cache, sessions (writable)
├── vendor/               # Composer dependencies
├── .env                  # Your environment config (never commit)
├── .htaccess             # Root redirect to public/ (shared hosting)
├── artisan               # CLI entry point
└── composer.json
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Blank white page | Set `APP_DEBUG=true` temporarily, check `storage/logs/laravel.log` |
| 500 error | Check file permissions on `storage/` and `bootstrap/cache/` |
| Routes not working | Ensure `mod_rewrite` is enabled; check `public/.htaccess` |
| Database connection error | Verify `.env` DB credentials; ensure DB exists |
| CSS not loading | Check `APP_URL` matches your domain exactly (no trailing slash) |
| Login token mismatch | Clear browser cookies; ensure `SESSION_DRIVER=file` in `.env` |
| API data not showing | Verify API keys in `.env`; ensure `allow_url_fopen=On` |
| Key not set error | Run `php artisan key:generate` |

---

## User Roles

| Role | Access |
|------|--------|
| `normal` | Browse, apply for jobs, post listings |
| `shopowner` | My Shop dashboard, post jobs, shop listings |
| `service_provider` | My Service dashboard, business card |
| `shopworker` | Access shop features as worker |
| `superadmin` | Full admin panel at `/admin` |

The first superadmin is created via `php artisan db:seed` using `ADMIN_EMAIL` and `ADMIN_PASSWORD` from `.env`.

---

## Support

For questions about this project, refer to the source Rails application at `/home/dhananjay/city_pulses/`.
