-- Add missing interaction_stage enum values (run in Supabase SQL Editor before seeders/003_calling_scripts_seed.sql)
-- Run once. Adds objection_handling and closing so the calling_scripts seed can insert all four stages.

alter type interaction_stage add value if not exists 'objection_handling';
alter type interaction_stage add value if not exists 'closing';
