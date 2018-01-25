<?php

use Phinx\Migration\AbstractMigration;

class Dbjr728 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `vt_rights`
ADD UNIQUE `vt_rights_vt_code_unique` (`vt_code`);
EOD
        );
    }
}
