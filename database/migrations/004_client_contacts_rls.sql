-- RLS policies for client_contacts (fixes "new row violates row-level security policy" on Add Client)
-- Run in Supabase SQL Editor if you get that error when creating a client with a primary contact.

-- Ensure RLS is enabled
alter table public.client_contacts enable row level security;

-- Allow authenticated users to read, insert, update, delete client_contacts
create policy "Allow authenticated select client_contacts"
  on public.client_contacts for select to authenticated using (true);

create policy "Allow authenticated insert client_contacts"
  on public.client_contacts for insert to authenticated with check (true);

create policy "Allow authenticated update client_contacts"
  on public.client_contacts for update to authenticated using (true);

create policy "Allow authenticated delete client_contacts"
  on public.client_contacts for delete to authenticated using (true);
