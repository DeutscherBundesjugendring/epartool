<?php
/**
 * UserController
 *
 * @desc   Users for Consultation
 */
class Admin_UserController extends Zend_Controller_Action {
  
  protected $_flashMessenger = null;
  
  
  /**
   * Construct
   * @return void
   */
  public function init() {
    // Setzen des Standardlayouts
    $this->_helper->layout->setLayout('backend');
    $this->_flashMessenger =
        $this->_helper->getHelper('FlashMessenger');
  }

  /**
   * index
   * @return void
   */
  public function indexAction() {
    $userModel = new Model_Users();
    $this->view->userlist = $userModel->getAll();
  }
  
  public function createAction() {
    $form = new Admin_Form_User_Create();
    if ($this->getRequest()->isPost()) {
      if ($form->isValid($this->getRequest()->getPost())) {
        $userModel = new Model_Users();
        // check if email allready exists
        $emailAddress =$form->getValue('email');
        if(!$userModel->emailExists($emailAddress)) {
          $userRow = $userModel->createRow($form->getValues());
          $newId = $userRow->save();
          if ($newId > 0) {
            $this->_flashMessenger->addMessage('Neuer Benutzer wurde erstellt.', 'success');
          } else {
            $this->_flashMessenger->addMessage('Erstellen eines neuen Benutzers fehlgeschlagen!', 'error');
          }
          
          $this->_redirect($this->view->url(array(
            'action' => 'index'
          )));
        }
        else {
          $this->_flashMessenger->addMessage('Diese E-Mail-Adresse existiert bereits! Wählen Sie eine andere.', 'error');
          $form->populate($form->getValues());
        }
        

      } else {
        $form->populate($form->getValues());
      }
    }

    $this->view->assign(array(
      'form' => $form
    ));
  }
  
  public function editAction() {
    $uid = $this->getRequest()->getParam('uid', 0);
    if ($uid > 0) {
      $userModel = new Model_Users();
      $user = $userModel->getById($uid);
      if (!empty($user)) {
          $form = new Admin_Form_User_Edit();
          $form->setAction('/admin/user/edit/uid/' . $uid);
          if ($this->getRequest()->isPost()) {
            // Formular wurde abgeschickt und muss verarbeitet werden
            $params = $this->getRequest()->getPost();
            if ($form->isValid($params)) {
              // Prüfe ob E-Mail bereits existiert
              $emailAddress =$form->getValue('email');
              if(!$userModel->emailExists($emailAddress)) {
                $row = $userModel->find($uid)->current();
                $row->setFromArray($form->getValues());
                $row->save();
                $this->_flashMessenger->addMessage('Änderungen wurden gespeichert.', 'success');
                $form->populate($this->getRequest()->getPost());
              }
              else {
                $this->_flashMessenger->addMessage('Diese E-Mail-Adresse existiert bereits! Wählen Sie eine andere.', 'error');
                $form->populate($this->getRequest()->getPost());
              }

            } else {
              $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben und versuchen Sie es erneut!', 'error');
              $form->populate($this->getRequest()->getPost());
            }
          }
          else {
            $form->populate($user->toArray());
          }
      }
      else {
        $this->_flashMessenger->addMessage('Benutzer nicht gefunden!', 'error');
        $this->_redirect('/admin/user/index');
      }
    }
    else {
      $this->_flashMessenger->addMessage('Benutzer nicht gefunden!', 'error');
      $this->_redirect('/admin/user/index');
    }
    
    $this->view->assign(array(
      'user' => $user,
      'form' => $form
    ));
  }
  
  public function deleteAction() {
    $uid = $this->getRequest()->getParam('uid', 0);
    if ($uid > 0) {
      $userModel = new Model_Users();
      $deleted = $userModel->deleteById($uid);
      //$this->_helper->layout()->disableLayout();
      //$this->_helper->viewRenderer->setNoRender(true);
      if ($deleted > 0) {
        $this->_flashMessenger->addMessage('Der Benutzer wurde gelöscht.', 'success');
      }
      else {
        $this->_flashMessenger->addMessage('Fehler beim Löschen des Benutzers. Bitte versuchen Sie es erneut.', 'error');
      }
    }
    $this->_redirect('/admin/user/index');
  }
}
?>