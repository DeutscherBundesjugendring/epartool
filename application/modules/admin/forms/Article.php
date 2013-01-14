<?php
/**
 * Article
 *
 * @description   Form of Article
 * @author        Markus Hackel
 */
class Admin_Form_Article extends Zend_Form {
  protected $_iniFile = '/application/modules/admin/forms/Article.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}
