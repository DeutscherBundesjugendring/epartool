<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1065 extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE `users` SET `age_group` = NULL");
        $this->execute(<<<EOD
ALTER TABLE `users`
CHANGE `age_group` `age_group_from` int(11) NULL AFTER `name_pers`,
ADD `age_group_to` int(11) NULL AFTER `age_group_from`;
EOD
        );
    }
}
