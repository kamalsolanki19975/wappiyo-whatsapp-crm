# Wappiyo — Production Deployment Checklist & Runbook

**System:** Wappiyo WhatsApp Marketing, SaaS Automation & Meta Calling Platform  
**Target Release:** Production Release 1.0.0  
**Target OS / Server:** Ubuntu 22.04 LTS / Debian 12 / RHEL 9 (or macOS / Linux hosting environment)  
**Target Stack:** PHP 8.2+ (PHP 8.3 recommended), MySQL 8.0+, Redis 6.2+, Nginx, Supervisor  
**Classification:** Operational Runbook  

---

## 1. Pre-Deployment Phase

### 1.1 Infrastructure & Environment Readiness Verification
Prior to executing deployment, verify the server environment satisfies all operational requirements:

- [ ] **PHP Runtime:** PHP 8.2 or 8.3 installed with extensions:
  - `php-fpm`, `php-mysql`, `php-redis` (or `igbinary` + `redis`), `php-mbstring`, `php-xml`, `php-curl`, `php-gd`, `php-zip`, `php-intl`.
  - Memory limit configured to $\ge 512\text{M}$ (`memory_limit = 512M`).
  - Max execution time configured to $\ge 300\text{s}$ for long-running imports/reports.
- [ ] **Database Server:** MySQL 8.0+ running with utf8mb4 collation:
  - Character set: `utf8mb4`, collation: `utf8mb4_unicode_ci`.
  - Dedicated database user with appropriate table, trigger, and index privileges.
- [ ] **Redis Server:** Redis 6.2+ running on `127.0.0.1:6379` (or secure private VPC endpoint):
  - Password protection configured (`requirepass` in `redis.conf`).
  - Persistent disk snapshots (`appendonly yes` or RDB enabled).
- [ ] **Web Server (Nginx):**
  - Public static HTTPS enabled with valid SSL/TLS certificate (Let's Encrypt / DigiCert).
  - FastCGI buffer sizes configured to prevent HTTP 502 on large Inertia responses:
    ```nginx
    fastcgi_buffers 16 16k;
    fastcgi_buffer_size 32k;
    ```
  - Webhook URL `/webhook/whatsapp/*` exposed without CSRF middleware interference.
- [ ] **Process Manager (Supervisor):**
  - Supervisor installed and active for daemonized Laravel queue workers.
- [ ] **Crontab:**
  - System cron entry verified for Laravel's task scheduler (`php artisan schedule:run`).

---

### 1.2 Configuration & Secret Audit (`.env`)
Verify that production environment variables are properly defined and secrets are protected:

- [ ] `APP_NAME="Wappiyo"`
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` (CRITICAL: Must never be `true` in production)
- [ ] `APP_URL=https://app.wappiyo.com` (Must match public canonical domain)
- [ ] `DB_CONNECTION=mysql`
- [ ] `DB_HOST=127.0.0.1` (or RDS / private database cluster IP)
- [ ] `DB_PORT=3306` (or custom port)
- [ ] `DB_DATABASE=wappiyo_production`
- [ ] `CACHE_DRIVER=redis`
- [ ] `SESSION_DRIVER=redis`
- [ ] `QUEUE_CONNECTION=redis`
- [ ] `REDIS_HOST=127.0.0.1`
- [ ] `REDIS_PASSWORD=YourSecureRedisPassword`
- [ ] `MAIL_MAILER=smtp`
- [ ] `MAIL_HOST=email-smtp.us-east-1.amazonaws.com` (or Sendgrid/Mailgun)
- [ ] `MAIL_PORT=587`
- [ ] `MAIL_ENCRYPTION=tls`
- [ ] `MAIL_FROM_ADDRESS=noreply@wappiyo.com`
- [ ] `STRIPE_KEY` & `STRIPE_SECRET` populated with live Stripe API keys
- [ ] `STRIPE_WEBHOOK_SECRET` populated with live webhook endpoint signing secret
- [ ] `RAZORPAY_KEY_ID` & `RAZORPAY_KEY_SECRET` populated with live Razorpay keys
- [ ] `RAZORPAY_WEBHOOK_SECRET` populated with live Razorpay webhook secret

---

### 1.3 Pre-Deployment Safety Snapshot
Before making any file modifications or database schema updates:

```bash
# 1. Navigate to application root
cd /var/www/wappiyo

# 2. Execute automated database backup with SHA-256 integrity check
php artisan wappiyo:backup --retention=30

# 3. Verify backup file was created and is non-empty
ls -lh storage/app/backups/
```

---

## 2. Deployment Execution Phase

### 2.1 Enable Maintenance Mode
Notify users and hold incoming web requests safely with an informative status page:

```bash
# Set maintenance mode with secret bypass cookie for deploy team
php artisan down --secret="WappiyoDeployPass2026" --render="errors::503" --retry=60
```

### 2.2 Pull Latest Certified Code
```bash
# Ensure working branch is main / production
git checkout main
git pull origin main
```

### 2.3 Install Production Dependencies
```bash
# Install PHP dependencies with optimized class autoloader (zero dev packages)
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Install frontend dependencies and build production bundles
npm ci --prefer-offline
npm run build
```

### 2.4 Apply Database Schema Migrations
```bash
# Run migrations with force flag (required in production)
php artisan migrate --force
```

### 2.5 Clear and Warm Production Caches
```bash
# Flush any stale runtime caches
php artisan optimize:clear

# Recompile optimized production caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2.6 Restart Background Supervisor Workers
Ensure all long-running queue workers reload the updated codebase into memory:

```bash
# Gracefully signal Laravel queue workers to restart after completing current jobs
php artisan queue:restart

# Restart supervisor worker pools
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart wappiyo-worker:*
```

### 2.7 Disable Maintenance Mode
```bash
# Open application to public traffic
php artisan up
```

---

## 3. Post-Deployment Verification & Smoke Testing

### 3.1 Automated Health & Sanity Tests
Execute automated regression test suite on production environment:

```bash
# Run core health tests
php artisan test --filter="PaymentGatewayLifecycleTest|ProductionEmailPipelineTest|DataSafetyAndImportExportTest"
```

### 3.2 Endpoint HTTP Status Code Checks
Verify essential public and authenticated routes respond with HTTP 200:

| Route Path | Method | Expected Status | Verification Command |
| :--- | :---: | :---: | :--- |
| `/` | GET | `200 OK` | `curl -I https://app.wappiyo.com/` |
| `/login` | GET | `200 OK` | `curl -I https://app.wappiyo.com/login` |
| `/api/health` | GET | `200 OK` | `curl -s https://app.wappiyo.com/api/health` |
| `/manifest.json` | GET | `200 OK` | `curl -I https://app.wappiyo.com/site.webmanifest` |
| `/sw.js` | GET | `200 OK` | `curl -I https://app.wappiyo.com/sw.js` |

### 3.3 Functional Smoke Test Sequence
1. **User Authentication:** Log into customer dashboard as organization owner.
2. **Contact Directory:** Open `/contact`, verify contact list loads cleanly without Vue/Inertia warnings.
3. **WhatsApp Inbox:** Open conversation thread, check message delivery statuses, verify emerald green Call button appears in header.
4. **Dialer Modal:** Click Call button, verify dialer opens, audio synthesizer initializes, live timer increments.
5. **Campaign Dashboard:** Open `/campaign`, verify campaign statistics card, scheduled campaigns, and recipient counts.
6. **Billing & Subscriptions:** Navigate to `/billing`, verify current plan status, renewal date, and invoice list.
7. **Webhook Ingress:** Send test webhook ping from Meta App Dashboard, verify HTTP 200 response and log entry in `processed_webhook_events`.

---

## 4. Rollback Trigger Criteria

Initiate immediate rollback (refer to `PRODUCTION_ROLLBACK_PLAN.md`) if any of the following occur:

1. **Persistent 500 Internal Server Errors** affecting $> 1\%$ of inbound web requests after cache rebuild.
2. **Database migration failure** resulting in broken table constraints or inaccessible data.
3. **Queue worker stall** where queued campaign jobs or transactional emails do not process within 3 minutes.
4. **Payment webhook verification failures** causing legitimate customer subscription updates to fail HMAC validation.
