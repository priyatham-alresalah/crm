# Database (Supabase)

Run migrations and seeders in **Supabase Dashboard → SQL Editor**.

For the **current schema** (UUIDs, `client_status`, `interaction_type`, `stage`, `users`, `client_contacts`, etc.) see **SCHEMA-CURRENT.md**. The app is aligned with that schema.

1. **Email templates (enum)**: If `email_templates.category` uses enum `email_category`, run **`migrations/003_email_category_enum_values.sql`** first (adds intro, follow_up, meeting_request, reminder, thank_you, closure).
2. **Seeders**: Run `seeders/002_professional_email_templates.sql` for professional email templates.
3. **RLS for client contacts**: If you get "new row violates row-level security policy for table client_contacts" when adding a client, run **`migrations/004_client_contacts_rls.sql`** to allow authenticated users to insert/update/delete contacts.
4. **Profiles table (My Profile)**: Run **`migrations/006_profiles_table.sql`** to create `public.profiles` for user-editable profile data (name, email, phone). This follows Supabase’s recommended pattern.

Then in **Authentication → Providers** enable Email and set up at least one user for login.

## Tables (current schema)

- `users` – id (FK auth.users), name, role, is_active
- `profiles` – id (FK auth.users), full_name, email, phone, avatar_url, updated_at (user-editable profile data)
- `clients` – id (uuid), client_name, address, email, phone, client_status, assigned_to, created_by
- `client_contacts` – id, client_id, contact_name, contact_email, contact_phone, designation, is_primary
- `interactions` – id, client_id, interaction_type, stage, subject, notes, interaction_date, created_by
- `email_templates` – id, name, category, subject, body, is_active (no content column)
- `calling_scripts` – id, title, stage, content, is_active

RLS is enabled; adjust policies for user / manager / admin access as needed.
