<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1199 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `proj`
ADD `motto` text NULL,
ADD `title` text NULL AFTER `motto`,
ADD `description` text NULL AFTER `title`,
ADD `contact_www` text NULL AFTER `description`,
ADD `contact_name` text NULL AFTER `contact_www`,
ADD `contact_email` text NULL AFTER `contact_name`;
EOD
        );

        $this->execute(<<<EOD
UPDATE `proj` SET 
title = CONCAT('Project ', `proj`.`proj`),
description = 'Project description',
contact_www = 'https://www.example.com',
contact_name = 'Contact person name',
contact_email = 'email@example.com';
EOD
        );

        $this->execute(<<<EOD
ALTER TABLE `proj`
CHANGE `title` `title` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `motto`,
CHANGE `description` `description` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `title`,
CHANGE `contact_www` `contact_www` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `description`,
CHANGE `contact_name` `contact_name` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `contact_www`,
CHANGE `contact_email` `contact_email` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `contact_name`;
EOD
        );

        $this->execute(<<<EOD
DROP TABLE `parameter`;
EOD
        );
    }
}
