<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1153 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `inpt`
CHANGE `video_service` `video_service` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `input_discussion_contrib`,
CHANGE `video_id` `video_id` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `video_service`;
EOD
        );
    }
}
