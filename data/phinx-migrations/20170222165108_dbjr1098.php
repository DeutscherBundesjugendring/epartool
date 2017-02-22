<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1098 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<'EOD'
ALTER TABLE `cnslt`
CHANGE inp_fr inp_fr datetime NULL COMMENT 'Input possible from date on',
CHANGE inp_to inp_to datetime NULL COMMENT 'Input possible till',
CHANGE spprt_fr spprt_fr datetime NULL COMMENT 'support button clickable from',
CHANGE spprt_to spprt_to datetime NULL COMMENT 'Supporting possible until',
CHANGE vot_fr vot_fr datetime NULL COMMENT 'Voting possible from date on',
CHANGE vot_to vot_to datetime NULL COMMENT 'Voting possible till';
EOD
);
        $this->execute("UPDATE cnslt SET inp_fr = NULL WHERE inp_fr = '0000-00-00 00:00:00'");
        $this->execute("UPDATE cnslt SET inp_to = NULL WHERE inp_to = '0000-00-00 00:00:00'");
        $this->execute("UPDATE cnslt SET spprt_fr = NULL WHERE spprt_fr = '0000-00-00 00:00:00'");
        $this->execute("UPDATE cnslt SET spprt_to = NULL WHERE spprt_to = '0000-00-00 00:00:00'");
        $this->execute("UPDATE cnslt SET vot_fr = NULL WHERE vot_fr = '0000-00-00 00:00:00'");
        $this->execute("UPDATE cnslt SET vot_to = NULL WHERE vot_to = '0000-00-00 00:00:00'");
    }
}
