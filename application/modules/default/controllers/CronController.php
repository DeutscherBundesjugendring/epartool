<?php

class CronController extends Zend_Controller_Action
{
    /**
     * Executes all cron actions
     */
    public function executeAction()
    {
        // Cron should be at leas partially protected from public
        $key = $this->getRequest()->getParam('key');
        if ($key === Zend_Registry::get('systemconfig')->cron->key) {
            (new Service_Cron_Mail())->execute();
            (new Service_Cron_Logrotate())->execute();
            echo 'Success!';
        } else {
            echo 'Error!';
        }
        die();
    }
}
