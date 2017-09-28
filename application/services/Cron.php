<?php

abstract class Service_Cron
{
    public static function executeAll()
    {
        (new Service_Cron_ContributionsConfirmationReminder())->execute();
        (new Service_Cron_ReminderConfirmVoting())->execute();
        (new Service_Cron_Mail())->execute();
        (new Service_Cron_CleanMailArchive())->execute();
        (new Service_Cron_Logrotate())->execute();
    }

    abstract public function execute();
}
