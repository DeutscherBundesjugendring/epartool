<?php
/**
 * Plugin for Navigation Component, currently used in admin module only
 * @author Markus
 *
 */
class Plugin_Navigation extends Zend_Controller_Plugin_Abstract {
  
  /**
   * Set active pages
   * @see Zend_Controller_Plugin_Abstract::postDispatch()
   */
  public function postDispatch(Zend_Controller_Request_Abstract $request) {
    $view = Zend_Layout::getMvcInstance()->getView();
    
    $kid = $request->getParam('kid', 0);
    if ($kid > 0) {
      $consultationModel = new Model_Consultations();
      $consultation = $consultationModel->find($kid)->current();
      $page = $view->navigation()->findOneByLabel('Konsultationen');
      if ($page) {
        $page->setActive();
      }
      if ($consultation) {
        $subpage = $view->navigation()->findOneByLabel($consultation->titl);
        if ($subpage) {
          $subpage->setActive();
        }
      }
    }
  }
}
?>