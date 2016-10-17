<?php

use Phinx\Migration\AbstractMigration;

class Dbjr936 extends AbstractMigration
{
    public function up()
    {
        $this->execute("CREATE TABLE `input_relations` (`parent_id` int(10) unsigned NOT NULL,
`child_id` int(10) unsigned NOT NULL) ENGINE='InnoDB'");
        $this->execute("ALTER TABLE `input_relations` ADD PRIMARY KEY `pkey` (`parent_id`, `child_id`)");
        $this->execute("ALTER TABLE `input_relations`
ADD FOREIGN KEY (`parent_id`) REFERENCES `inpt` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->execute("ALTER TABLE `input_relations`
ADD FOREIGN KEY (`child_id`) REFERENCES `inpt` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE");

        $contributions = $this->query("SELECT tid, rel_tid FROM `inpt`");
        foreach ($contributions as $contribution) {
            if (!empty($contribution['rel_tid'])) {
                $origins = explode(',', $contribution['rel_tid']);
                $values = [];
                foreach ($origins as $originId) {
                    $values[] = sprintf('(%d, %d)', $originId, $contribution['tid']);
                }
                $this->execute(
                    sprintf("INSERT INTO `input_relations` (`parent_id`, `child_id`) VALUES%s;", implode(',', $values))
                );
            }
        }
        $this->execute("ALTER TABLE `inpt` DROP `rel_tid`");
    }
}
