# Meta WhatsApp Calling Module — Setup & Architecture Guide

## 1. Overview
The Wappiyo WhatsApp Calling Module integrates **Meta's official WhatsApp Business Platform (Cloud API)** voice calling capabilities directly into Wappiyo CRM. Calling is a first-class CRM activity linked to contacts, conversation threads, team analytics, and lead dispositions.

---

## 2. Meta Business Platform Prerequisites

### 2.1 WhatsApp Business Account (WABA) Requirements
1. **Verified Meta Business Manager**:
   - The organization must possess a verified Meta Business Account.
2. **Payment Method & Tier Eligibility**:
   - A valid payment method attached in the Meta Business Manager.
   - Calling is supported on WhatsApp Business Accounts in good standing (`APPROVED` account review status, `HIGH` or `MEDIUM` quality rating).
3. **Graph API Version**:
   - Graph API version `v20.0` or higher (`v21.0` recommended).

### 2.2 Phone Number Requirements
1. **Voice-Capable Business Phone Number**:
   - The phone number registered in WhatsApp Manager must be active and verified.
   - For inbound and outbound calling, numbers with two-step verification configured and standard WhatsApp Business Cloud API registration are supported.
2. **Calling Feature Enablement**:
   - In Meta Business Suite / WhatsApp Manager, verify voice calling is permitted for the registered phone number ID.

### 2.3 Required Meta Permissions
When generating the System User permanent access token:
- `whatsapp_business_messaging`
- `whatsapp_business_management`
- `business_management`

---

## 3. Webhook Configuration

To receive real-time call events (`ringing`, `accepted`, `terminated`, `missed`, `failed`):

1. Navigate to **Meta App Dashboard** &rarr; **WhatsApp** &rarr; **Configuration**.
2. **Callback URL**:
   ```text
   https://your-domain.com/webhook/whatsapp/{organization_identifier}
   ```
3. **Verify Token**:
   - Found in Wappiyo under **Settings** &rarr; **WhatsApp** &rarr; **Verify Token**.
4. **Webhook Fields Subscription**:
   - Under **Webhook fields**, click **Manage**.
   - Subscribe to the **`calls`** field (in addition to `messages`).

---

## 4. Wappiyo Calling Architecture

### 4.1 Provider Abstraction
```text
CallingProviderInterface
        ↓
MetaWhatsAppCallingProvider
        ↓
Meta Graph API (https://graph.facebook.com/v20.0/{phone_number_id}/calls)
```
The CRM application logic is decoupled from Meta API calls via `CallingService` and `CallingProviderInterface`.

### 4.2 Normalized Status Lifecycle
Meta events are mapped to internal normalized statuses via `CallStatusProcessor`:
- `ringing` &rarr; `ringing`
- `pre_accept` / `connecting` &rarr; `connecting`
- `accepted` / `connected` &rarr; `connected`
- `terminated` / `completed` &rarr; `completed`
- `missed` / `no_answer` / `timeout` &rarr; `missed`
- `rejected` / `declined` &rarr; `rejected`
- `failed` / `error` &rarr; `failed`

**Out-of-Order Webhook Protection**: Terminal states (`completed`, `missed`, `failed`) can never be overwritten by delayed preceding webhooks (`ringing` or `connecting`).

### 4.3 Webhook Idempotency
- Events are tracked in `processed_webhook_events` with unique event IDs.
- Duplicate deliveries with the same event ID are safely acknowledged without creating duplicate call records or double-counting talk time.

---

## 5. CRM User Experience

### 5.1 Call Initiation Locations
1. **WhatsApp Inbox / Conversation Header**: `CallButton.vue` positioned in the chat header actions.
2. **Contact CRM Profile**: Quick call button in the sticky profile header and a dedicated **Calls** history tab.
3. **Call History Page (`/calls`)**: Dedicated hub with analytics KPIs, multi-attribute filter toolbar, call table, and "Start Call" dialer.
4. **Chat Conversation Stream (`ChatThread.vue`)**: Calls appear inline with messages, duration badges, and outcome tags.

### 5.2 Live Calling Interface (`CallModal.vue`)
- Real-time call state: Calling &rarr; Ringing &rarr; Connected (live timer) &rarr; Ended.
- Web Audio ringtone feedback.
- Mute/Speaker toggles and End Call button.
- Floating minimized call bar (`ActiveCallBar.vue`) when navigating across other CRM sections during an active call.

### 5.3 Post-Call Wrap-Up & Dispositions
Immediately upon call completion:
- Select disposition outcome (`Interested`, `Follow-up Required`, `Converted`, `No Answer`, `Callback Requested`, etc.).
- Add internal team notes saved against the call record and contact.
- Schedule future follow-up dates and reminder notes.

---

## 6. Multi-Tenancy & Security Rules
- **Tenant Isolation**: Every call query and webhook is strictly scoped to `organization_id`.
- **IDOR Protection**: Requests attempting to view or modify a call belonging to another organization return `404 Not Found`.
- **RBAC**: Calls adhere to team roles (`owner`, `manager`, `agent`). Agents without `calling.manage` can only view their own calls.
- **Credential Protection**: Graph API tokens and internal credentials are never returned in public JSON responses.
- **Subscription Limits**: Centralized limit checks (`calling_limit`) in `SubscriptionService`.

---

## 7. Configuration Toggle in Wappiyo

1. Navigate to **Settings** &rarr; **WhatsApp** as an Organization Owner or Admin.
2. Under **WhatsApp Voice Calling (Meta Cloud API)**, click **Enable Calling**.
3. Confirm that the WABA ID, Phone Number ID, and Permanent Access Token are valid.

---

## 8. Troubleshooting & FAQ

| Issue | Cause | Resolution |
|-------|-------|------------|
| `WhatsApp Calling is not configured` | Calling toggle is OFF or WhatsApp credentials missing | Navigate to Settings &rarr; WhatsApp, configure phone credentials, and toggle Calling ON |
| Webhook events not updating UI | Webhook `calls` field not subscribed in Meta Dashboard | In Meta App Dashboard &rarr; WhatsApp &rarr; Configuration, subscribe to the `calls` webhook field |
| Real-time call updates delayed | Pusher/Echo connection not configured or disconnected | Check `pusher_app_key` and `pusher_app_cluster` in Admin/Settings |
| Number requires a country to be specified | Contact phone lacks international dialing prefix | Provide phone numbers in E.164 format (e.g. `+91 98765 43210` or `919876543210`) |

---

## 9. Production Release Checklist
- [x] Migrations executed (`calls`, `processed_webhook_events`, `calling` module permissions).
- [x] Provider abstraction and normalized status processor verified.
- [x] Webhook idempotency and ChatLog activity timeline logging verified.
- [x] Multi-tenant isolation and IDOR protection tests passing (61/61 tests pass).
- [x] Frontend compiled cleanly with Vite (`npm run build`).
- [x] Dark mode, Light mode, and mobile responsiveness audited.
- [ ] Active Meta WABA calling approval enabled in Meta WhatsApp Manager for live production phone numbers.
