<?php

class Model_UrlkeyAction_Parameter extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'urlkey_action_parameter';
    protected $_primary = ['urlkey_action_id', 'name'];
}
