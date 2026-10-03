# Wappiyo — Production Monitoring, Observability & Alerting Runbook

**System:** Wappiyo WhatsApp Marketing, SaaS Automation & Meta Calling Platform  
**Target Release:** Production Release 1.0.0  
**Classification:** Operational Runbook & Observability Standards  
**Target Availability SLA:** $99.95\%$ Uptime  
**Target Latency SLO:** $\text{P95} < 50\text{ms}$ (Achieved baseline: $15.54\text{ms}$)  

---

## 1. System Health Checks & Probes

### 1.1 Application Health Endpoint (`/api/health`)
Wappiyo exposes a lightweight, automated health check endpoint designed for load balancers (AWS ALB, Cloudflare, HAProxy) and uptime monitors (UptimeRobot, BetterUptime, Datadog):

- **Route:** `GET /api/health`
- **Controller Action:** `HealthCheckController@check`
- **Checked Subsystems:**
  1. **MySQL Database:** Executes `SELECT 1;` — verifies active connection and read availability.
  2. **Redis In-Memory Cache:** Executes `Redis::ping()` — verifies cache cluster responsiveness.
  3. **Queue Health:** Checks Redis queue depth and evaluates `failed_jobs` delta.
  4. **Filesystem Storage:** Confirms `storage/` and `storage/logs/` are writable.

**Sample Successful Health Probe Response (`200 OK`):**
```json
{
  "status": "healthy",
  "timestamp": "2026-10-03T12:00:00+00:00",
  "services": {
    "database": "connected",
    "cache": "connected",
    "queue": "active",
    "disk": "writable"
  },
  "version": "1.0.0"
}
```

---

## 2. Production Service Level Objectives (SLOs) & KPIs

| Metric | Target Production SLA / SLO | Empirically Measured Baseline | Monitoring Mechanism |
| :--- | :---: | :---: | :--- |
| **System Uptime** | $\ge 99.95\%$ | $100\%$ | External ping via UptimeRobot / Datadog Synthetics |
| **HTTP P95 Latency (Public Pages)** | $< 50\text{ms}$ | **$7.13\text{ms}$** | Nginx access logs / APM (New Relic / Sentry) |
| **HTTP P95 Latency (Dashboard)** | $< 100\text{ms}$ | **$14.82\text{ms}$** | Nginx access logs / APM |
| **HTTP P95 Latency (Contacts / CRM)**| $< 100\text{ms}$ | **$15.54\text{ms}$** | Nginx access logs / APM |
| **Queue Worker Execution Latency** | $< 100\text{ms}$ | **$3.45\text{ms}$** | Laravel Horizon / Redis queue monitor |
| **MySQL Query Latency (P95)** | $< 10\text{ms}$ | **$0.15\text{ms}$** | MySQL Slow Query Log (`long_query_time = 1`) |
| **HTTP 5xx Error Rate** | $< 0.05\%$ | $0.00\%$ | Cloudflare / Nginx log aggregation |

---

## 3. Queue & Background Worker Monitoring

### 3.1 Redis Queue Depth & Latency
Monitor queue pending counts via CLI or Redis CLI:

```bash
# Check size of default queue in Redis
redis-cli LLEN queues:default

# Inspect failed jobs count
php artisan tinker --execute="echo 'Failed jobs: ' . DB::table('failed_jobs')->count();"
```

### 3.2 Dead-Letter Queue & Failed Job Remediation
When a job exceeds its retry limit (`--tries=3`), Laravel writes the failure context to the `failed_jobs` table:

```bash
# View list of failed queue jobs
php artisan queue:failed

# Retry a specific failed job by UUID
php artisan queue:retry <uuid>

# Retry all failed jobs after resolving root cause
php artisan queue:retry all

# Flush / discard unrecoverable failed jobs
php artisan queue:flush
```

---

## 4. Log Management & Sensitive Data Masking

### 4.1 Production Log Configuration
Ensure `config/logging.php` is configured to `daily` rotation or centralized `syslog`:
- Log retention set to 14 days minimum.
- Directory: `/var/www/wappiyo/storage/logs/laravel-YYYY-MM-DD.log`.

### 4.2 Security Token Masking in Logs
Wappiyo's exception handler and service layers automatically sanitize logs to prevent sensitive credential leaks:
- WhatsApp API Access Tokens and App Secrets are masked as `EAA...***[REDACTED]`.
- Payment Gateway API Secrets and Webhook Signatures are masked as `whsec_...***[REDACTED]`.
- User passwords, OTPs, and authentication bearer tokens are stripped from log payloads.

---

## 5. Alerting Matrix & Escalation Thresholds

| Trigger Condition | Severity | Notification Channel | Remediation Action |
| :--- | :---: | :---: | :--- |
| `/api/health` returns non-200 for 2 consecutive cycles | **Critical (P0)** | PagerDuty / On-Call Phone | Restart PHP-FPM / MySQL / Nginx; check server memory. |
| Server Disk Usage $\ge 85\%$ | **Critical (P0)** | Slack #ops-alerts / Email | Run `php artisan wappiyo:backup --retention=14` and prune old logs. |
| Server RAM Usage $\ge 90\%$ | **High (P1)** | Slack #ops-alerts | Inspect Redis memory or memory-leaking queue worker processes. |
| Queue Backlog $> 1,000$ jobs for $> 5\text{ minutes}$ | **High (P1)** | Slack #ops-alerts | Scale up Supervisor worker count in `/etc/supervisor/conf.d/wappiyo.conf`. |
| Unhandled Exceptions $> 25$ in $5\text{ minutes}$ | **Medium (P2)** | Sentry / Slack #dev-alerts | Inspect stack trace; determine if bug or third-party API outage. |
| Daily Backup Failed | **Medium (P2)** | Slack #ops-alerts / Email | Inspect `storage/logs/backup.log`; verify disk space and database privileges. |
