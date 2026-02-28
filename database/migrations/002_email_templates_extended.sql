-- Extend email_templates for Email Generator (subject, body, category, is_active)
-- Run in Supabase SQL Editor after 001_initial_schema.sql

alter table public.email_templates
  add column if not exists subject text,
  add column if not exists body text,
  add column if not exists category text,
  add column if not exists is_active boolean default true;

-- Backfill existing rows: use name as subject and content as body
update public.email_templates
set subject = coalesce(subject, name),
    body = coalesce(body, content),
    category = coalesce(category, 'Intro')
where subject is null or body is null or category is null;
