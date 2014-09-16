<?php

class Model_Mail_Template_Type extends Dbjr_Db_Table_Abstract
{
    const TEMPLATE_TYPE_SYSTEM = 'system';
    const TEMPLATE_TYPE_CUSTOM = 'custom';

    protected $_name = 'email_template_type';
    protected $_primary = 'id';
    protected $_referenceMap = array(
        'Template' => array(
            'columns'           => 'id',
            'refTableClass'     => 'Model_Mail_Template',
            'refColumns'        => 'type_id'
        ),
    );
}
