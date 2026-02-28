# CRM – Client Follow-Up & Interaction Management

Lightweight PHP CRM using **Supabase** (PostgreSQL + Auth) and **AdminLTE 3** (Bootstrap 5). Compatible with XAMPP and cPanel.

## Tech stack

- **Backend**: PHP 8.x, Supabase REST API (cURL), sessions for JWT
- **Frontend**: AdminLTE 3, Bootstrap 5, jQuery, Font Awesome
- **Hosting**: Apache, cPanel-friendly

## Setup

1. **Clone/copy** the project (e.g. `htdocs/crm` or your cPanel `public_html` subfolder).

2. **Environment**
   - Copy `.env.example` to `.env`
   - Set `SUPABASE_URL` and `SUPABASE_ANON_KEY` from your [Supabase](https://supabase.com) project (Settings → API).
   - Set `BASE_URL`: for XAMPP use `/crm/public`; for cPanel use your app URL (e.g. `https://yourdomain.com/crm` if the document root is `crm/public`).

3. **Database**
   - In Supabase **SQL Editor**, run:
     - `database/migrations/001_initial_schema.sql`
     - `database/seeders/001_sample_data.sql` (optional sample templates and scripts)
   - Enable **Authentication → Email** and create at least one user.

4. **Web server**
   - **XAMPP**: Open `http://localhost/crm/public/` (or `http://localhost/crm/` if you use the root `index.php` that loads `public/index.php`).
   - **cPanel**: Point document root to `public/` or set `BASE_URL` to match your URL.

## Hosting on cPanel as a subdomain

To run the CRM on a subdomain (e.g. `https://crm.yourdomain.com`):

1. **Create the subdomain** in cPanel (Subdomains): e.g. `crm` → `crm.yourdomain.com`.

2. **Set the document root** for that subdomain to the **`public`** folder of this project:
   - In cPanel → Subdomains → Edit, set "Document Root" to the path that ends with `/public`, e.g. `crm/public` (so the subdomain serves only the contents of `public/`, not the whole repo).

3. **Upload the project** so that the subdomain’s document root is exactly the `public` directory (e.g. upload the full repo and point the subdomain to `crm/public`).

4. **Configure `.env`** (copy from `.env.example`):
   - `SUPABASE_URL` and `SUPABASE_ANON_KEY` as usual.
   - **`BASE_URL=/`** for subdomain (root of the subdomain; no path prefix).

5. **Apache**: The repo includes `public/.htaccess` so that all requests are routed to `index.php`. Ensure **mod_rewrite** is enabled (cPanel usually has it on).

6. **Optional**: In `public/.htaccess` you can uncomment the HTTPS redirect lines to force SSL on the subdomain.

## Architecture

- **Single front controller**: `public/index.php` (router + bootstrap).
- **No direct DB**: all data via Supabase REST API with JWT in session.
- **Views**: PHP only; no business logic in views. Controllers coordinate; `SupabaseClient` does API calls.

## Modules

1. **Dashboard** – total clients, interactions, follow-ups today, status summary  
2. **Clients** – list, add, view (with interaction timeline)  
3. **Interactions** – log email/call per client; timeline on client view  
4. **Email Generator** – templates with `{{client_name}}`, `{{your_name}}`; copy to clipboard  
5. **Calling Script** – stage-based scripts (Intro, Follow-up, Objection, Closing), read-only  
6. **Reports** – clients by status, follow-ups in date range, interactions per user (Manager/Admin)

## User roles (Supabase RLS)

- **user**: own clients, add interactions, update status  
- **manager**: team clients, reports  
- **admin**: full access, users, templates, scripts  

Adjust RLS policies in the migration SQL to match.

## Non-goals

- No React/Vue/Node
- No server-side email sending
- No WhatsApp/SMS or payments
