<?php
class Admin_TagController extends Zend_Controller_Action {
  
  protected $_flashMessenger = null;
  
  public function init() {
    $this->_helper->layout->setLayout('backend');
    $this->_flashMessenger =
        $this->_helper->getHelper('FlashMessenger');
  }
  
  public function indexAction() {
    $tagModel = new Model_Tags();
    $this->view->tags = $tagModel->getAll();
    $this->view->createForm = new Admin_Form_Tag();
  }
  
  public function createAction() {
    if ($this->_request->isPost()) {
      $form = new Admin_Form_Tag();
      $data = $this->_request->getPost();
      if ($form->isValid($data)) {
        $tagModel = new Model_Tags();
        $key = $tagModel->add($data);
        if (!empty($key)) {
          $this->_flashMessenger->addMessage('Neues Schlagwort angelegt.', 'success');
        }
        $this->redirect('/admin/tag');
      } else {
        $this->view->createForm = $form->populate($data);
      }
    } else {
      $this->redirect('/admin/tag');
    }
  }
  
  public function editAction() {
    if ($this->_request->isPost()) {
      $data = $this->_request->getPost();
      $validator = new Zend_Validate_Alnum(true);
      $tagModel = new Model_Tags();
      $nrUpdated = 0;
      foreach ($data['tg_de'] as $tg_nr => $tg_de) {
        if ($tg_de != $data['tag_old'][$tg_nr]) {
          // field was changed
          if ($validator->isValid($tg_de)) {
            $nr = $tagModel->updateById($tg_nr, array('tg_de' => $tg_de));
            $nrUpdated+= $nr;
          } else {
            $this->_flashMessenger->addMessage('Ungültiger Wert für "'
              . $data['tag_old'][$tg_nr] . '"!', 'error');
          }
        }
      }
      if ($nrUpdated > 0) {
        $this->_flashMessenger->addMessage($nrUpdated . ' Einträge geändert.', 'success');
      }
    }
    $this->redirect('/admin/tag');
  }
  
  public function deleteAction() {
    $validator = new Zend_Validate_Int();
    $tg_nr = $this->_request->getParam('tag', 0);
    if (!$validator->isValid($tg_nr)) {
      throw new Zend_Validate_Exception('Given parameter "tag" must be integer!');
    }
    if ($tg_nr > 0) {
      $inputsTagsModel = new Model_InputsTags();
      if (!$inputsTagsModel->tagExists($tg_nr)) {
        $tagModel = new Model_Tags();
        $nr = $tagModel->deleteById($tg_nr);
        if ($nr > 0) {
          $this->_flashMessenger->addMessage('Eintrag gelöscht.', 'success');
        }
      } else {
        $this->_flashMessenger
          ->addMessage('Dieser Tag ist bereits Beiträgen zugeordnet und kann deshalb nicht gelöscht werden!', 'error');
      }
    }
    $this->redirect('/admin/tag');
  }
}
?>