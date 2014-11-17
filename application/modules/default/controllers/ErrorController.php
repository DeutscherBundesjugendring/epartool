<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        Zend_Registry::get('log')->crit($errors->exception);

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'The page can not be found.';
                $this->view->environment = APPLICATION_ENV;
                $this->view->exception = $errors->exception;
                break;
            default:
                throw $errors->exception;
        }
    }
}
