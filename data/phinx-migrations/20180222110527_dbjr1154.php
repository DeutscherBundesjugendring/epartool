<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1154 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `fowups`
DROP `embed`;
EOD
        );
    }
}
