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
        if ($key && $key === Zend_Registry::get('systemconfig')->cron->key) {
            Service_Cron::executeAll();
            // @codingStandardsIgnoreLine
            echo 'Success!';
        } else {
            // @codingStandardsIgnoreLine
            echo 'Error!';
        }
        // @codingStandardsIgnoreLine
        die();
    }
}
