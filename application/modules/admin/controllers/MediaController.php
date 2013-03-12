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
    $directory = realpath(APPLICATION_PATH . '/../public/media');
    $dir_ws = $this->view->baseUrl() . '/media';
    if ($kid > 0) {
      $consultationModel = new Model_Consultations();
      $consultation = $consultationModel->find($kid)->current();
      if ($consultation) {
        $directory.= '/consultations/' . $kid;
        $dir_ws.= '/consultations/' . $kid;
        if (!is_dir($directory)) {
          mkdir($directory);
        }
      }
    } else {
      $directory.= '/misc';
      $dir_ws.= '/misc';
    }
    $files = scandir($directory);
    $action = $this->view->url(array(
      'action' => 'delete',
      'kid' =>$kid
    ));
    $i = 0;
    $aFileinfo = array();
    if (!empty($files)) {
      foreach ($files as $filename) {
        if (is_file($directory . '/' . $filename)) {
          $i++;
          $deleteForm = new Admin_Form_Media_Delete();
          $deleteForm->setAction($action);
          $deleteForm->setAttrib('name', 'delete_' . $i)
            ->setAttrib('id', 'delete_' . $i);
          $deleteForm->getElement('file')->setValue($filename);
          $aFileinfo[$filename] = pathinfo($directory . '/' . $filename);
          $aFileinfo[$filename]['size'] = ceil(filesize($directory . '/' . $filename)/1024);
          $aFileinfo[$filename]['deleteform'] = $deleteForm;
        }
      }
    }
    $form = new Admin_Form_Media_Upload();
    $form->setAction($this->view->url(array(
      'action' => 'upload',
      'kid' =>$kid
    )));
    
    $this->view->assign(array(
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
    $form = new Admin_Form_Media_Upload();
    if ($form->isValid($formData)) {
      $originalFilename = pathinfo($form->file->getFileName());
      if ($kid > 0) {
        $uploadDir = realpath(APPLICATION_PATH . '/../public/media/consultations/' . $kid);
      } else {
        $uploadDir = realpath(APPLICATION_PATH . '/../public/media/misc');
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
            $this->_flashMessenger
              ->addMessage('Die Datei »'.$originalFilename['basename'].'« wurde erfolgreich hinzugefügt.', 'success');
          } else {
            $this->_flashMessenger
              ->addMessage('Die Datei konnte nicht hinzugefügt werden. Sie war möglicherweise zu groß oder die Schreibrechte nicht ausreichend.', 'error');
          }
        } catch (Zend_File_Transfer_Exception $e) {
          $this->_flashMessenger
            ->addMessage($e->getMessage(), 'error');
        }
      }
    } else {
      $this->_flashMessenger
        ->addMessage('Upload fehlgeschlagen.', 'error');
    }
    $uploadedData = $form->getValues();
    $this->redirect($this->view->url(array(
      'action' => 'index',
      'kid' =>$kid
    )), array('prependBase' => false));
  }
  
  /**
   * @desc Handles Delete Action, redirects to index view
   * @return void
   */
  public function deleteAction() {
    $kid = (int)$this->getRequest()->getParam('kid', 0);
    $formData = $this->_request->getParams();
    $form = new Admin_Form_Media_Delete();
    if ($form->isValid($formData)) {
      $originalFilename = $form->getElement('file')->getValue();
      if ($kid > 0) {
        $deleteDir = realpath(APPLICATION_PATH . '/../public/media/consultations/' . $kid);
      } else {
        $deleteDir = realpath(APPLICATION_PATH . '/../public/media/misc');
      }
      $deleteFilename = $deleteDir . '/' . $originalFilename;
      if (is_file($deleteFilename)) {
        if (unlink($deleteFilename)) {
          $this->_flashMessenger
              ->addMessage('Die Datei »'.$originalFilename.'« wurde erfolgreich gelöscht.', 'success');
        } else {
          $this->_flashMessenger
              ->addMessage('Datei konnte nicht gelöscht werden.', 'error');
        }
      } else {
        $this->_flashMessenger
              ->addMessage('Datei ' . $deleteFilename . ' ist keine gültige Datei.', 'error');
      }
    } else {
      $this->_flashMessenger
                ->addMessage('Formulardaten ungültig', 'error');
    }
    $this->redirect($this->view->url(array(
      'action' => 'index',
      'kid' =>$kid
    )), array('prependBase' => false));
  }
}
?>