<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1070 extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE `articles` CHANGE `time_modified` `time_modified` timestamp NULL;');
        $this->execute('ALTER TABLE `quests` CHANGE `time_modified` `time_modified` timestamp NULL;');
        $this->execute('ALTER TABLE `user_info` CHANGE `date_added` `date_added` timestamp NULL;');
    }
}
