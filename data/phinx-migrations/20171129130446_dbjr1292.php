<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1292 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `inpt`
ADD `latitude` decimal(10,8) NULL,
ADD `longitude` decimal(11,8) NULL AFTER `latitude`;
EOD
        );
        $this->execute(<<<EOD
UPDATE `inpt` SET `latitude` = NULL, `longitude` = NULL;
EOD
        );
    }
}
