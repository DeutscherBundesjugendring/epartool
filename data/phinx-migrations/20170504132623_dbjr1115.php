<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1115 extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE `articles` CHANGE `sidebar` `sidebar` text NULL COMMENT \'Content for sidebar\';');
        $this->execute('ALTER TABLE `cnslt` CHANGE `contribution_confirmation_info` `contribution_confirmation_info` text NULL COMMENT \'\';');
        $this->execute('ALTER TABLE `cnslt` CHANGE `vot_expl` `vot_expl` text NULL COMMENT \'info text for voting start\';');
        $this->execute('ALTER TABLE `email` CHANGE `body_html` `body_html` text NOT NULL COMMENT \'\';');
        $this->execute('ALTER TABLE `email` CHANGE `body_text` `body_text` text NOT NULL COMMENT \'\';');
    }
}
