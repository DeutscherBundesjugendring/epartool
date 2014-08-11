# Version 2.2

## Environment
* Application newly requires PHP 5.4+, that is a change from previously required PHP 5.3+

## Users
* All passwords have to be manually reset by all users as the cryptographic backend has changed to a more secure algorithm

## Email
* Site now supports SMTP sending, if not configured system mail is default
* Emails are sent by cron. Make sure that the page ```/cron/execute/key/<secret_cron_key>``` is executed by cron
* All records of previous emails are deleted
* If so desired you can use SMTP to send mail, credentials are to be set ```project/configs/application.local.ini```

## Inputs
* There must be no consultation active when deploy is being performed. Active consultation is a consultation that requires user input. This happens in the following phases:
    * input
    * voting
