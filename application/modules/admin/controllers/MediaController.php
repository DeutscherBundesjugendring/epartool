<?php
/**
 * MediaController
 *
 * @desc   Media Admin
 * @author        Markus Hackel
 */
class Admin_MediaController extends Zend_Controller_Action {
  /**
   * FlashMessenger
   *
   * @var Zend_Controller_Action_Helper_FlashMessenger
   */
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
    $this->initView();
  }

  /**
   * @desc media dashboard
   * @return void
   */
  public function indexAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $consultation = null;
    $directory = APPLICATION_PATH . '/public/media/';
    $dir_ws = '/media/';
    if ($kid > 0) {
      $consultationModel = new Consultations();
      $consultation = $consultationModel->find($kid)->current();
      if ($consultation) {
        $directory.= 'consultations/' . $kid . '/';
        $dir_ws.= 'consultations/' . $kid . '/';
        if (!is_dir($directory)) {
          mkdir($directory);
        }
      }
//      Zend_Debug::dump($directory);
    } else {
      $directory.= 'misc/';
      $dir_ws.= 'misc/';
    }
    $files = scandir($directory);
    $formClass = Zend_Registry::get('formloader')->load('Media_Delete');
    $deleteForm = new $formClass();
    $action = $this->view->url(array(
      'action' => 'delete',
      'kid' =>$kid
    ));
    $deleteForm->setAction($action);
    $i = 0;
    $aFileinfo = array();
    if (!empty($files)) {
      foreach ($files as $filename) {
        if (is_file($directory . $filename)) {
          $i++;
          $deleteForm->setAttrib('name', 'delete_' . $i)
            ->setAttrib('id', 'delete_' . $i);
          $deleteForm->getElement('filename')->setValue($filename);
          $aFileinfo[$filename] = pathinfo($directory . $filename);
          $aFileinfo[$filename]['size'] = ceil(filesize($directory . $filename)/1024);
          $aFileinfo[$filename]['deleteform'] = $deleteForm;
        }
      }
    }
//    Zend_Debug::dump($aFileinfo);
    $formClass = Zend_Registry::get('formloader')->load('Media_Upload');
    $form = new $formClass();
    $form->setAction($this->view->url(array(
      'action' => 'upload',
      'kid' =>$kid
    )));
    
    $aMessages = array(
      'note' => $this->_flashMessenger->getMessages('note'),
      'warning' => $this->_flashMessenger->getMessages('warning')
    );
    $this->view->assign(array(
      'messages' => $aMessages,
      'kid' => $kid,
      'consultation' => $consultation,
      'directory' => $dir_ws,
      'files' => $aFileinfo,
      'form' => $form
    ));
  }
  
  /**
   * @desc Handles Upload Action, redirects to index view
   * @return void
   */
  public function uploadAction() {
    $kid = (int)$this->getRequest()->getParam('kid', 0);
    $formData = $this->_request->getUserParams();
    $formClass = Zend_Registry::get('formloader')->load('Media_Upload');
    $form = new $formClass();
    if ($form->isValid($formData)) {
      $originalFilename = pathinfo($form->file->getFileName());
      if ($kid > 0) {
//        Zend_Debug::dump($originalFilename);exit();
        $uploadDir = APPLICATION_PATH . '/public/media/consultations/' . $kid;
      } else {
        $uploadDir = APPLICATION_PATH . '/public/media/misc';
      }
      $uploadFilename = $uploadDir . '/' . $originalFilename['basename'];
      if (is_dir($uploadDir)) {
        $upload = new Zend_File_Transfer_Adapter_Http();
        $upload->addFilter('Rename', array(
          'target' => $uploadFilename,
          'overwrite' => true
        ));
        try {
          // upload received file(s)
          if ($upload->receive()) {
            $this->_helper->flashMessenger
              ->addMessage('Die Datei »'.$originalFilename['basename'].'« wurde erfolgreich hinzugefügt.', 'note');
          } else {
            $this->_helper->flashMessenger
              ->addMessage('Die Datei konnte nicht hinzugefügt werden. Sie war möglicherweise zu groß oder die Schreibrechte nicht ausreichend.', 'warning');
          }
        } catch (Zend_File_Transfer_Exception $e) {
          $this->_helper->flashMessenger
            ->addMessage($e->getMessage(), 'warning');
        }
      }
    } else {
      $this->_helper->flashMessenger
        ->addMessage('Upload fehlgeschlagen.', 'warning');
    }
    $uploadedData = $form->getValues();
//    Zend_Debug::dump($upload->get(), 'Upload Object:');
    $this->_redirect($this->view->url(array(
      'action' => 'index',
      'kid' =>$kid
    )));
  }
  
  /**
   * @desc Handles Delete Action, redirects to index view
   * @return void
   */
  public function deleteAction() {
    $kid = (int)$this->getRequest()->getParam('kid', 0);
    $formData = $this->_request->getParams();
    $formClass = Zend_Registry::get('formloader')->load('Media_Delete');
    $form = new $formClass();
    if ($form->isValid($formData)) {
      $originalFilename = $form->getElement('filename')->getValue();
      if ($kid > 0) {
//        Zend_Debug::dump($originalFilename);exit();
        $deleteDir = APPLICATION_PATH . '/public/media/consultations/' . $kid;
      } else {
        $deleteDir = APPLICATION_PATH . '/public/media/misc';
      }
      $deleteFilename = $deleteDir . '/' . $originalFilename;
      if (is_file($deleteFilename)) {
        if (unlink($deleteFilename)) {
          $this->_helper->flashMessenger
              ->addMessage('Die Datei »'.$originalFilename.'« wurde erfolgreich gelöscht.', 'note');
        } else {
          $this->_helper->flashMessenger
              ->addMessage('Datei konnte nicht gelöscht werden.', 'warning');
        }
      } else {
        $this->_helper->flashMessenger
              ->addMessage('Datei ' . $deleteFilename . ' ist keine gültige Datei.', 'warning');
      }
    } else {
      $this->_helper->flashMessenger
                ->addMessage('Formulardaten ungültig', 'warning');
    }
    $this->_redirect($this->view->url(array(
      'action' => 'index',
      'kid' =>$kid
    )));
  }
}
?>