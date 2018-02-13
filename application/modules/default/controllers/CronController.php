<?php

class CronController extends Zend_Controller_Action
{
    /**
     * Executes all cron actions
     */
    public function executeAction()
    {
        // Cron should be at least partially protected from public
        $key = $this->getRequest()->getParam('key');
        $cronConfig = Zend_Registry::get('systemconfig')->cron;
        if ($key && $cronConfig && $key === $cronConfig->key) {
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

    public function fallbackAction()
    {
        $cronConfig = Zend_Registry::get('systemconfig')->cron;
        if ($cronConfig && $cronConfig->key) {
            // @codingStandardsIgnoreLine
            die();
        }

        if (!$this->getRequest()->isPost()) {
            // @codingStandardsIgnoreLine
            die();
        }

        Service_Cron::waitForEnterCriticalSection();
        if (Service_Cron::verifyHash($this->getRequest()->getParam('h'))) {
            try {
                Service_Cron::executeAll();
            } catch (Exception $e) {
                Service_Cron::leaveCriticalSection();

                throw $e;
            }
        }
        Service_Cron::leaveCriticalSection();
        // @codingStandardsIgnoreLine
        die();
    }
}
