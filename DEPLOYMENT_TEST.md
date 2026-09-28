# Wappiyo — Test & Staging Server Deployment Guide

**Target Environment:** Test / Staging Server  
**Target Git Branch:** `test`  
**Application:** Wappiyo WhatsApp Marketing & SaaS Automation Platform  
**Laravel Version:** 10.x / 11.x compatible (PHP 8.2 / 8.3)  
**Node Version:** Node.js 18.x or 20.x LTS  
**Primary Timezone:** `Asia/Kolkata` (IST, UTC+05:30)

---

## 1. System Requirements & Prerequisites

| Requirement | Supported Versions | Notes |
| :--- | :--- | :--- |
| **Operating System** | Ubuntu 22.04 LTS / Debian 12 / AlmaLinux 9 | Standard Linux production/staging stack |
| **Web Server** | Nginx 1.22+ or Apache 2.4+ | Nginx reverse proxy recommended with gzip/HTTP2 |
| **PHP Engine** | PHP 8.2 or PHP 8.3 | Required extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml`, `zip` |
| **Database** | MySQL 8.0+ or MariaDB 10.6+ | `utf8mb4_unicode_ci` default charset |
| **Node.js & NPM** | Node 18.x or 20.x LTS / NPM 9.x+ | Required for frontend asset building (`vite build`) |
| **Process Manager**| Supervisor 4.x+ | For background queue workers (`SendCampaignJob`, `RetryCampaignJob`) |
| **In-Memory Cache**| Redis 6.x or 7.x (Optional / Recommended) | For scalable sessions, rate limiting, and queue management |

---

## 2. Directory Permissions

Ensure web server user (`www-data` or `nginx`) owns the storage and bootstrap cache directories:

```bash
sudo chown -R www-data:www-data /var/www/wappiyo
sudo chmod -R 775 /var/www/wappiyo/storage
sudo chmod -R 775 /var/www/wappiyo/bootstrap/cache
```

---

## 3. Server Deployment Sequence

Execute the following commands sequentially on the staging/test server:

### Step 1: Clone or Fetch the `test` Branch
```bash
# Initial clone (if fresh installation):
git clone <repository_url> /var/www/wappiyo
cd /var/www/wappiyo
git checkout test

# Or pull latest if already cloned:
cd /var/www/wappiyo
git fetch origin
git checkout test
git pull origin test
```

### Step 2: Environment Configuration
```bash
# If .env does not exist:
cp .env.example .env

# Generate application encryption key (if not already set):
php artisan key:generate
```

> [!IMPORTANT]
> Verify that the staging `.env` contains:
> - `APP_ENV=staging` or `APP_ENV=production`
> - `APP_DEBUG=false`
> - `APP_TIMEZONE=Asia/Kolkata`
> - Dedicated staging database credentials (NEVER connect staging to production DB)
> - Sandbox payment and test WhatsApp credentials

### Step 3: Install Backend Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 4: Install Frontend Dependencies & Compile Assets
```bash
npm ci --prefer-offline || npm install
npm run build
```

### Step 5: Execute Database Migrations
```bash
php artisan migrate --force
```

### Step 6: Create Storage Symlink
```bash
php artisan storage:link
```

### Step 7: Clear & Optimize Caches
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Background Queue Worker Setup (Supervisor)

The Wappiyo Campaign and Recovery engine dispatches asynchronous broadcast jobs (`SendCampaignJob`, `RetryCampaignJob`). Configure Supervisor on the server:

Create configuration file `/etc/supervisor/conf.d/wappiyo-worker.conf`:
```ini
[program:wappiyo-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/wappiyo/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=120
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/wappiyo/storage/logs/worker.log
stopwaitsecs=3600
```

Update and restart Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart wappiyo-worker:*
```

---

## 5. Cron Task Scheduler

Wappiyo relies on Laravel's scheduler to process scheduled campaigns, execute automated workflows, check subscription renewals, and prune old logs.

Add the following cron entry to the `www-data` or root crontab:
```bash
sudo crontab -u www-data -e
```
Add line:
```cron
* * * * * cd /var/www/wappiyo && php artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Environment Variables Reference (.env)

| Key | Staging / Test Recommendation | Purpose |
| :--- | :--- | :--- |
| `APP_NAME` | `Wappiyo (Staging)` | Platform display title |
| `APP_ENV` | `staging` | Environment mode |
| `APP_DEBUG` | `false` | Prevents exposing debug stack traces |
| `APP_URL` | `https://staging.yourdomain.com` | Base URL for routing and callbacks |
| `APP_TIMEZONE` | `Asia/Kolkata` | Application standard timezone |
| `DB_CONNECTION` | `mysql` | MySQL database driver |
| `DB_HOST` | `127.0.0.1` | Database server IP |
| `DB_DATABASE` | `wappiyo_staging` | Separate staging database |
| `QUEUE_CONNECTION` | `database` or `redis` | Async queue driver |
| `CACHE_DRIVER` | `file` or `redis` | Caching driver |
| `SESSION_DRIVER` | `file` or `redis` | Session storage driver |
| `GRAPH_API_VERSION` | `v19.0` | Meta WhatsApp Cloud API version |
| `STRIPE_KEY` | `pk_test_...` | Sandbox payment key |
| `STRIPE_SECRET` | `sk_test_...` | Sandbox secret key |
| `PAYPAL_MODE` | `sandbox` | Sandbox PayPal mode |

---

## 7. Staging Smoke Test Checklist

After deployment, verify the following core paths:

- [ ] **Public Website**: Load homepage (`/`), pricing (`/pricing`), and contact (`/contact`).
- [ ] **Lead Capture**: Submit website contact form → verify lead appears in Admin Panel (`/admin/leads`).
- [ ] **Authentication**: Login as admin and standard user (`/login`), test session persistence.
- [ ] **Timezone Check**: Verify timestamps display in India Standard Time (`Asia/Kolkata`).
- [ ] **Campaign Management**:
  - Visit `/campaigns` and `/campaigns/create`.
  - Verify audience calculation and deduplication.
  - Test campaign schedule with IST time input.
  - Verify campaign export CSV downloads.
- [ ] **PWA & Mobile Navigation**:
  - Verify `/manifest.webmanifest` loads with HTTP 200.
  - Verify `/offline.html` and `/sw.js` are reachable.
- [ ] **Dark Mode**: Toggle theme and check visual contrast.

---

## 8. Rollback Procedure

If an unexpected critical issue occurs during deployment:

```bash
# 1. Revert Git branch to previous stable commit:
git checkout <previous_stable_commit_hash>

# 2. Re-run Composer and build:
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Rollback database migrations (if necessary):
php artisan migrate:rollback --step=1

# 4. Clear and optimize caches:
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

# 5. Restart queue workers:
sudo supervisorctl restart wappiyo-worker:*
```
