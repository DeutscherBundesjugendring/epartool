DELETE FROM email_template_has_email_placeholder
WHERE email_template_id = (SELECT id FROM email_template WHERE name = 'voting_confirmation_single')
AND email_placeholder_id = (SELECT id FROM email_placeholder WHERE name = 'rejection_url');
