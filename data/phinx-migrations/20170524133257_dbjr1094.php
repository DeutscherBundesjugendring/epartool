<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1094 extends AbstractMigration
{
    public function up()
    {
        $this->execute(
            <<<'EOD'
            ALTER TABLE `fowup_fls`
ADD `type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'general',
ADD FOREIGN KEY (`type`) REFERENCES `fowups_type` (`name`)
EOD
        );
    }
}
