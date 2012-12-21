<?php
/**
 * AdminController
 *
 * @desc   administrationarea
 * @author        Jan Suchandt
 */
class AdminController extends Zend_Controller_Action {
  /**
   * @desc Konstruktor
   * @return void
   */
  public function init() {
    // Setzen des Standardlayouts
    $this->_helper->layout->setLayout('backend');
  }

  /**
   * @desc admin dashboard
   * @return void
   */
  public function indexAction() {}

  /**
   * Systemverwaltung
   *
   * @return void
   */
  public function configsAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $post = $request->getPost('group');

    $configs = new Configs();
    $this->view->configs = $configs->getByGroup($post);

    $groups = new ConfigsGroups();
    $this->view->groups = $groups->getAll();

  }

  /**
   * Systemverwaltung - Einzelnen Konfigurationsparameter bearbeiten
   *
   * @return void
   */
  public function configseditAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $id = $request->get('id');

    if (!empty($id)) {
      // Konfigurations-Model laden
      $configs = new Configs();
      // Loader aus Registry beziehen
      $formClass = Zend_Registry::get('formloader')->load('ConfigsEdit');
      // Formular-Klasse erstellen
      $form = new $formClass();
      // Formular konfigurieren
      $form->setAction('/admin/configsedit/id/' . $id);

      $this->view->form = $form;
      $this->view->config = $configs->getById($id);
    }
    else {
      $this->view->error = 'Kein Eintrag gefunden';
    }
    /*
    $configs = new Configs();
    $this->view->configs = $configs->getByGroup($post);
    /*
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $post = $request->getPost('group');
    
    $configs = new Configs();
    $this->view->configs = $configs->getByGroup($post);
    
    $groups = new ConfigsGroups();
    $this->view->groups = $groups->getAll();
     */

  }

}
