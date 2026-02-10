-- Sample data for CRM (run after migrations)
-- Optional: run in Supabase SQL Editor

-- Email templates (placeholders: {{client_name}}, {{your_name}})
insert into public.email_templates (name, content) values
('Intro', 'Hi {{client_name}},\n\nMy name is {{your_name}}. I wanted to reach out to introduce ourselves and see if we can help with your needs.\n\nBest regards,\n{{your_name}}'),
('Follow-up', 'Hi {{client_name}},\n\nFollowing up on my previous message. I would love to connect when you have a moment.\n\nThanks,\n{{your_name}}');

-- Calling scripts by stage
insert into public.calling_scripts (stage, stage_order, name, content) values
('Intro', 1, 'Opening', 'Hello, this is [your name] from [company]. I am calling to introduce ourselves and see if we can help with [topic].'),
('Follow-up', 1, 'Follow-up', 'Hi, I am following up on my previous email/call. Do you have a few minutes to discuss?'),
('Objection handling', 1, 'Not interested', 'I understand. Would it be okay if I send a short summary by email so you have it for later?'),
('Objection handling', 2, 'Busy', 'When would be a better time for a quick call? I can call back at your convenience.'),
('Closing', 1, 'Next step', 'Great. I will send a short recap and we can schedule the next step. Thank you.');
