<?php

class Service_Cron_Mail extends Service_Cron
{
    /**
     * Sends all unsent emails
     */
    public function execute()
    {
        (new Service_Email())->sendQueued();
    }
}
