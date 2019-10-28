<?php

class Admin_MailQueuedController extends Zend_Controller_Action
{
    /**
     * Holds a FlashMessanger helper instance for this controller
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * Displays and processes a form to send email
     */
    public function indexAction()
    {
        $mailModel = new Model_Mail();
        $emails = $mailModel->fetchAll(
            $mailModel->select()->where('time_sent IS NULL')->order('time_queued DESC')
        );

        $this->view->emails = $emails;
    }

    /**
     * Sends all queued emails.
     */
    public function sendAllAction()
    {
        if ((new Service_Email())->sendQueued()) {
            $this->_flashMessenger->addMessage('All emails have been successfully sent.', 'success');
        } else {
            $this->_flashMessenger->addMessage('There was an error when sending the emails.', 'error');
        }
        $this->redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
    }
}
