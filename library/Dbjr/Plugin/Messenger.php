<?php
/**
 * Plugin für den Flash Messenger
 * @author Markus
 *
 */
class Plugin_Messenger extends Zend_Controller_Plugin_Abstract {
  
  /**
   * Stellt die Messages aus dem FlashMessenger im Layout bereit
   * @see Zend_Controller_Plugin_Abstract::postDispatch()
   */
  public function postDispatch(Zend_Controller_Request_Abstract $request) {
    $messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
    $layout = Zend_Layout::getMvcInstance();
    $messages = array();
    $messages['success'] = $messenger->getMessages('success');
    $messagesCurrent['success'] = $messenger->setNamespace('success')->getCurrentMessages();
    $messenger->setNamespace('success')->clearCurrentMessages();
    $messages['success'] = array_merge($messages['success'], $messagesCurrent['success']);
    $messages['error'] = $messenger->getMessages('error');
    $messagesCurrent['error'] = $messenger->setNamespace('error')->getCurrentMessages();
    $messenger->setNamespace('error')->clearCurrentMessages();
    $messages['error'] = array_merge($messages['error'], $messagesCurrent['error']);
    $messages['info'] = $messenger->getMessages('info');
    $messagesCurrent['info'] = $messenger->setNamespace('info')->getCurrentMessages();
    $messenger->setNamespace('info')->clearCurrentMessages();
    $messages['info'] = array_merge($messages['info'], $messagesCurrent['info']);
    $layout->messages = $messages;
  }
}