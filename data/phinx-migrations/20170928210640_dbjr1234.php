<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1234 extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `proj` ADD `teaser_enabled` int(1) NOT NULL DEFAULT '1';");
    }
}
