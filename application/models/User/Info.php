<?php

class Model_User_Info extends Dbjr_Db_Table_Abstract
{
    const PARTICIPANT_TYPE_VOTER = 'voter';
    const PARTICIPANT_TYPE_NEWSLETTER_SUBSCRIBER = 'newsletter_subscriber';
    const PARTICIPANT_TYPE_FOLLOWUP_SUBSCRIBER = 'followup_subscriber';

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
     * Returns latest confirmed user info entry by user and consultation
     * @param  integer            $uid  The user identifier
     * @param  integer            $kid  The consultation identifier
     * @return Zend_Db_Table_Row
     */
    public function getLatestByUserAndConsultation($uid, $kid)
    {
        $select = $this
            ->select()
            ->where('uid=?', $uid)
            ->where('kid=?', $kid)
            ->where('time_user_confirmed IS NOT NULL')
            ->order('user_info_id DESC')
            ->limit(1);

        return $this->fetchRow($select);
    }
}
