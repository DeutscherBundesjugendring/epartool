<?php

class Service_Cron_Mail extends Service_Cron
{
    /**
     * Sends all unsent emails
     */
    public function execute()
    {
        if (!(new Service_Email())->sendQueued()) {
            throw new Exception('There was an error when sending the emails');
        }
    }
}
