<?php

class Service_Cron_Logrotate extends Service_Cron
{
    /**
     * Rotates log files if necessary
     * It assumes that log files have no dots in their names
     */
    public function execute()
    {
        $logPath = RUNTIME_PATH . '/logs';
        $files = scandir($logPath);

        foreach ($files as $file) {
            $pathInfo = pathinfo($logPath . '/' . $file);
            $maxSize = Zend_Registry::get('systemconfig')->log->file->maxSize;
            if (isset($pathInfo['extension'])
                && $pathInfo['extension'] === 'log'
                && !strpos($pathInfo['filename'], '.')
                && filesize($pathInfo['dirname'] . '/' . $pathInfo['basename']) > $maxSize
            ) {
                rename(
                    $logPath . '/' . $file,
                    $logPath . '/' . $pathInfo['filename'] . '.' . date("Y-m-d_H-i-s") . '.' . $pathInfo['extension']
                );
                touch($logPath . '/' . $file);
            }
        }


    }
}
