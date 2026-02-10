# Database (Supabase)

Run migrations and seeders in **Supabase Dashboard → SQL Editor**.

1. **Migrations**: Run `migrations/001_initial_schema.sql` first.
2. **Seeders**: Run `seeders/001_sample_data.sql` for sample email templates and calling scripts.

Then in **Authentication → Providers** enable Email and set up at least one user for login.

## Tables

- `clients` – client name, address, email, status, optional `user_id`
- `interactions` – client_id, type (email/call), notes, status_at_time, created_by
- `email_templates` – name, content (use `{{client_name}}`, `{{your_name}}`)
- `calling_scripts` – stage, stage_order, name, content

RLS is enabled; adjust policies for user / manager / admin access as needed.
