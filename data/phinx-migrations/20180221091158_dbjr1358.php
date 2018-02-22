<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1358 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
CREATE TABLE `voting_button_set` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `consultation_id` int(10) unsigned NOT NULL,
  `button_type` varchar(191) NOT NULL,
  `points` int NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `label` text NULL
) ENGINE='InnoDB';
EOD
        );

        $this->execute(<<<EOD
ALTER TABLE `voting_button_set`
ADD INDEX `voting_button_set_consultation_index` (`consultation_id`, `button_type`),
ADD UNIQUE `voting_button_set_unique_index` (`consultation_id`, `button_type`, `points`),
ADD FOREIGN KEY (`consultation_id`) REFERENCES `cnslt` (`kid`) ON DELETE CASCADE,
ADD FOREIGN KEY (`button_type`) REFERENCES `voting_buttons_type` (`buttons_type`) ON DELETE RESTRICT;
EOD
        );

        $oldVtSettings = $this->fetchAll('SELECT * FROM `vt_settings`');
        $columnsDoNotExists = false;
        foreach ($oldVtSettings as $oldVtSetting) {
            if (!isset($oldVtSetting['btn_numbers']) || !isset($oldVtSetting['btn_labels'])) {
                $columnsDoNotExists = true;

                break;
            }
            $buttons = $oldVtSetting['btn_numbers'] >= 2 ? $oldVtSetting['btn_numbers'] : 2;
            $labels = explode(',', $oldVtSetting['btn_labels']);
            for ($i = 0; $i < $buttons && $i < 6; $i++) {
                $this->insert('voting_button_set', [
                    'consultation_id' => $oldVtSetting['kid'],
                    'button_type' => in_array($oldVtSetting['button_type'], ['stars', 'hearts'])
                        ? $oldVtSetting['button_type']
                        : 'stars',
                    'points' => $i,
                    'enabled' => true,
                    'label' => $labels[$i] ?: null,
                ]);
            }
        }

        if (!$columnsDoNotExists) {
            $this->execute(<<<EOD
            ALTER TABLE `vt_settings`
            DROP `btn_numbers`,
            DROP `btn_labels`;
EOD
            );
        }
    }
}
