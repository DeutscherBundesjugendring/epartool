<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1028 extends AbstractMigration
{
    public function up()
    {
        $this->execute(
            "ALTER TABLE `vt_settings` ADD `btn_no_opinion` boolean NOT NULL DEFAULT true AFTER `btn_important`;"
        );
    }
}
