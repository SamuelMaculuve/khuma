# Feature Research

**Domain:** Multi-tenant CRM SaaS for SMB sales teams (Khuma v1.0 — completing the CRM)
**Researched:** 2026-04-28
**Confidence:** HIGH (CRM ecosystem is mature and well-documented; recommendations grounded in HubSpot, Pipedrive, Zoho, Close, Attio, Salesforce Essentials feature parity)

## Scope Note

This research covers the **gap between Khuma today (lead tracker + messaging + billing) and a complete CRM**. Already built (excluded from re-research): leads, kanban, clients, companies, WhatsApp, email campaigns + inbound, call logs, tickets, billing, RBAC, teams.

Each capability area is decomposed into specific features with category, complexity (S/M/L), and dependencies.

---

## Feature Landscape

### Table Stakes (Users Expect These)

Missing any of these = "this isn't a real CRM."

| # | Feature | Why Expected | Complexity | Notes |
|---|---------|--------------|------------|-------|
| **1. Deals/Opportunities & Forecasting** | | | | |
| 1.1 | Deals as first-class model (separate from Leads) | Industry-standard split: Lead = unqualified inbound, Deal = qualified pipeline. All 6 reference CRMs do this. | M | Khuma conflates these today. Migration path: Lead.status='qualified' → spawn Deal. |
| 1.2 | Deal stages with weighted probability (%) | Forecast math is `Σ(deal.value × stage.probability)`. Standard since Salesforce 2003. | S | Per-pipeline stages, configurable per tenant. |
| 1.3 | Multiple pipelines per tenant | Sales/Renewals/Partner pipelines coexist. Pipedrive popularized for SMB. | M | Adds `pipeline_id` to deals; UI selector on kanban. |
| 1.4 | Expected close date + amount + currency | Required to forecast at all. | S | Currency at deal level (not tenant level). |
| 1.5 | Stage history / audit trail | "How long did this deal sit in 'Negotiation'?" Mirrors existing `LeadHistories` pattern. | S | Reuse `LeadObserver` pattern. |
| 1.6 | Won/Lost reason capture | Lost-reason analytics is in every CRM's reporting. | S | Enum or free-text + tag. |
| 1.7 | Weighted forecast view (this month/quarter) | Sales managers' #1 report. | M | Reporting dependency. |
| **2. Tasks/Activities & Calendar** | | | | |
| 2.1 | Task model (call/meeting/email/todo) with due date, owner, related record | Universal across all 6 CRMs. | S | Polymorphic `taskable` (lead, deal, client). |
| 2.2 | Activity timeline on each record | Unified view of calls + emails + WhatsApp + tasks. | M | Khuma already has Messages, CallLogs, EmailCampaignLog, InboundEmail — needs aggregator. |
| 2.3 | Reminders (in-app + email) | Reps live or die by reminders. | S | Scheduled job; reuse existing queue. |
| 2.4 | Calendar view (day/week/month) of activities | Table-stakes UI surface. | M | FullCalendar.js or similar; Livewire-compatible. |
| 2.5 | Google Calendar 2-way sync | 80%+ SMB market on Google Workspace. | L | OAuth, watch channels, conflict resolution. **Hardest task feature.** |
| 2.6 | Outlook/Microsoft 365 calendar 2-way sync | Other 20%. | L | Microsoft Graph API; mirrors Google pattern. |
| **3. Custom Fields, Tags, CSV, Dedupe** | | | | |
| 3.1 | Per-tenant custom fields on Lead/Deal/Contact/Account | Required for non-trivial customers. Universal. | M | EAV table or JSON column; recommend JSON column with schema definition. |
| 3.2 | Field types: text, number, date, dropdown, multi-select, checkbox, URL | Standard set. | S | Validation rules per type. |
| 3.3 | Tags (free-form labels, many-to-many, polymorphic) | Lighter than custom fields, used for segmentation. | S | Consider `spatie/laravel-tags`. |
| 3.4 | CSV import wizard with field mapping + preview | First thing every new tenant does. Onboarding-critical. | M | Chunked job; per-row error report; dedupe-on-import option. |
| 3.5 | CSV export (filtered list → file) | Required for GDPR + ad-hoc reporting. | S | Streamed response or queued job for large sets. |
| 3.6 | Duplicate detection (email, phone, normalized name) | Pipedrive/HubSpot flag duplicates on creation; Salesforce has dedicated dedupe rules. | M | Background job + on-create check; phone E.164 normalization is non-trivial. |
| 3.7 | Manual merge UI (pick winner, merge fields, preserve history) | After detection, must resolve. | M | Transaction-heavy; preserve all child records (messages, notes, history). |
| **4. Workflow Automation** | | | | |
| 4.1 | Trigger/Condition/Action engine (visual builder) | Defining feature of modern CRM. HubSpot Workflows, Pipedrive Automations, Zoho Blueprint. | L | In-app, queue-driven. Triggers: created/updated/stage-changed/field-changed/time-based. |
| 4.2 | Action library: send email, send WhatsApp, create task, update field, assign user, add tag, call webhook | Standard action set. | M | Each action a Job class; reuse existing mailers/messaging. |
| 4.3 | Drip email sequences (multi-step, time-spaced) | Close.io/Outreach made this table stakes for outbound. | M | Sequence = ordered steps; per-recipient state machine. |
| 4.4 | Outbound webhooks (POST on event) | Zapier/Make/n8n integration without building hundreds of native integrations. | S | Per-tenant subscriptions; HMAC-signed; retry queue. |
| 4.5 | Round-robin / load-balanced lead assignment | Standard for inbound routing. | S | Action within engine. |
| 4.6 | Workflow logs / run history | Debugging is impossible without this. | S | Every run = a row; show last N per workflow. |
| **5. Reporting & Analytics** | | | | |
| 5.1 | Sales funnel (count + value by stage) | First chart every CRM ships. | S | SQL aggregate; Chart.js/ApexCharts. |
| 5.2 | Win/loss rate over time | Manager's KPI. | S | Aggregate over closed deals. |
| 5.3 | Conversion rate by source | Marketing ROI. | S | Group by `lead.source`. |
| 5.4 | Rep leaderboard (deals won, revenue, activities) | Motivational + accountability. | S | Group by `owner_id`. |
| 5.5 | Activity report (calls/emails/meetings per rep per period) | Manager view. | S | Aggregate over Tasks + Messages. |
| 5.6 | Forecast view (weighted pipeline this period) | Depends on 1.7. | M | Cross-cuts deals + stages + probability. |
| 5.7 | Saved/scheduled reports (email me weekly) | HubSpot/Zoho ship this. | M | Scheduled job; PDF or HTML email. |
| 5.8 | SaaS-side metrics: MRR/ARR/churn/LTV (admin-only) | For Khuma operators, not tenants. | M | Off existing Subscription/Invoice/Payment models. |
| **6. Security & Compliance** | | | | |
| 6.1 | Two-factor authentication (TOTP) | Table stakes since 2020. | S | `pragmarx/google2fa-laravel` or Fortify. |
| 6.2 | System-wide audit log (who did what, when) | Required for B2B sale > 10 seats. | M | Generic events table; log auth + record CRUD + permission changes. |
| 6.3 | GDPR data export (per contact, all data) | Legal requirement in EU; expected as data laws emerge regionally. | M | Per-record export job; ZIP of JSON + attachments. |
| 6.4 | GDPR right-to-erasure (anonymize/delete) | Legal requirement. | M | Soft-delete + scrub PII on linked records (messages, calls). |
| 6.5 | Password policy + session expiry | Standard. | S | Laravel config + middleware. |
| 6.6 | SSO via Google OAuth | 80% of SMB. Table stakes for Khuma's SMB target. | M | Laravel Socialite. |
| **7. Public API + Webhooks** | | | | |
| 7.1 | REST API for core resources (leads, deals, contacts, accounts, tasks) | Required for integrations. Khuma already has Sanctum. | M | Resource controllers + API resources + per-tenant token. |
| 7.2 | Per-tenant API tokens with scopes | Sanctum supports; need UI. | S | Settings page lists/revokes tokens. |
| 7.3 | Webhook subscriptions (managed via UI + API) | See 4.4 — same plumbing. | S | Shared with workflow webhooks. |
| 7.4 | API documentation (OpenAPI/Swagger) | Required for integrators. | S | `darkaonline/l5-swagger` or Scribe. |
| 7.5 | Rate limiting per token | Operational hygiene. | S | Laravel throttle middleware. |
| **8. Lead Capture & Customer Portal** | | | | |
| 8.1 | Embeddable web form widget (JS snippet) | HubSpot/Pipedrive/Zoho all ship this. | M | Hosted form renderer + JS embed; CSRF-exempt POST endpoint scoped by form token. |
| 8.2 | Form builder UI (fields + redirect URL + thank-you) | Companion to 8.1. | M | Maps to custom fields (3.1). |
| 8.3 | Spam protection (honeypot + rate limit + optional reCAPTCHA) | Public forms attract bots. | S | Standard hardening. |
| **9. AI Assist** | | | | |
| 9.1 | Conversation summarization (deal/contact thread → bullets) | HubSpot Breeze, Zoho Zia, Salesforce Einstein all ship this. | M | OpenAI/Anthropic API; per-tenant key option. |
| 9.2 | Email/WhatsApp draft suggestions | Now-standard since GPT-4. | M | Same infra as 9.1. |
| 9.3 | Lead scoring (rule-based + optionally ML) | Standard from Pipedrive/HubSpot. Rule-based first. | M | Score = Σ(rule weights); recompute on field/activity change. |
| **10. Misc Foundational** | | | | |
| 10.1 | In-app notification center (bell icon + history) | Universal modern UI. | M | Notifications table + Livewire poll/Echo. |
| 10.2 | Email notifications (configurable per event) | Pairs with 10.1. | S | Reuse existing mailer. |
| 10.3 | Internationalization (PT/EN minimum) | Khuma's market is PT-speaking; EN for export. Already partial. | M | Laravel localization; extract existing PT strings to lang files. |
| 10.4 | Multi-currency on deals (per-deal currency, tenant base, FX rate) | Mozambique CRM with regional clients needs MZN + ZAR + USD + EUR. | M | Store original currency + base-currency snapshot at deal time. |
| 10.5 | File attachments on records | Universal. | S | `spatie/laravel-medialibrary`; S3-compatible storage. |
| 10.6 | Contacts vs Accounts split (Person vs customer-Company, M:N) | B2B-standard data model. | M | **Naming collision** with tenant `Companies` — recommend introducing `Account` model rather than renaming. |
| 10.7 | Products catalog + line items + Quotes/Proposals | Quotes are differentiator-tier; full quote-to-PDF is L. | L | Products → DealLineItems → Quote PDF. Optional in v1.0. |

### Differentiators (Competitive Advantage)

Aligned with Khuma's Core Value: "leads → deals → won revenue with WhatsApp/email built in and automation removing manual follow-up."

| # | Feature | Value Proposition | Complexity | Notes |
|---|---------|-------------------|------------|-------|
| D1 | **WhatsApp-native automation actions** | Every other CRM treats WhatsApp as a 3rd-party plugin. Khuma already runs Evolution API per tenant — workflow actions can natively send WhatsApp. | S (given existing infra) | "Send WhatsApp template after stage change to Negotiation." |
| D2 | **WhatsApp drip sequences** | Outbound WhatsApp cadences with 24h-window awareness, where competitors offer email-only. | M | Respect Meta's 24h customer-care window; template messages outside it. |
| D3 | **MZN + M-Pesa-aware pricing/quotes** | Localized money handling; competitors are USD/EUR-centric. | M | Currency formatting + M-Pesa payment links on quotes. |
| D4 | **Per-tenant inbound email aliases tied to teams** | Already partly built — surface as differentiator: each team gets its own inbox that auto-creates leads. | S (mostly built) | Polish UX; document. |
| D5 | **Unified timeline across WhatsApp + Email + Calls** | Most CRMs split channels into separate tabs. Khuma can render true cross-channel. | M | Same as 2.2; positioned as differentiator. |
| D6 | **Portuguese-first UX with regional tax/phone formats** | Lusophone market underserved by English-first CRMs. | M | i18n is table stakes; PT-first defaults are differentiator. |
| D7 | **Lead enrichment from inbound message metadata** | WhatsApp profile name, email signature parsing, phone E.164 normalization to auto-fill contact data. | M | Parsing pipeline; falls back gracefully. |
| D8 | **AI "next-best-action" tied to actual channel mix** | "Deal silent 7 days; suggest WhatsApp follow-up because last 3 replies were on WhatsApp." Channel-aware, not generic. | L | Combines 9.1 + activity history + heuristics. |

### Anti-Features (Avoid or Defer)

Features that look good in demos but harm Khuma's focus, scope, or operability.

| # | Feature | Why Requested | Why Problematic | Alternative |
|---|---------|---------------|-----------------|-------------|
| A1 | **Native Slack/Teams/Twilio/Stripe/Calendly/Mailchimp/etc. integrations** | "Connect to everything" demo appeal | Each = maintenance burden + auth flow + breaking-change risk. SMB CRMs sink here. | Robust public API + outbound webhooks (4.4, 7.x) → users wire via Zapier/Make/n8n. |
| A2 | **Real-time collaborative editing of deals (Google Docs–style)** | Modern UX trend (Attio does it) | Massive Livewire/Reverb scope; SMB sales reps don't co-edit deals. | Optimistic save + last-write-wins + activity log. |
| A3 | **Mobile native apps (iOS/Android)** | "Sales reps are mobile" | Doubles surface area; Livewire SSR is already responsive. | PWA + responsive web; native deferred to v2+. |
| A4 | **Generic "everything is a record" data model (Attio-style)** | Future-proofing, flexibility | Massive UX cost + slow queries + analytics nightmare. Attio raised $115M to make this work. | Fixed core entities + custom fields (3.1) for flexibility. |
| A5 | **Custom report builder with drag-drop pivot tables** | "Tableau in our CRM" | 3+ engineer-months for a worse Tableau. | Curated reports (5.1–5.7) + CSV export → users pivot in Excel/Sheets. |
| A6 | **In-CRM phone dialer / VoIP (Close.io-style)** | Power-feature appeal | Telephony is a separate hard product (carriers, SIP, recording, compliance). | Click-to-call via `tel:` links; CallLog records outcomes. |
| A7 | **Marketing automation suite (landing pages, A/B tests, ad spend)** | "Replace HubSpot Marketing Hub" | Doubles product scope. HubSpot has 500 engineers on this. | Stay sales-CRM focused; lead capture forms (8.1) are the boundary. |
| A8 | **Per-record granular field-level permissions** | Enterprise security demo | Massive complexity in queries, UI, audit. SMB doesn't need it. | Role-based + record-owner + team scoping. |
| A9 | **Email tracking pixels (open/click)** | Standard sales-engagement feature | Increasingly blocked (Apple MPP, corporate proxies); GDPR consent issues; false-positive opens are now the norm. | Track replies + bounces (already have via Mailcow), not opens. |
| A10 | **Custom domains for tenant portals (white-label)** | Reseller demand | Out of scope per PROJECT.md; cert + DNS automation burden. | Subdomain on `khuma.app` only. |
| A11 | **Workflow nested branching with full programming primitives** | "What if we need conditional loops?" | Becomes a programming language; debugging hell. | Linear sequences + conditions + multiple workflows; complex logic → webhook to user's own service. |
| A12 | **Live chat / website visitor tracking widget** | HubSpot ships it | Different product (Intercom/Crisp territory); separate frontend infra. | Refer users to dedicated tools; lead forms (8.1) are sufficient. |
| A13 | **AI agents that autonomously send messages to customers** | Hype-cycle demand | Reputational risk — auto-send goes wrong loudly; hallucinated personal data. | AI drafts (9.2) — human approves before send. |

---

## Feature Dependencies

```
Deals (1.1)
  ├──requires──> Multi-tenancy hardening (PROJECT.md, decision pending)
  ├──enables───> Forecasting (1.7) ──requires──> Multi-currency (10.4)
  ├──enables───> Won/Loss reporting (5.2)
  └──enables───> Quotes (10.7) ──requires──> Products catalog (10.7) + PDF generator

Tasks (2.1)
  ├──enables───> Activity timeline (2.2) ──aggregates──> Messages, CallLogs, Emails (existing)
  ├──enables───> Reminders (2.3)
  ├──enables───> Calendar UI (2.4) ──enables──> Google sync (2.5) ──pattern-mirrors──> Outlook sync (2.6)
  └──used-by──> Workflow actions (4.2: "create task")

Custom fields (3.1)
  ├──enables───> Form builder (8.2) ──enables──> Lead capture forms (8.1)
  ├──used-by──> CSV import (3.4) ──requires──> Duplicate detection (3.6) ──enables──> Manual merge (3.7)
  └──used-by──> Workflow conditions (4.1)

Tags (3.3) ──used-by──> Workflow conditions (4.1), Reporting filters (5.x)

Workflow engine (4.1)
  ├──requires──> Job queue (existing)
  ├──requires──> Audit log (6.2) for run history (4.6)
  ├──includes──> Webhook actions (4.4) ──shares-infra──> Public API webhooks (7.3)
  ├──includes──> Drip sequences (4.3) ──requires──> Email mailer (existing) + WhatsApp (existing → D2)
  └──includes──> Round-robin assignment (4.5)

Reporting (5.x)
  ├──requires──> Deals (1.x)
  ├──requires──> Tasks (2.x) for activity reports
  └──requires──> Stage history (1.5)

Audit log (6.2) ──used-by──> Workflow run history (4.6), GDPR export (6.3), security review

GDPR export (6.3) + erasure (6.4)
  ├──requires──> Attachments (10.5) inventory
  └──requires──> Cross-table PII inventory (clients, leads, deals, messages, calls, emails)

Public API (7.1)
  ├──requires──> API tokens (7.2)
  ├──enables───> Webhooks (7.3)
  └──requires──> Rate limiting (7.5)

Lead capture forms (8.1)
  ├──requires──> Custom fields (3.1)
  ├──requires──> Spam protection (8.3)
  └──enables───> Workflow trigger "form submitted" (4.1)

AI assist (9.x)
  ├──requires──> Activity timeline (2.2) for context
  ├──D8 requires──> Channel history (existing Messages + CallLogs + Emails)
  └──optionally requires──> Per-tenant AI key storage (settings)

Notifications (10.1)
  ├──used-by──> Reminders (2.3)
  ├──used-by──> Workflow actions (4.2: "notify user")
  └──used-by──> Mentions (future)

i18n (10.3) ──blocks──> Multi-currency display (10.4) for proper number formatting

Contacts/Accounts split (10.6)
  ├──blocks──> Naming refactor of tenant `Companies` model
  └──impacts──> Most existing queries — high-risk refactor
```

### Critical Dependency Notes

- **Multi-tenancy hardening (global scope/trait) MUST land before Deals (1.1)** — adding a new tenant-scoped table without fixing the manual `company_id` pattern compounds the leak risk flagged in PROJECT.md.
- **Audit log (6.2) should land before or with Workflow engine (4.1)** — workflow run history piggybacks on audit infrastructure.
- **Custom fields (3.1) is on the critical path for CSV import, lead forms, and workflow conditions** — sequence early.
- **Contacts/Accounts split (10.6) has a naming collision** with tenant `Companies`. Either rename tenant model to `Tenant`/`Workspace` (large refactor) or introduce `Account` as the customer-company model. **Recommend the latter** for v1.0; defer rename.
- **Calendar sync (2.5, 2.6) is the highest-risk task feature** — OAuth lifecycle, token refresh, two-way conflict resolution, watch-channel renewal. Allocate buffer or defer to v1.1.
- **Multi-currency (10.4) blocks accurate forecasting (1.7)** — if forecasting is in v1.0, currency must be too.

---

## MVP Definition for v1.0

### Launch With (v1.0 Core — aligns with PROJECT.md priority order)

- [ ] **1.1–1.7 Deals/Opportunities & weighted forecast** — core value
- [ ] **2.1–2.4 Tasks/Activities + in-app calendar (no external sync yet)** — core value
- [ ] **3.1–3.7 Custom fields + tags + CSV I/O + dedupe + merge** — onboarding-critical
- [ ] **4.1, 4.2, 4.4, 4.5, 4.6 Workflow engine (triggers, core actions, webhooks, round-robin, run history)** — core value
- [ ] **5.1–5.6 Curated reports (funnel, win/loss, conversion, leaderboard, activity, forecast)** — core value
- [ ] **6.1, 6.5 2FA + password policy** — table stakes for SMB B2B sale
- [ ] **10.1, 10.2 In-app + email notifications** — required for tasks/reminders
- [ ] **10.3 i18n formalization (PT + EN)** — Khuma's market
- [ ] **10.4 Multi-currency on deals** — required for accurate forecast
- [ ] **10.5 File attachments** — table stakes
- [ ] **D1, D2 WhatsApp-native workflow actions + drip sequences** — Khuma's differentiator

### Add After v1.0 (v1.x)

- [ ] **2.5, 2.6 Google + Outlook calendar sync** — high value, high cost; defer to v1.1 to de-risk v1.0
- [ ] **4.3 Email drip sequences** — defer behind WhatsApp drips (D2) which are the differentiator
- [ ] **5.7 Scheduled reports** — needs reports stable first
- [ ] **5.8 SaaS-side MRR/ARR/churn dashboards** — admin-only, ad-hoc SQL initially
- [ ] **6.2 System-wide audit log** — should land with workflow run history; if scope tight, ship workflow logs first
- [ ] **6.3, 6.4 GDPR export + erasure** — needed before any EU/regulated tenant
- [ ] **6.6 Google SSO** — high value for SMB
- [ ] **7.1–7.5 Public API + docs + tokens UI + rate limiting** — webhooks (7.3) ship with workflows; full REST API in v1.1
- [ ] **8.1–8.3 Lead capture forms + builder** — needs custom fields stable
- [ ] **9.1, 9.2, 9.3 AI summarization + draft + lead scoring** — once timeline (2.2) is solid
- [ ] **10.6 Contacts/Accounts split** — large refactor; care needed

### Future (v2+)

- [ ] **D8 AI next-best-action with channel-awareness** — needs significant data
- [ ] **10.7 Products catalog + Quotes/Proposals + PDF**
- [ ] **D3 polish on M-Pesa quote payment links** — pairs with 10.7

---

## Feature Prioritization Matrix

| # | Feature | User Value | Implementation Cost | Priority |
|---|---------|------------|---------------------|----------|
| 1.1–1.7 | Deals + forecasting | HIGH | M | **P1** |
| 2.1–2.4 | Tasks + in-app calendar | HIGH | M | **P1** |
| 2.5, 2.6 | Google/Outlook sync | HIGH | L | P2 |
| 3.1, 3.4, 3.6, 3.7 | Custom fields + CSV + dedupe + merge | HIGH | M | **P1** |
| 3.3 | Tags | MEDIUM | S | **P1** (cheap) |
| 4.1, 4.2 | Workflow engine + actions | HIGH | L | **P1** |
| 4.3 | Email drip | MEDIUM | M | P2 |
| 4.4 | Webhooks | HIGH | S | **P1** |
| 4.5 | Round-robin | MEDIUM | S | **P1** |
| 4.6 | Workflow logs | HIGH (debugging) | S | **P1** |
| 5.1–5.6 | Curated reports | HIGH | M | **P1** |
| 5.7 | Scheduled reports | MEDIUM | M | P2 |
| 5.8 | SaaS metrics | MEDIUM (internal) | M | P2 |
| 6.1 | 2FA | HIGH | S | **P1** |
| 6.2 | Audit log | HIGH | M | P2 (P1 if scope allows) |
| 6.3, 6.4 | GDPR export/erasure | HIGH (compliance) | M | P2 |
| 6.6 | Google SSO | HIGH | M | P2 |
| 7.1, 7.2, 7.4, 7.5 | REST API + tokens + docs + throttle | HIGH | M | P2 |
| 7.3 | Webhook subs UI | HIGH | S | **P1** (with 4.4) |
| 8.1–8.3 | Lead forms + builder + spam | HIGH | M | P2 |
| 9.1, 9.2 | AI summarize + draft | MEDIUM-HIGH | M | P2 |
| 9.3 | Lead scoring | MEDIUM | M | P3 |
| 10.1, 10.2 | Notifications | HIGH | M | **P1** |
| 10.3 | i18n | HIGH (PT market) | M | **P1** |
| 10.4 | Multi-currency | HIGH | M | **P1** |
| 10.5 | Attachments | HIGH | S | **P1** |
| 10.6 | Contacts/Accounts split | MEDIUM | M-L (refactor risk) | P2 |
| 10.7 | Products + Quotes | MEDIUM | L | P3 |
| D1 | WhatsApp workflow actions | HIGH | S (infra exists) | **P1** |
| D2 | WhatsApp drip sequences | HIGH | M | **P1** |
| D3 | MZN/M-Pesa quote pricing | MEDIUM | M | P3 (with 10.7) |
| D4 | Team inbound aliases polish | MEDIUM | S | **P1** (mostly done) |
| D5 | Unified channel timeline | HIGH | M | **P1** (= 2.2) |
| D6 | PT-first UX | HIGH | M | **P1** (= 10.3) |
| D7 | Lead enrichment from messages | MEDIUM | M | P2 |
| D8 | Channel-aware next-best-action | HIGH | L | P3 |

---

## Competitor Feature Reference

| Capability | HubSpot | Pipedrive | Zoho CRM | Close | Attio | SF Essentials | Khuma Approach |
|---|---|---|---|---|---|---|---|
| Deals + forecasting | Standard | **Best-in-class** SMB UX | Standard + Blueprint | Power-user focus | Flexible records | Industry standard | Pipedrive-style stages + weighted forecast |
| Calendar sync | Both | Both | Both + Zoho native | Both | Both | Both | Both, but in v1.1 |
| Custom fields | All record types | All record types | All + layouts | All | Native (record = fields) | All + page layouts | JSON-column per entity |
| Workflow automation | **Workflows (visual)** | Automations | Blueprint + Workflows | Sequences (email/call) | Limited | Process Builder/Flow | In-app visual builder, queue-driven |
| Webhooks | Yes | Yes | Yes | Yes | Yes | Yes | Yes — share infra with workflows |
| Reporting | **Best-in-class** | Good | Very good | Good (sales-focused) | Limited | Good | Curated reports; defer custom builder |
| 2FA/SSO | Yes | Yes | Yes | Yes | Yes | Yes | TOTP v1.0; SSO v1.1 |
| Audit log | Enterprise tier | Enterprise tier | Yes | Yes | Yes | Yes | v1.0/v1.1 boundary |
| Public API | Excellent | Excellent | Excellent | Excellent | Excellent | Excellent | REST + webhooks |
| Lead forms | Yes (built-in) | Yes (LeadBooster) | Yes | Add-on | No | Web-to-lead | JS embed widget |
| AI assist | Breeze (mature) | AI Assistant | Zia | Native suggestions | Limited | Einstein | OpenAI/Anthropic; channel-aware (D8) |
| WhatsApp native | Plugin | Plugin | Plugin | Plugin | No | Plugin | **Native (Khuma differentiator)** |
| Multi-currency | Yes | Yes | Yes | Yes | Yes | Yes | Per-deal currency |
| Quotes | Yes (CPQ tier) | Yes | Yes | No | No | Yes | Defer to v2 |
| Customer portal | Service Hub | No | Yes | No | No | Yes (Experience Cloud) | Anti-feature for v1 |

---

## Sources

- HubSpot product documentation (Sales Hub, Workflows, Breeze AI) — vendor knowledge base, 2024–2026 feature evolution
- Pipedrive feature documentation (Pipelines, Automations, LeadBooster) — vendor knowledge base
- Zoho CRM documentation (Blueprint, Zia, Layouts) — vendor knowledge base
- Close.io documentation (Sequences, Power Dialer) — vendor knowledge base
- Attio documentation (Records model, Lists) — vendor knowledge base
- Salesforce Essentials/Starter feature scope — Salesforce knowledge base
- Khuma `.planning/PROJECT.md` and `.planning/codebase/ARCHITECTURE.md` — current state grounding
- GDPR Articles 15 (right of access), 17 (right to erasure), 20 (data portability) — compliance requirements
- Meta WhatsApp Business Platform — 24h customer-care window rules

**Confidence:** HIGH for category placement (table stakes vs differentiator vs anti-feature) — CRM ecosystem is mature and these boundaries are well-understood across all 6 reference products. MEDIUM for exact complexity estimates — depend on Khuma's specific multi-tenancy refactor decisions still pending.

---
*Feature research for: CRM SaaS — Khuma v1.0 completion*
*Researched: 2026-04-28*
