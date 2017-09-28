<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1213 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<< 'EOD'
ALTER TABLE `email_recipient`
DROP FOREIGN KEY `email_recipient_email_id_ibfk`,
ADD FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
EOD
        );
        $this->execute(<<< 'EOD'
ALTER TABLE `email_attachment`
DROP FOREIGN KEY `email_attachment_email_id_ibfk`,
ADD FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
EOD
        );
    }
}
