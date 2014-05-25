<?php

class Dbjr_Db_Criteria
{
    /**
     * Holds the where conditions in format ['col1=?' => 'val1', 'col2=?' => 'val2']
     * @var array
     */
    public $where;

    /**
     * Holds the order by definition ['col1 ASC', 'col2 DESC']
     * @var array
     */
    public $order;

    /**
     * Holds the list of columns to select ['tbl1.col1', 'tbl2.col1']
     * @var array
     */
    public $columns;
}
