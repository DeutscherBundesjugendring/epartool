<?php

class Service_Cron_CleanMailArchive extends Service_Cron
{
    public function execute()
    {
        (new Model_Mail())->delete([
            'time_sent IS NOT NULL AND time_sent < ?' => (new \DateTime())->sub(
                new \DateInterval(Zend_Registry::get('systemconfig')->archive_sent_emails_interval)
            )->format('Y-m-d H:i:s')
        ]);
    }
}
