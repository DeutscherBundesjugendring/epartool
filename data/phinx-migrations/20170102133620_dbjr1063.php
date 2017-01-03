<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1063 extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE `vt_indiv` SET `status` = 'c' WHERE `upd` <= '2016-06-29 08:00:00'");

        $oldVotes = $this->query(<<<EOD
SELECT vt_indiv.uid, vt_indiv.sub_uid, kid FROM `vt_indiv`
INNER JOIN `inpt` ON `vt_indiv`.`tid` = `inpt`.`tid`
INNER JOIN `quests` ON `quests`.`qi` = `inpt`.`qi`
WHERE `vt_indiv`.`upd` <= '2016-06-29 08:00:00'
EOD
        );

        if ($oldVotes) {
            foreach ($oldVotes as $oldVote) {
                $this->execute(sprintf(
                    "UPDATE `vt_grps` SET `member` = 'y' WHERE `uid` = %d AND `sub_uid` = '%s' AND `kid` = %d",
                    $oldVote['uid'],
                    $oldVote['sub_uid'],
                    $oldVote['kid']
                ));
            }
        }
    }
}
