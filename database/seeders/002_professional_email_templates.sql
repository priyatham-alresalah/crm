-- Professional email templates for Email Generator
-- For schema with enum email_category. Uses enum values: intro, follow_up, meeting_request, reminder, thank_you, closure.
-- Placeholders: {{client_name}}, {{contact_name}}, {{your_name}}, {{company_name}}, {{today_date}}

insert into public.email_templates (name, subject, body, category, is_active) values

('Initial introduction', 'Introduction – {{company_name}} | How we can support you',
 'Dear {{contact_name}},

My name is {{your_name}} from {{company_name}}. I am reaching out to introduce our consultancy and training services and to see how we might support you and your team.

We work with organisations on [brief area, e.g. leadership development, process improvement, or custom training]. I would welcome the chance to learn about your priorities and explore whether there is a fit.

Would you have 15 minutes for a short call in the coming days?

Kind regards,
{{your_name}}', 'intro', true),

('Introduction with services', '{{company_name}} – Consultancy & Training | Quick overview',
 'Dear {{contact_name}},

I hope this email finds you well. I am {{your_name}} from {{company_name}}. We specialise in consultancy and training solutions tailored to our clients’ goals.

We typically support in:
• Strategy and organisational development
• Leadership and management development
• Custom training programmes and workshops

I would be glad to send a short one-page overview and, if useful, arrange a brief call to discuss your objectives.

Best regards,
{{your_name}}', 'intro', true),

('First follow-up', 'Re: Introduction – {{company_name}}',
 'Dear {{contact_name}},

I wanted to follow up on my previous message. I appreciate how busy things get, so I am happy to work around your schedule.

If now is not a good time, no problem – I can reach out again in a few weeks. If you would prefer not to receive further emails, please say so and I will update my records.

Kind regards,
{{your_name}}', 'follow_up', true),

('Second follow-up', 'Quick follow-up – {{company_name}}',
 'Dear {{contact_name}},

I am following up once more in case my earlier emails were missed or pushed down the list. I would still value the chance to connect and see if {{company_name}} could support you.

A short call or a reply with a convenient time would be ideal. If your priorities have changed, a quick "not for now" is enough for me to step back.

Thank you,
{{your_name}}', 'follow_up', true),

('Final follow-up', 'Last note from {{your_name}} – {{company_name}}',
 'Dear {{contact_name}},

This will be my last follow-up so as not to take more of your time. If you would like to explore how {{company_name}} can support your organisation, please reply or book a slot here: [link if applicable]. Otherwise I will assume the timing is not right and will not contact you again unless you reach out.

I wish you every success.

Best regards,
{{your_name}}', 'follow_up', true),

('Request a meeting', 'Meeting request – {{company_name}}',
 'Dear {{contact_name}},

Thank you for your interest in {{company_name}}. I would like to suggest a short meeting (about 20–30 minutes) to:

• Understand your current priorities and challenges
• Outline how we might support you
• Agree next steps, if any

Would any of the following work for you? [Suggest 2–3 options or link to calendar.] If none suit, please suggest a time that does.

I look forward to hearing from you.

Best regards,
{{your_name}}', 'meeting_request', true),

('Confirm meeting', 'Confirmed: Meeting on [date] – {{company_name}}',
 'Dear {{contact_name}},

This email confirms our meeting on [date] at [time] [and location/video link if applicable].

I will prepare a short overview of {{company_name}} and some questions to make the best use of our time. If you have any materials or points you would like me to review in advance, please send them before we meet.

See you then.

Best regards,
{{your_name}}', 'meeting_request', true),

('Gentle reminder', 'Gentle reminder – {{company_name}}',
 'Dear {{contact_name}},

I am sending a gentle reminder about [e.g. our upcoming call / the proposal we sent / the next step we discussed]. I know how full inboxes can get.

Please let me know if you need to reschedule or if you have any questions.

Kind regards,
{{your_name}}', 'reminder', true),

('Pre-meeting reminder', 'Reminder: Our meeting on [date] – {{company_name}}',
 'Dear {{contact_name}},

This is a quick reminder that we have a meeting scheduled for [date] at [time] [and location/link].

Please let me know if you need to change the time or if you have any questions before we meet.

Best regards,
{{your_name}}', 'reminder', true),

('Thank you after meeting', 'Thank you – {{company_name}}',
 'Dear {{contact_name}},

Thank you for taking the time to meet with me today. I enjoyed learning about [specific topic discussed] and the opportunity to explore how {{company_name}} might support you.

As agreed, I will [e.g. send the proposal by Friday / follow up with the details we discussed]. Please do not hesitate to reach out if you have any questions in the meantime.

Kind regards,
{{your_name}}', 'thank_you', true),

('Thank you after training', 'Thank you for participating – {{company_name}}',
 'Dear {{contact_name}},

Thank you for participating in [training/workshop name] on [date]. We hope it was useful for you and your team.

If you have any feedback or would like to discuss follow-up sessions or further support, please get in touch. We would be glad to stay in touch and share relevant resources.

Best regards,
{{your_name}}', 'thank_you', true),

('Closing – no response', 'Closing our thread – {{company_name}}',
 'Dear {{contact_name}},

I have reached out a few times without hearing back, so I will assume that the timing is not right for now. I will close our thread on my side and will not contact you again unless you get in touch.

If your situation changes and you would like to explore how {{company_name}} can support you, please feel free to reply to this email or contact us directly.

Best wishes,
{{your_name}}', 'closure', true),

('Closing – not a fit', 'Thank you – {{company_name}}',
 'Dear {{contact_name}},

Thank you for letting me know that [e.g. you are not moving forward / the timing is not right]. I appreciate you taking the time to reply.

If your priorities change in the future, we would be happy to hear from you. I wish you and your team every success.

Kind regards,
{{your_name}}', 'closure', true);
