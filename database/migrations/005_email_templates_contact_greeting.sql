-- Use contact name in email template greetings (run once in Supabase SQL Editor)
-- Replaces "Dear {{client_name}}," with "Dear {{contact_name}}," so the greeting uses the contact person.

update public.email_templates
set body = replace(body, 'Dear {{client_name}},', 'Dear {{contact_name}},')
where body like '%Dear {{client_name}},%';
