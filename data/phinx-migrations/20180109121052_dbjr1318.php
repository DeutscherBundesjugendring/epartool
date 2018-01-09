<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1318 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `quests`
ADD `location_enabled` tinyint(1) NOT NULL DEFAULT '0';
EOD
        );
    }
}
