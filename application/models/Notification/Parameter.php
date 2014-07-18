<?php

class Model_Notification_Parameter extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'notification_parameter';
    protected $_primary = ['notification_id', 'name'];
}
