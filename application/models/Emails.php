<?php
/**
 * Users
 * @desc    Class of emails
 * @author  Jan Suchandt
 */
class Model_Emails extends Model_DbjrBase {
  protected $_name = 'ml_sent';
  protected $_primary = 'id';
  
  protected $_flashMessenger = null;
  
  protected $_auth = null;
  
  public function init() {
    $this->_auth = Zend_Auth::getInstance();
    $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
  }
  
  /**
   * getById
   * @desc returns entry by id
   * @name getById
   * @param integer $id
   * @return array
   */
  public function getById($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return array();
    }

    $row = $this->find($id)->current();
    return $row;
  }

  /**
   * add
   * @desc add new entry to db-table
   * @name add
   * @param array $data
   * @return integer primary key of inserted entry
   */
  public function add($data) {
    $row = $this->createRow($data);

    return $row->save();
  }

  /**
   * updateById
   * @desc update entry by id
   * @name updateById
   * @param integer $id
   * @param array $data
   * @return integer
   */
  public function updateById($id, $data) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return 0;
    }
    // exists?
    if ($this->find($id)->count() < 1) {
      return 0;
    }

    $where = $this->getDefaultAdapter()
        ->quoteInto($this->_primary[1] . '=?', $id);
    return $this->update($data, $where);
  }

  /**
   * deleteById
   * @desc delete entry by id
   * @name deleteById
   * @param integer $id
   * @return integer
   */
  public function deleteById($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return 0;
    }
    // exists?
    if (!$this->exists($id)) {
      return 0;
    }

    // where
    $where = $this->getDefaultAdapter()
        ->quoteInto($this->_primary[1] . '=?', $id);
    $result = $this->delete($where);
    return $result;
  }

  /**
   * exists
   * @desc check if a entitie exists
   * @param integer $id user-id
   * @return boolean
   */
  public function exists($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return false;
    }
    // exists?
    if ($this->find($id)->count() < 1) {
      return false;
    }
    else {
      return true;
    }
  }

  
  /**
   * Return all users
   */
  public function getAll() {
    return $this->fetchAll($this->select()->order('when DESC'));
  }
  
  /**
   * send a email and put into database
   * @param string $receiver email address
   * @param string $subject
   * @param string $message
   * @param string $templateReference Name of template (see DB-Table "mail_def" col "refnm")
   * @param array $templateReplace array({{MARKER}} => <VALUE>[, ...])
   * @param string $senderEmail
   * @param string $senderName
   * @param string $cc
   * @param string $bcc
   *
   * @return boolean
   */
  public function send($receiver,
    $subject,
    $message,
    $templateReference = '',
    $templateReplace = '',
    $senderEmail='',
    $senderName='',
    $cc='',
    $bcc=''
  ) {
    $success = false;
    $logger = Zend_Registry::get('log');
    // check if needed data exists (min receiver & subject)
    if(empty($message)
    || empty($receiver)
    || empty($subject) ) {
      $success = false;
    }
    
    
    $systemconfig = Zend_Registry::get('systemconfig');
    $sender = $systemconfig->systemEmailaddress;
    
    $senderEmail = (empty($senderEmail))? $systemconfig->systemEmailaddress : $senderEmail;
    $senderName = (empty($senderName))? $systemconfig->systemEmailname : $senderName;
    
    // use Template
    if(!empty($templateReference) && !empty($templateReplace) && is_array($templateReplace)) {
      /// fetch template by name
      $templateModel = new Model_Emails_Templates();
      $template = $templateModel->getByName($templateReference);
      if($template) {
        // replace Message
        $message = $template->txt;
        $subject = $template->subj;
        foreach($templateReplace AS $pattern => $replace) {
          $logger->debug($pattern . ' mit ' . $replace);
          $subject = str_replace($pattern, $replace, $subject);
          $message = str_replace($pattern, $replace, $message);
        }
        // use head-Area
        if($template->head=='y') {
          $templateHeader = $templateModel->getByName('header');
          // if head-template exists
          if($templateHeader) {
            $message = $templateHeader->txt . $message;
          }
        }
        // use footer-Area
        if($template->foot=='y') {
          $templateFooter = $templateModel->getByName('Footer');
          // if head-template exists
          if($templateFooter) {
            $message.= $templateFooter->txt;
          }
        }
        // replace all unused patterns
        $subject = preg_replace('~\{\{([A-Za-z0-9-_]*)\}\}~i', '', $subject);
        $message = preg_replace('~\{\{([A-Za-z0-9-_]*)\}\}~i', '', $message);
      }
      else {
        $logger->err('E-Mail-Template-ERROR: konnte Template ('.$templateReference.') nicht finden.');
      }
    }
    else {
      $logger->notice('E-Mail-Template: Kein Template angegeben oder Ersetzung im falschen Format.');
    }

    if (APPLICATION_ENV == 'development') {
      $logger->debug('E-Mail:');
      $logger->debug('-------Absender:' . $senderName . ' <' . $senderEmail . '>');
      $logger->debug('-------Empfänger:' . $receiver);
      $logger->debug('-------Betreff:' . $subject);
      $logger->debug('-------CC:' . $cc);
      $logger->debug('-------BCC:' . $bcc);
      $logger->debug('-------Nachricht:' . $message);
      $success = true;
    } else {
      
      // E-Mail verschicken
      $mail = new Zend_Mail('UTF-8');
      $mail->setBodyText($message);
      $mail->setFrom($senderEmail, $senderName);
      $mail->addTo($receiver);
      $mail->setSubject($subject);
      if (!empty($cc)) {
        $mail->addCc($cc);
      }
      if (!empty($bcc)) {
        $mail->addBcc($bcc);
      }
      try {
        $mail->send();
        $success = true;
        
      }
      catch( Zend_Mail_Transport_Exception $e ) {
        $logger->err('E-Mail-ERROR: E-Mail-Versand:' . $e->getMessage());
        $success = false;
      }
    }
    
    if($success) {
      $addData = array(
        'sender'=>$sender,
        'subj'=>$subject,
        'proj'=>Zend_Registry::get('systemconfig')->project,
        'rec'=>$receiver
      );
      $dbsuccess = $this->add($addData);
      if(!$dbsuccess) {
        $logger->debug('E-Mail-ERROR: Kein DB eintrag erstellt');
      }
      return true;
    }
    else {
      return false;
    }
  }
}

