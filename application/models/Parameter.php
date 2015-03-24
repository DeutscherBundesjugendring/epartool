<?php

class Model_Parameter extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'parameter';
    protected $_primary = ['name', 'proj'];

    /**
     * Returns all params as array
     * @param  array $condArr An array limiting the results
     * @return array          An array of params in format [name => value]
     */
    public function getAsArray($condArr = null)
    {
        $params = [];
        $paramsRaw = $this->fetchAll($condArr);
        foreach ($paramsRaw as $param) {
            $params[$param->name] = $param->value;
        }

        return $params;
    }
}
