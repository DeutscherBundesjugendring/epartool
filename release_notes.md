# Version 2.2

## Users
* All passwords have to be manually reset by all users as the cryptographic backend has changed to a more secure algorythm

## Email
* Site now supports SMTP sending, if not configured system mail is default
* Emails are sent by cron. Make sure that the page /cron/execute/key/<secret_cron_key> is visted by cron
