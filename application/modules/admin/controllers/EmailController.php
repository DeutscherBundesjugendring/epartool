<?php
/**
 * EmailController
 *
 */
class Admin_EmailController extends Zend_Controller_Action {
  
  protected $_flashMessenger = null;
  
  /**
   * @desc Construct
   * @return void
   */
  public function init() {
    // Setzen des Standardlayouts
    $this->_helper->layout->setLayout('backend');
    $this->_flashMessenger =
        $this->_helper->getHelper('FlashMessenger');
  }

  /**
   * @desc overview of sended e-mails
   * @return void
   */
  public function indexAction() {
    $emailModel = new Model_Emails();
    $this->view->emaillist = $emailModel->getAll();
  }
  
  /**
   * @desc overview of e-mail-templates
   * @return void
   */
  public function templateAction() {
    $templateModel = new Model_Emails_Templates();
    $this->view->templateList = $templateModel->getAll();
  }

  /**
   * new email to send
   */
  public function sendAction() {
    $form = new Admin_Form_Email_Send();
    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          $emailModel = new Model_Emails();
          if ($form->isValid($this->getRequest()->getPost())) {
            $message = $this->getRequest()->getParam('message');
            $receiver = $this->getRequest()->getParam('empfaenger');
            $subject = $this->getRequest()->getParam('subject');
            $cc = $this->getRequest()->getParam('mailcc');
            $bcc = $this->getRequest()->getParam('mailbcc');

            $mailsended = $emailModel->send($receiver, $subject, $message, null, null, null, null, $cc, $bcc);
            
            Zend_Debug::dump($mailsended);
            if ($mailsended) {
              $this->_flashMessenger->addMessage('E-Mail wurde versendet.', 'success');
              $this->_redirect('admin/email/');
            } else {
              $this->_flashMessenger->addMessage('Fehler beim Versenden der E-Mail.', 'error');
              $form->populate($this->getRequest()->getPost());
            }
          } else {
            $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
            $form->populate($this->getRequest()->getPost());
          }
    }
    $this->view->form = $form;
  }
  
  /**
   * Template edit/new
   */
  public function edittemplateAction() {
    $form = new Admin_Form_Email_Template();
    $templateModel = new Model_Emails_Templates();
    
    $mid = $this->getRequest()->getParam('mid');
    $form->setAction($this->view->baseUrl() . '/admin/email/edittemplate/');
    $redirect = '/admin/email/template/';
    $values=array();
    if(!empty($mid)) {
      $form->setAction($this->view->baseUrl() . '/admin/email/edittemplate/mid/' . $mid);
      $redirect.='mid/'.$mid;
      $template = $templateModel->getById($mid);
      $values = $template->toArray();
    }
    
    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          if ($form->isValid($this->getRequest()->getPost())) {
            // edit entry
            if(!empty($mid)) {
              $template->setFromArray($form->getValues());
              $template->save();
            }
            // add new entry
            else {
              $templateRow = $templateModel->createRow($form->getValues());
              $success = $templateRow->save();
            }
            $this->_flashMessenger->addMessage('Änderungen gespeichert.', 'success');
            
            $this->_redirect($redirect);
          } else {
            $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
            $form->populate($form->getValues());
          }
    } else {
      $form->populate($values);
    }
    $this->view->form = $form;
  }
  
  /**
   * delete sended e-mail-log
   */
  public function deleteAction() {
    $id = $this->getRequest()->getParam('id', 0);
    if (!empty($id)) {
      $emailModel = new Model_Emails();
      $deleted = $emailModel->deleteById($id);
//      $this->_helper->layout()->disableLayout();
//      $this->_helper->viewRenderer->setNoRender(true);
      if ($deleted > 0) {
        $this->_flashMessenger->addMessage('Der E-Mail-Eintrag wurde gelöscht.', 'success');
      }
      else {
        $this->_flashMessenger->addMessage('Fehler beim Löschen des E-Mail-Eintrags. Bitte versuchen Sie es erneut.', 'error');
      }
    }
    $this->_redirect('/admin/email/index');
  }
}
?>