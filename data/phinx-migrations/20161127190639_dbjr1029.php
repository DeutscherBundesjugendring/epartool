<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1029 extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `user_info` ADD `invitation_sent_date` datetime NULL DEFAULT NULL;");
    }
}
