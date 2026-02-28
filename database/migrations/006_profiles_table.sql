-- Create profiles table for user-editable profile data (Supabase recommended pattern)
-- auth.users stores credentials; public.profiles stores name, email, phone, etc.
-- Run in Supabase SQL Editor

create table if not exists public.profiles (
  id uuid primary key references auth.users (id) on delete cascade,
  full_name text,
  email text,
  phone text,
  avatar_url text,
  updated_at timestamptz default now()
);

-- Enable RLS
alter table public.profiles enable row level security;

-- Users can read their own profile
create policy "Users can read their own profile"
  on public.profiles
  for select
  using ( auth.uid() = id );

-- Users can insert their own profile (for first-time setup)
create policy "Users can insert their own profile"
  on public.profiles
  for insert
  with check ( auth.uid() = id );

-- Users can update their own profile
create policy "Users can update their own profile"
  on public.profiles
  for update
  using ( auth.uid() = id );
