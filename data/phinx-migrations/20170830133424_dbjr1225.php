<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1225 extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE `cnslt` ADD `anonymous_contribution` tinyint(1) NULL');
        $this->execute('UPDATE `cnslt` SET `anonymous_contribution` = 0');
        $this->execute(
            'ALTER TABLE `cnslt` CHANGE `anonymous_contribution` `anonymous_contribution` tinyint(1) NOT NULL'
        );
        $this->execute('ALTER TABLE `cnslt` ADD `anonymous_contribution_finish_info` text NULL');
    }
}
