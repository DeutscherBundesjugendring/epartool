<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1245 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
UPDATE help_text AS ht1
SET ht1.body = CONCAT(
    ht1.body,
    ' ',
    (
        SELECT ht2.body
        FROM (SELECT * FROM help_text) AS ht2
        WHERE ht2.name = 'help-text-admin-consultation-voting-invitations'
        AND ht2.project_code = ht1.project_code
    )
)
WHERE
    ht1.name = 'help-text-admin-consultation-voting-permissions';
    
EOD
        );
        $this->execute("DELETE FROM help_text WHERE name = 'help-text-admin-consultation-voting-invitations';");
    }
}
