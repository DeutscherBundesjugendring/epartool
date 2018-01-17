<?php

use Phinx\Migration\AbstractMigration;

class FixCreateAdmin extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
UPDATE `users` SET `is_receiving_consultation_results` = 0 WHERE `is_receiving_consultation_results` IS NULL;         
EOD
        );
        $this->execute(<<<EOD
ALTER TABLE `users`
CHANGE
`is_receiving_consultation_results` `is_receiving_consultation_results` tinyint(1) NOT NULL DEFAULT 0 AFTER `regio_pax`;
EOD
        );
    }
}
