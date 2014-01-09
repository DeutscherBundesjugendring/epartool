<?php
class Model_User_Info extends Model_DbjrBase
{
    protected $_name = 'user_info';
    protected $_primary = 'user_info_id';

    protected $_referenceMap = array(
            'Users' => array(
                    'columns' => 'uid',
                    'refTableClass' => 'Model_Users',
                    'refColumns' => 'uid'
            ),
            'Consultations' => array(
                    'columns' => 'kid',
                    'refTableClass' => 'Model_Consultations',
                    'refColumns' => 'kid'
            )
    );

    /**
     * Returns latest user info entry by user and consultation
     *
     * @param  integer       $uid
     * @param  integer       $kid
     * @return NULL|Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
    public function getLatestByUserAndConsultation($uid, $kid)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($uid)) {
            return null;
        }
        if (!$validator->isValid($kid)) {
            return null;
        }

        $subselect = $this->select();
        $subselect->from($this, array(new Zend_Db_Expr('MAX(user_info_id)')));
        $subselect->where('uid=?', $uid)->where('kid=?', $kid);

        $select = $this->select();
        $select->where('user_info_id=(?)', $subselect);

        $row = $this->fetchRow($select);

        return $row;
    }
}
