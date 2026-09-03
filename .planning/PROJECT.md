# Khuma

## What This Is

A multi-tenant CRM SaaS for small/mid-size businesses, built on Laravel 11 + Livewire 3. It already provides lead capture, a Kanban pipeline, WhatsApp messaging (Evolution API), per-tenant email (Mailcow), call logs, role-based access, and subscription billing (M-Pesa). v1.0 closes the gap between "lightweight lead tracker" and a complete CRM by adding deal forecasting, activities/calendar, custom fields, workflow automation, and analytics.

## Core Value

**A sales team can manage the full pipeline — leads → deals → activities → won revenue — in one tenant-isolated app, with WhatsApp/email built in and automation removing manual follow-up.**

If only one thing must work: closing the loop from a captured lead to a won deal with forecastable revenue and audit trail.

## Context

- **Stage:** Brownfield. ~20 Eloquent models, ~12 Livewire components, billing/messaging/email already in production-shape. See `.planning/codebase/` for the full state map.
- **Stack:** PHP 8.2 / Laravel 11, Livewire 3, Tailwind, Vite, SQLite/MySQL, Spatie Permission, Yajra DataTables. Background queue + scheduler. M-Pesa, Cloudflare DNS, Mailcow integrations live.
- **Multi-tenancy:** `company_id` scoping is manual today (no global scope) — flagged as a top concern.
- **Users:** SMB sales teams; Portuguese-speaking primary market (kanban strings already in PT). i18n is partially present, not formalized.
- **Constraints:** Must not break existing tenants. Migrations need to be additive. RBAC layer must remain Spatie-compatible. Server-rendered (Livewire) — no SPA pivot in scope.

## Current Milestone: v1.0 Complete CRM

**Goal:** Close the gaps that make Khuma a real CRM rather than a lead tracker.

**Target features (priority order):**
1. **Deals/Opportunities** — split from Leads; value, currency, stage history, probability, expected close, weighted forecast
2. **Tasks/Activities** — calls, meetings, follow-ups; reminders; Google/Outlook calendar 2-way sync
3. **Custom fields + data hygiene** — per-company custom fields on leads/deals/contacts; CSV import/export; tags; duplicate detection & merge
4. **Workflow automation** — trigger/condition/action engine; drip sequences; outbound webhooks
5. **Reporting & analytics** — sales funnel, win/loss, conversion, source ROI, rep leaderboard, MRR/ARR/churn for the SaaS billing side

**Secondary (in v1.0 if capacity):** 2FA/SSO, system-wide audit log, GDPR data export/erasure, public API tokens + docs, web lead-capture form widgets, customer portal, AI assist (summaries, next-best-action, lead enrichment), in-app notifications, formal i18n + multi-currency, attachments/documents on records, separate Contacts from Accounts, products catalog + quotes.

## Requirements

### Validated (existing — confirmed by codebase map)

- ✓ Multi-tenant company + user model with role enum + Spatie permissions — existing
- ✓ Lead capture, list view, Kanban pipeline, lead history observer, notes — existing
- ✓ Client and Company records — existing
- ✓ WhatsApp via per-tenant Instance (Evolution API), inbound + outbound Messages — existing
- ✓ Per-tenant email: Mailcow subdomain provisioning, TenantMailer SMTP, inbound polling — existing
- ✓ Email campaigns (outbound) with logs — existing
- ✓ Call log records — existing
- ✓ Ticket system (Livewire) — existing
- ✓ Subscription billing: Plans, Prices, Cycles, Invoices, Payments via M-Pesa; plan-feature middleware — existing
- ✓ Teams with per-team inbound email aliases — existing
- ✓ Auth scaffolded by Breeze; Sanctum for API — existing

### Active (v1.0 — to define in REQUIREMENTS.md after research)

- [ ] Deals/Opportunities domain (split from Leads)
- [ ] Tasks & Activities + Calendar sync
- [ ] Custom fields + CSV I/O + tags + dedupe
- [ ] Workflow automation engine
- [ ] Reporting & analytics dashboards
- [ ] (Secondary set above)

### Out of Scope

- SPA / mobile app rewrite — Livewire SSR stays
- Self-serve white-label / reseller portal — not required for v1
- Replacing M-Pesa with Stripe in v1 — additive only if time permits
- Replacing Mailcow architecture — keep per-tenant SMTP model

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Split Leads → Deals as first-class model | Leads-as-deals conflates capture with sales pipeline; forecasting needs a Deal entity | — Pending |
| Multi-tenant scoping must be hardened (global scope or trait) before adding new tenant-scoped tables | Manual `company_id` filtering in every query is a leak risk | — Pending |
| Workflow engine is in-app, not a separate service | Smaller surface, fits Laravel queues; Zapier covered via webhooks | — Pending |
| Keep server-rendered Livewire UI for new features | Consistency with existing surface area, no SPA migration tax | — Pending |
| Mass-assignment posture (`$guarded = []`) needs review during v1 | Security debt flagged in codebase map | — Pending |

## Evolution

This document evolves at phase transitions and milestone boundaries.

**After each phase transition** (via `/gsd-transition`):
1. Requirements invalidated? → Move to Out of Scope with reason
2. Requirements validated? → Move to Validated with phase reference
3. New requirements emerged? → Add to Active
4. Decisions to log? → Add to Key Decisions
5. "What This Is" still accurate? → Update if drifted

**After each milestone** (via `/gsd-complete-milestone`):
1. Full review of all sections
2. Core Value check — still the right priority?
3. Audit Out of Scope — reasons still valid?
4. Update Context with current state

---
*Last updated: 2026-04-28 after initialization*
