<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1065 extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `users` CHANGE `age_group` `age_group` text NULL AFTER `name_pers`");
        $this->execute("UPDATE `users` SET `age_group` = NULL WHERE `age_group` = 5");
        $this->execute(<<<EOD
ALTER TABLE `users`
CHANGE `age_group` `age_group` int(11) NULL AFTER `name_pers`,
ADD FOREIGN KEY (`age_group`) REFERENCES `contributor_age` (`id`) ON DELETE RESTRICT
EOD
        );
    }
}
