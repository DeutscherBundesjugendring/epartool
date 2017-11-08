<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1245 extends AbstractMigration
{
    public function up()
    {
        $this->execute("DELETE FROM help_text WHERE name = 'help-text-admin-consultation-voting-invitations'");
    }
}
