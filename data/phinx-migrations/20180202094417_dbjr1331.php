<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1331 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `quests`
ADD `geo_fence_enabled` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `quests`
ADD `geo_fence_polygon` text COLLATE 'utf8mb4_general_ci' NULL;
EOD
        );
    }
}
