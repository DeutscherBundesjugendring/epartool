<?php

class Model_License extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'license';
    protected $_primary = ['number', 'locale'];

    public function getLicences($locale)
    {
        $select = $this->select();
        $select->where('locale = ?', $locale);

        return $this->fetchAll($select);
    }
}
