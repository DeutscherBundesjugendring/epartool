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

    /**
     * Adds a condition to where array
     * @param string             $condition The condition to be used
     * @param string|integer     $value     The value to be used in the condition
     * @return Dbjr_Db_Criteria             Provides fluent interface
     */
    public function addWhere($condition, $value)
    {
        if (!is_array($this->where)) {
            $this->where = [];
        }
        $this->where[$condition] = $value;

        return $this;
    }

    /**
     * Adds a condition to where array
     * @param string             $column The column to be added
     * @return Dbjr_Db_Criteria             Provides fluent interface
     */
    public function addColumns($column)
    {
        if (!is_array($this->where)) {
            $this->where = [];
        }
        $this->columns[] = $column;

        return $this;
    }
}
