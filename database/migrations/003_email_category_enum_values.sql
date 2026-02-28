-- Add required values to email_category enum (run once before seeding email templates)
-- Run this in Supabase SQL Editor before 002_professional_email_templates.sql

alter type public.email_category add value if not exists 'intro';
alter type public.email_category add value if not exists 'follow_up';
alter type public.email_category add value if not exists 'meeting_request';
alter type public.email_category add value if not exists 'reminder';
alter type public.email_category add value if not exists 'thank_you';
alter type public.email_category add value if not exists 'closure';
