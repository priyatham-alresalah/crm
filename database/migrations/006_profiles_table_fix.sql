-- Create public.profiles (run this entire script in Supabase SQL Editor once).
-- Uses timestamptz (correct type; "timestz" is invalid and causes errors).

drop table if exists public.profiles;

create table public.profiles (
  id uuid primary key references auth.users (id) on delete cascade,
  full_name text,
  email text,
  phone text,
  avatar_url text,
  updated_at timestamptz default now()
);

alter table public.profiles enable row level security;

create policy "Users can read their own profile"
  on public.profiles for select using ( auth.uid() = id );

create policy "Users can insert their own profile"
  on public.profiles for insert with check ( auth.uid() = id );

create policy "Users can update their own profile"
  on public.profiles for update using ( auth.uid() = id );
