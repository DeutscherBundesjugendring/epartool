<?php

abstract class Service_Cron
{
    public static function executeAll()
    {
        (new Service_Cron_Mail())->execute();
    }

    abstract public function execute();
}
