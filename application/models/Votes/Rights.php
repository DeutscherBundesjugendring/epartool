<?php
/**
 * Votes_Rights
 * @author    Jan Suchandt, Markus Hackel
 */
class Model_Votes_Rights extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'vt_rights';
    protected $_primary = array(
        'kid', 'uid'
    );

    protected $_referenceMap = array(
        'Users' => array(
            'columns' => 'uid', 'refTableClass' => 'Model_Users', 'refColumns' => 'uid'
        ),
        'Consultations' => array(
            'columns' => 'kid',
            'refTableClass' => 'Model_Consultations',
            'refColumns' => 'kid'
        ),
    );

    
    public function setInitialRightsForConfirmedUser($userId, $consultationId)
    {
        $row = $this->find($consultationId, $userId)->current();
        
        if (empty($row)) {
            return $this->createRow([
                'kid' => $consultationId,
                'uid' => $userId,
                'vt_weight' => 1,
                'vt_code' => $this->generateVotingCode(),
            ])->save();
        }
        
        return 0;
    }

    /**
     * Returns voting rights for all participants of given consultation
     * @param  integer                 $kid  The consultation identifier
     * @throws Zend_Validate_Exception
     * @return array
     */
    public function getByConsultation($kid)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }
        $db = $this->getDefaultAdapter();

        $subselect = $db
            ->select()
            ->from('user_info', array(new Zend_Db_Expr('MAX(user_info_id)')))
            ->where('uid=vr.uid')
            ->where('kid=?', $kid)
            ->where('time_user_confirmed IS NOT NULL');

        $select = $db->select();
        $select->from(array('vr' => $this->_name), array(
                'uid' => 'vr.uid',
                'vt_weight' => 'vr.vt_weight',
                'vt_code' => 'vr.vt_code',
            ))
            ->joinLeft(
                ['gs' => (new Model_GroupSize())->info(Model_GroupSize::NAME)],
                'gs.id = vr.grp_siz',
                ['grp_siz' => 'IFNULL(CONCAT(gs.`from`, " - ", gs.`to`), "")']
            )
            ->joinUsing(array('u' => 'users'), 'uid', array(
                'email' => 'u.email'
            ))
            ->joinLeft(array('ui' => 'user_info'), 'vr.uid = ui.uid', ['group_size'])
            ->joinLeft(
                ['gs2' => (new Model_GroupSize())->info(Model_GroupSize::NAME)],
                'gs2.id = ui.group_size',
                ['group_size_user' => 'IFNULL(CONCAT(gs2.`from`, " - ", gs2.`to`), "")']
            )
            ->where('vr.kid = ?', $kid)
            ->where('vr.uid > ?', 1)
            ->where('u.email != ?', '')
            ->where('(ui.user_info_id = (?) OR ui.user_info_id IS NULL)', $subselect)
            ->order('u.email ASC');
        $stmt = $db->query($select);

        return $stmt->fetchAll();
    }

    /**
     * Returns  all  voting rights entrys in DB
     * @param  integer   $kid  The consultation identifier
     * @throws Zend_Validate_Exception
     * @see Model_Votes getResultsValuesFromDB
     * @return Zend_Db_Table_Row             The vt_rights table row object
     */
    public function getWeightsByConsultation($kid)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }

        $db = $this->getDefaultAdapter();
        $select = $db->select();
        $select->from(array('vr' => $this->_name), array(
                'uid' => 'vr.uid',
                'vt_weight' => 'vr.vt_weight'
            ));
        $select->where('kid = ?', $kid)
                    ->where('uid > ?', 1);
        $result = $db->query($select);
        // uid = key weight = value
        return $result->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Generates and returns a voting code,
     * logically adopted from old system
     * @param  integer $length Defaults to 8
     * @return string
     */
    public function generateVotingCode($length = 8)
    {
        $password="";
        // define possible characters - any character in this string can be
        // picked for use in the password, so if you want to put vowels back in
        // or add special characters such as exclamation marks, this is where
        // you should do it
        $possible = "2346789abcdfghjkmnpqrtvwxyzABCDEFGHJKLMNPQRTVWXYZ";

        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = mb_strlen($possible);

        // check for length overflow and truncate if necessary
        if ($length > $maxlength) {
            $length = $maxlength;
        }

        // set up a counter for how many characters are in the password so far
        $i = 0;

        // add random characters to $password until $length is reached
        while ($i < $length) {
            // pick a random character from the possible ones
            $char = mb_substr($possible, mt_rand(0, $maxlength-1), 1);

            // have we already used this character in $password?
            if (!mb_strstr($password, $char)) {
                // no, so it's OK to add it onto the end of whatever we've already got...
                $password .= $char;
                // ... and increase the counter by one
                $i++;
            }
        }

        return $password;
    }


    /**
     * Return rights of a voting user by authcode
     * @param  string $code authentification-code
     * @return array
     */
    public function findByCode($code)
    {
        if (empty($code)) {
            return array();
        }

        $select = $this->select();
        $select->where('vt_code = ?', $code);

        $result = $this->fetchRow($select);
        if ($result) {
            return $result->toArray();
        } else {
            return array();
        }
    }

    /**
     * Return rights of a voting user by authcode with user data
     * @param  string $code authentification-code
     * @return array
     */
    public function findByCodewithUserData($code)
    {
        $db = $this->getDefaultAdapter();
        $select = $db
            ->select()
            ->from(['vtr' => $this->_name])
            ->join(
                ['u' => (new Model_Users())->info(Model_Users::NAME)],
                'u.uid = vtr.uid',
                ['email']
            )
            ->where('vtr.vt_code = ?', $code);

        $result = $db->query($select)->fetch();
        if (!is_array($result)) {
            return [];
        }
        
        return $result;
    }

    /**
     * Returns the counted and grouped voting weights by consultation
     * @param  integer                 $kid
     * @throws Zend_Validate_Exception
     * @return Zend_Db_Table_Rowset
     */
    public function getWeightCountsByConsultation($kid)
    {
        $db = $this->getDefaultAdapter();
        $select = $db
            ->select()
            ->from(['vtr' => $this->_name], [
                'vtr.vt_weight',
                new Zend_Db_Expr('COUNT(vtr.vt_weight) AS weight_count'),
                new Zend_Db_Expr('COUNT(p.uid) AS participating_count')
            ])
            ->joinLeft(
                ['p' => (new Model_Votes_Individual())->selectVotesGroupsByConsultation($kid)],
                'p.uid = vtr.uid',
                []
            )
            ->where('vtr.kid = ?', $kid)
            ->group('vtr.vt_weight');

        return $db->query($select)->fetchAll();
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function addPermission(array $data)
    {
        if ($this->createRow($data)->save() > 0) {
            $userInfo = (new Model_User_Info())->fetchRow([
                'uid = ' . $data['uid'],
                'kid =' . $data['uid'],
            ]);
            if ($userInfo === null) {
                $userModel = new Model_Users();
                $userData = $userModel->find($data['uid'])->current()->toArray();
                $userData['cmnt_ext'] = '';
                $userData['kid'] = $data['kid'];
                $userInfoId = $userModel->addConsultationData($userData);
                if (!$userInfoId) {
                    throw new \Exception('Adding user info failed');
                }
            }
            return;
        }
        throw new \Exception('Adding permission failed.');
    }
}
