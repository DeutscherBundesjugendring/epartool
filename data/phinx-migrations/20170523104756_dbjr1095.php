<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1095 extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `fowups`DROP `hlvl`");
    }
}
