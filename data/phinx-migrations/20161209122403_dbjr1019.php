<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1019 extends AbstractMigration
{
    public function change()
    {
        $this->execute(<<<EOD
UPDATE help_text
SET name='help-text-admin-consultation-settings-groups'
WHERE name='help-text-admin-consultation-settings-group-clusters';
EOD
        );
    }
}
