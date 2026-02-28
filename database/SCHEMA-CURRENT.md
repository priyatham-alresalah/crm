# Current database schema (reference)

This file describes the schema the CRM application is aligned with. Table order and constraints are for context only; do not run this as a single script.

## Tables

- **users** – `id` (uuid, FK auth.users), `name`, `role` (enum user_role), `is_active`, `created_at`
- **clients** – `id` (uuid), `client_name`, `address`, `email`, `phone`, `client_status` (enum), `assigned_to` (FK users), `created_by` (FK users), `created_at`
- **client_contacts** – `id` (uuid), `client_id` (FK clients), `contact_name`, `contact_email`, `contact_phone`, `designation`, `is_primary`, `created_at`
- **interactions** – `id` (uuid), `client_id` (FK clients), `interaction_type` (enum), `stage` (enum), `subject`, `notes`, `interaction_date`, `created_by` (FK users), `created_at`
- **email_templates** – `id` (uuid), `name`, `category` (enum), `subject`, `body`, `is_active`, `created_at`
- **calling_scripts** – `id` (uuid), `title`, `stage` (enum), `content`, `is_active`, `created_at`

## App alignment

| Area | Notes |
|------|------|
| **Clients** | Uses `client_status`, `assigned_to`, `created_by`, `email`, `phone`. Status values in app: `new`, `contacted`, `converted`, `lost`. |
| **Client contacts** | Full CRUD; `client_id`, `contact_name`, `contact_email`, `contact_phone`, `designation`, `is_primary`. |
| **Interactions** | Uses `interaction_type` (call, email, meeting, whatsapp), `stage` (intro, followup, closing), `subject`, `notes`, `interaction_date`, `created_by`. |
| **Email templates** | No `content` column; uses `name`, `category`, `subject`, `body`, `is_active`. Categories in app: Intro, Follow-up, Meeting Request, Reminder, Thank You, Closure. If your `category` enum differs, adjust seeders or app constants. |
| **Calling scripts** | Uses `title`, `stage`, `content`, `is_active`. Ordered by `stage`, `title`. |
| **Users** | Auth reads `public.users` by `id`; session stores `user_id`, `user_name`, `user_role`. |

## Enums

Your schema uses USER-DEFINED types for `client_status`, `interaction_type`, `stage`, `user_role`, and `email_templates.category`. Ensure enum values match what the app sends (e.g. client_status: new/contacted/converted/lost; interaction_type: call/email/meeting/whatsapp; stage: intro/followup/closing or stage names for calling_scripts).
