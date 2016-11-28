<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1020 extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
            (
                SELECT
                    'help-text-admin-consultation-settings-general',
                    'Sample consultation settings general page text.',
                    `proj`.`proj`,
                    'admin'
                FROM
                    proj
            );
        ");

        $this->execute("
            INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
            (
                SELECT
                    'help-text-admin-consultation-settings-participants-data',
                    'Sample consultation settings participants data page text.',
                    `proj`.`proj`,
                    'admin'
                FROM
                    proj
            );
        ");

        $this->execute("
            INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
            (
                SELECT
                    'help-text-admin-consultation-settings-voting',
                    'Sample consultation settings voting page text.',
                    `proj`.`proj`,
                    'admin'
                FROM
                    proj
            );
        ");

        $this->execute("
            INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
            (
                SELECT
                    'help-text-admin-consultation-settings-phases',
                    'Sample consultation settings phases page text.',
                    `proj`.`proj`,
                    'admin'
                FROM
                    proj
            );
        ");

        $this->execute("
            INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
            (
                SELECT
                    'help-text-admin-consultation-settings-group-clusters',
                    'Sample consultation settings group clusters page text.',
                    `proj`.`proj`,
                    'admin'
                FROM
                    proj
            );
        ");
    }
}
