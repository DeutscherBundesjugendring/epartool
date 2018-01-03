<?php

use Phinx\Migration\AbstractMigration;

class FixCreateAdmin extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `users`
CHANGE
`is_receiving_consultation_results` `is_receiving_consultation_results` tinyint(1) NOT NULL DEFAULT 0 AFTER `regio_pax`;
EOD
        );
    }
}
