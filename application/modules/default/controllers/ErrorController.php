<?php
/**
 * ErrorController
 */
class ErrorController extends Zend_Controller_Action {
  /**
   * Error Action
   *
   * @return void
   */
  public function errorAction() {
    // Hole das Fehlerobjekt aus dem Request-Objekt
    $errors = $this->getRequest()->getParam('error_handler');

    if(APPLICATION_ENV != 'production') {
      Zend_Registry::get('log')->crit($errors->exception);
    }


    // Prüfe den Fehlertyp
    switch ($errors->type) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
      // Controller oder Aktion nicht gefunden, somit 404 Fehler
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = 'Seite konnte nicht gefunden werden';
        break;
      default:
      // Anderer Fehler in der Anwendung
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->message = 'Es ist ein Fehler aufgetreten';
        break;
    }

    // Übergebe die aktuelle Umgebung sowie die Ausnahme an den View
    $this->view->environment = APPLICATION_ENV;
    $this->view->exception = $errors->exception;
  }

  public function noAccessAction() {

  }
}
