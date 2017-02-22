<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1101 extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE articles_refnm SET `type` = 'consultation' WHERE `type` IS NULL");
    }
}
