<?php

require_once (realpath(dirname(dirname(dirname(__FILE__)))) . '/application/services/Media.php');

use Phinx\Migration\AbstractMigration;

class Dbjr1060 extends AbstractMigration
{
    public function up()
    {
        $consultations = $this->query("SELECT kid, img_file FROM `cnslt`");
        foreach ($consultations as $consultation) {
            if (strpos($consultation['img_file'], '/') === false) {
                $this->execute("
                    UPDATE `cnslt` SET img_file = '" . Service_Media::MEDIA_DIR_CONSULTATIONS . "/"
                    . $consultation['kid'] . "/" . $consultation['img_file'] . "'
                    WHERE kid = " . $consultation['kid'] . "
                ");
            }
        }
    }
}
