<?php

class Model_Mail_Recipient extends Dbjr_Db_Table_Abstract
{
    const TYPE_TO = 'to';
    const TYPE_CC = 'cc';
    const TYPE_BCC = 'bcc';

    protected $_name = 'email_recipient';
    protected $_primary = 'id';
    protected $_referenceMap = array(
        'Mail' => array(
            'columns'           => 'email_id',
            'refTableClass'     => 'Model_Mail',
            'refColumns'        => 'id'
        ),
    );
}
