<?php

class Model_InputRelations extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'input_relations';
    protected $_primary = array(
        'parent_id', 'child_id'
    );
    protected $_referenceMap = [
        'Origin' => [
            'columns' => 'parent_id',
            'refTableClass' => 'Model_Inputs',
            'refColumns' => 'tid',
            'onDelete' => self::CASCADE,
            'onUpdate' => self::CASCADE
        ],
        'Related' => [
            'columns' => 'child_id',
            'refTableClass' => 'Model_Inputs',
            'refColumns' => 'tid',
            'onDelete' => self::CASCADE,
            'onUpdate' => self::CASCADE,
        ],
    ];
}
