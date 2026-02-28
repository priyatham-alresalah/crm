-- Calling scripts for Al Resalah Consultancies and Training LLC (training provider)
-- Stage enum: intro, follow_up, objection_handling, closing. Run 007_calling_scripts_stage_enum.sql first if needed.

insert into public.calling_scripts (stage, title, content) values
('intro', 'Opening', 'Hello, this is [your name] from Al Resalah Consultancies and Training. We provide accredited training programs in HSE, First Aid, project management, and vocational skills across Abu Dhabi and Dubai. I am calling to see if our training solutions could support your team or compliance needs.'),
('intro', 'Brief pitch', 'We are an ACTVET and KHDA accredited training provider, offering in-house and public courses—from Basic First Aid and HSE to Agile and Advanced Excel. I wanted to check if that could be relevant for your organisation at the moment.'),
('follow_up', 'Follow-up call', 'Hi, this is [your name] from Al Resalah. I am following up on my previous email/call about our training programs. Do you have a few minutes to discuss your training or certification needs?'),
('follow_up', 'Callback', 'Hi, you asked me to call back today regarding our training offerings. Is this still a good time? I can briefly go through our calendar and in-house options.'),
('objection_handling', 'Not interested', 'I understand. May I send you our training calendar and a short overview by email so you have it on file for when the timing is right?'),
('objection_handling', 'Busy', 'No problem. When would be a better time for a short call? I can call back or send our e-learning and public schedule by email.'),
('objection_handling', 'Already have a provider', 'That makes sense. If you ever need a second option, additional accreditations, or in-house delivery, we are here. Feel free to reach out.'),
('closing', 'Next step', 'Great. I will send a short recap and our training calendar by email, and we can schedule the next step—whether that is a proposal, demo, or enrolment. Thank you.'),
('closing', 'Thank you', 'Thank you for your time. I will follow up by email as discussed. You can also reach us at info@aresalah.com or visit alresalahct.com for our latest programs.');
