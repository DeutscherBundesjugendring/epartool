<?php

class Admin_MailSentController extends Zend_Controller_Action
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
        $form = new Admin_Form_ListControl();

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            if ($form->isValid($values)) {
                $mail = $mailModel
                    ->find($this->getRequest()->getPost('resendId'))
                    ->current();

                $mailer = new Dbjr_Mail();
                $mailer
                    ->setManualSent(true)
                    ->setSubject($mail->subject)
                    ->setBodyHtml($mail->body_html)
                    ->setBodyText($mail->body_text);
                foreach ($mail->findModel_Mail_Recipient() as $recipient) {
                    if ($recipient->type == Model_Mail_Recipient::TYPE_TO) {
                        $mailer->addTo($recipient->email, $recipient->name);
                    } elseif ($recipient->type == Model_Mail_Recipient::TYPE_CC) {
                        $mailer->addCc($recipient->email, $recipient->name);
                    } elseif ($recipient->type == Model_Mail_Recipient::TYPE_BCC) {
                        $mailer->addBcc($recipient->email, $recipient->name);
                    }
                }

                $mailer->send();

                $this->_flashMessenger->addMessage('Email queued for resend.', 'success');
                $this->_redirect('/admin/mail-sent');
            }
        }

        $emails = $mailModel->fetchAll(
            $mailModel->select()->where('time_sent IS NOT NULL')->order('time_sent DESC')
        );

        $this->view->emails = $emails;
        $this->view->form = $form;
    }
}

