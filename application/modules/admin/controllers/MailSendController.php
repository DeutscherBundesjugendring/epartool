<?php

class Admin_MailSendController extends Zend_Controller_Action
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
        $form = new Admin_Form_Mail_Send();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $mailer = new Dbjr_Mail();
                $mailer
                    ->setManualSent(true)
                    ->setSubject($values['subject'])
                    ->setBodyHtml($values['body_html'])
                    ->setBodyText($values['body_text'] ? $values['body_text'] : convert_html_to_text($values['body_html']));
                if ($values['mailto']) {
                    $mailer->addTo($values['mailto']);
                }
                if ($values['mailcc']) {
                    $mailer->addCc($values['mailcc']);
                }
                if ($values['mailbcc']) {
                    $mailer->addBcc($values['mailbcc']);
                }
                $mailer->send();

                $this->_flashMessenger->addMessage('Email sent.', 'success');
                $this->_redirect('/admin/mail-send');
            }
        }

        $placeholderModel = new Model_Mail_Placeholder();
        $this->view->placeholders = $placeholderModel->fetchAll(
            $placeholderModel->select()->where('is_global=?', true)
        );

        $componentModel = new Model_Mail_Component();
        $this->view->components = $componentModel->fetchAll();

        $templateModel = new Model_Mail_Template();
        $this->view->templates = $templateModel->fetchAll($templateModel->select());

        $this->view->form = $form;
    }
}
