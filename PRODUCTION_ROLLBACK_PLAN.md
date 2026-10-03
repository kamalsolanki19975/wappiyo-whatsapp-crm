# Wappiyo — Production Rollback Plan & Disaster Recovery Runbook

**System:** Wappiyo WhatsApp Marketing, SaaS Automation & Meta Calling Platform  
**Target Release:** Production Release 1.0.0  
**Classification:** Disaster Recovery & Operational Runbook  
**Target Recovery Time Objective (RTO):** $\le 15\text{ minutes}$  
**Target Recovery Point Objective (RPO):** $\le 1\text{ hour}$ (or zero data loss for non-destructive rollbacks)  

---

## 1. Rollback Decision Framework

A rollback must be initiated immediately upon detection of any Severity-0 or Severity-1 incident that cannot be resolved via configuration fix within a **15-minute diagnostic window**:

### 1.1 Trigger Criteria
| Severity | Description | Examples | Action |
| :---: | :--- | :--- | :--- |
| **P0** | **Critical Service Outage** | Application returning HTTP 500 across all routes; database connection exhaustion; complete failure of Nginx / PHP-FPM socket. | Immediate Rollback |
| **P0** | **Data Corruption / Security Leak** | Cross-tenant data leakage detected; multi-tenancy `organization_id` filter bypass; SQL syntax errors corrupting records. | Immediate Maintenance + Rollback |
| **P1** | **Payment & Ingestion Breakdown** | Webhook HMAC verification failing 100% of Meta or Stripe events; user subscription renewals failing silently. | Rollback if not fixable in 15m |
| **P1** | **Queue Worker Deadlock** | Campaign message dispatch or transactional email queues halted with memory leak or continuous worker crashes. | Rollback if not fixable in 15m |

---

## 2. Deterministic Rollback Procedures

### Step 1: Engage Maintenance Mode
Prevent users from submitting partial forms or generating mismatched data while rolling back:

```bash
cd /var/www/wappiyo
php artisan down --secret="WappiyoEmergencyRollback" --render="errors::503" --retry=60
```

---

### Step 2: Codebase & Git Rollback
Identify the prior stable Git commit or release tag (e.g., `v1.0.0-rc1` or previous commit SHA `PREV_STABLE_COMMIT`):

```bash
# Fetch git history and checkout previous stable release
git fetch --all --tags
git checkout <PREV_STABLE_COMMIT>

# Verify repository is in expected state
git log -1 --oneline
```

---

### Step 3: Database Rollback Strategy

Choose between **Schema Rollback** (if new migrations did not destroy existing columns) or **Full Database Restore** (if destructive changes or corruption occurred):

#### Option A: Migration Rollback (Preferred for Non-Destructive Migrations)
If the deployment only added new tables or nullable columns:

```bash
# Roll back only the batch applied during the current deployment
php artisan migrate:rollback --step=1 --force
```

#### Option B: Full Database Restore (Disaster Recovery via `wappiyo:restore`)
If the database schema was corrupted or data integrity compromised, restore the pre-deployment snapshot created prior to deployment:

```bash
# 1. Identify pre-deployment backup archive
ls -lt storage/app/backups/wappiyo_backup_*.sql.gz | head -n 1

# 2. Execute automated restore with SHA-256 integrity validation
php artisan wappiyo:restore storage/app/backups/wappiyo_backup_YYYY-MM-DD_HHMMSS.sql.gz --force

# 3. Verify table counts and row integrity
php artisan tinker --execute="echo 'Users: ' . App\Models\User::count() . ', Orgs: ' . App\Models\Organization::count();"
```

---

### Step 4: Rebuild Dependencies & Frontend Assets

Recompile PHP autoloaders and frontend bundles to match the rolled-back code:

```bash
# Re-install dependencies matching the previous composer.lock
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Re-compile frontend assets matching the previous package-lock.json
npm ci --prefer-offline
npm run build
```

---

### Step 5: Redis Queue & Cache Reset

Flush stale route and config caches and clear deadlocked queue jobs:

```bash
# Clear all compiled Laravel caches
php artisan optimize:clear

# Restart all background queue workers immediately
php artisan queue:restart
sudo supervisorctl restart wappiyo-worker:*

# Re-warm production caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Step 6: Post-Rollback Verification Smoke Tests

Execute fast automated sanity tests to verify application stability:

```bash
# Run core regression suite
php artisan test --filter="PaymentGatewayLifecycleTest|ProductionEmailPipelineTest"

# Verify web endpoint returns HTTP 200
curl -I https://app.wappiyo.com/login
```

---

### Step 7: Restore Public Traffic

Once verification passes, lift maintenance mode:

```bash
php artisan up
```

---

## 3. Post-Rollback Incident Management

1. **Team Notification:** Notify engineering, product, and customer success teams via incident channel.
2. **Customer Communications:** If maintenance window exceeded 5 minutes, publish incident update to Status Page (`https://status.wappiyo.com`).
3. **Log Collection & Archival:**
   ```bash
   tar -czf /tmp/rollback_incident_logs_$(date +%s).tar.gz storage/logs/ /var/log/nginx/ /var/log/supervisor/
   ```
4. **Post-Mortem Root Cause Analysis (RCA):**
   - Identify exact line, dependency, or query causing the failure.
   - Replicate the failure in local staging environment.
   - Author automated regression test before re-attempting deployment.
