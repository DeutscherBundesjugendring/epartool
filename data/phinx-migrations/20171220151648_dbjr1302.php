<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1302 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `vt_settings`
DROP `btn_numbers`,
DROP `btn_labels`;
EOD
        );

        $this->execute(<<<EOD
CREATE TABLE `voting_buttons_type` (
  `buttons_type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL
) ENGINE='InnoDB';
EOD
        );

        $this->execute(<<<EOD
ALTER TABLE `voting_buttons_type`
ADD PRIMARY KEY `buttons_type` (`buttons_type`);
EOD
        );

        $this->execute(<<<EOD
INSERT INTO `voting_buttons_type` (`buttons_type`)
VALUES ('stars'), ('hearts'), ('yesno');
EOD
        );

        $this->execute(<<<EOD
ALTER TABLE `vt_settings`
ADD `button_type` varchar(191) NOT NULL DEFAULT 'stars';
EOD
        );
        $this->execute(<<<EOD
ALTER TABLE `vt_settings`
ADD FOREIGN KEY (`button_type`) REFERENCES `voting_buttons_type` (`buttons_type`) ON DELETE RESTRICT;
EOD
        );
    }
}
