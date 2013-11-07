<?php

class Admin_Form_Email_ListControl extends Zend_Form {

  protected $_iniFile = '/modules/admin/forms/Email/ListControl.ini';


  public function init() {
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/email/list-control');
  }
}