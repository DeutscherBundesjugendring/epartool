<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1072 extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `cnslt` CHANGE `img_file` `img_file` text NULL AFTER `titl_sub`");
    }
}
