<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1199a extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `proj` DROP `title`;
EOD
        );

        $this->execute(<<<EOD
ALTER TABLE `proj`
CHANGE `titl_short` `title` text NOT NULL;
EOD
        );
    }
}
