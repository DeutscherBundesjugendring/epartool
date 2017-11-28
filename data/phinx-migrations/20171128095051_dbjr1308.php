<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1308 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `inpt` CHANGE `confirmation_key` `confirmation_key` varchar(255) NULL AFTER `notiz`;
EOD
        );

        $this->execute(<<<EOD
UPDATE `inpt` SET `confirmation_key` = NULL WHERE `confirmation_key` = '';
EOD
        );
    }
}
