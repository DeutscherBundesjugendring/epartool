<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1318b extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `vt_grps` CHANGE `vt_inp_list` `vt_inp_list` text NULL COMMENT 'list of votable tids';
EOD
        );
        $this->execute(<<<EOD
ALTER TABLE `vt_grps` CHANGE `vt_rel_qid` `vt_rel_qid` text NULL COMMENT 'list of rel QIDs';
EOD
        );
        $this->execute(<<<EOD
ALTER TABLE `vt_grps` CHANGE `vt_tg_list` `vt_tg_list` text NULL
COMMENT 'list of all (still) available tags for this user';
EOD
        );
    }
}
