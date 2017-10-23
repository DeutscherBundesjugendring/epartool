<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1263 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `cnslt`
CHANGE `anonymous_contribution` `anonymous_contribution` tinyint(1) NOT NULL DEFAULT '0' AFTER `groups_no_information`;
EOD
        );
    }
}
