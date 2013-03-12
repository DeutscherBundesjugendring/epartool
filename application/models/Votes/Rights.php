<?php
/**
 * Votes_Rights
 * @author  Jan Suchandt, Markus Hackel
 */
class Model_Votes_Rights extends Zend_Db_Table_Abstract {
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
  
  /**
   * Sets initial voting rights for all participants of given consultation
   * (if not already done)
   *
   * @param integer $kid
   * @throws Zend_Validate_Exception
   * @return integer Number of newly inserted rows
   */
  public function setInitialRightsByConsultation($kid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    $count = 0;
    $userModel = new Model_Users();
    $participants = $userModel->getParticipantsByConsultation($kid);
    foreach ($participants as $user) {
      if ($user['uid'] != 1) {
        $row = $this->find($kid, $user['uid'])->current();
        if (empty($row)) {
          $code = $this->generateVotingCode();
          $data = array(
            'kid' => $kid,
            'uid' => $user['uid'],
            'vt_weight' => 1,
            'vt_code' => $code,
          );
          $newRow = $this->createRow($data);
          $newRow->save();
          $count++;
        }
      }
    }
    
    return $count;
  }
  
  /**
   * Returns voting rights for all participants of given consultation
   *
   * @param integer $kid
   * @throws Zend_Validate_Exception
   * @return array
   */
  public function getByConsultation($kid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    $db = $this->getDefaultAdapter();
    $select = $db->select();
    $select->from(array('vr' => $this->_name), array(
        'uid' => 'vr.uid',
        'vt_weight' => 'vr.vt_weight',
        'vt_code' => 'vr.vt_code',
        'grp_siz' => 'vr.grp_siz',
      ))
      ->joinUsing(array('u' => 'users'), 'uid', array(
        'email' => 'u.email',
        'group_size_user' => 'u.group_size',
      ))
      ->where('vr.kid = ?', $kid)
      ->where('vr.uid > ?', 1)
      ->where('u.email != ?', '')
      ->order('u.email ASC');
    $stmt = $db->query($select);
    return $stmt->fetchAll();
  }
  
  /**
   * Returns voting rights for a given user and consultation
   *
   * @param integer $uid
   * @param integer $kid
   * @throws Zend_Validate_Exception
   * @return Zend_Db_Table_Row
   */
  public function getByUserAndConsultation($uid, $kid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($uid)) {
      throw new Zend_Validate_Exception('Given parameter uid must be integer!');
    }
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    
    return $this->find($kid, $uid)->current();
  }
  
  /**
   * Generates and returns a voting code,
   * logically adopted from old system
   *
   * @param integer $length Defaults to 8
   * @return string
   */
  protected function generateVotingCode($length = 8) {
    $password="";
    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789abcdfghjkmnpqrtvwxyzABCDEFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);
  
    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
      $length = $maxlength;
    }
	
    // set up a counter for how many characters are in the password so far
    $i = 0;
    
    // add random characters to $password until $length is reached
    while ($i < $length) {
      // pick a random character from the possible ones
      $char = substr($possible, mt_rand(0, $maxlength-1), 1);
        
      // have we already used this character in $password?
      if (!strstr($password, $char)) {
        // no, so it's OK to add it onto the end of whatever we've already got...
        $password .= $char;
        // ... and increase the counter by one
        $i++;
      }
    }
    
    return $password;
  }
}
