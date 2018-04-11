<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1415 extends AbstractMigration
{
    public function up()
    {
        $htmlBody = $this->fetchAll(<<<EOD
SELECT `id`, `body_html` FROM `email_template` WHERE `name` = 'password_reset'
EOD
        );

        foreach ($htmlBody as $item) {
            if (!preg_match('~<a[^>]+href="\{\{password_reset_url\}\}"[^>]*>.*</a>~', $item['body_html'])) {
                $this->execute(sprintf(<<<EOD
UPDATE `email_template` SET body_html = '%s' WHERE id = %d
EOD
                    ,
                    str_replace(
                        '{{password_reset_url}}',
                        '<a href="{{password_reset_url}}">{{password_reset_url}}</a>',
                        $item['body_html']
                    ),
                    $item['id']
                ));
            }
        }
    }
}
