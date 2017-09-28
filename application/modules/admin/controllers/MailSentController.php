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

                $db = $mailModel->getAdapter();
                $db->beginTransaction();
                try {
                    (new Service_Email)
                        ->queueForSend($mailer)
                        ->sendQueued();
                    $db->commit();
                    $this->_flashMessenger->addMessage('Email has been queued for resending.', 'success');
                    $this->_redirect('/admin/mail-sent');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            }
        }

        $emailsRaw = $mailModel->fetchAll(
            $mailModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(['e' => $mailModel->info($mailModel::NAME)])
                ->join(
                    ['r' => (new Model_Mail_Recipient())->info(Model_Mail_Recipient::NAME)],
                    'email_id = e.id',
                    ['recipient' => 'email', 'recipient_type' => 'type']
                )
                ->where('time_sent IS NOT NULL')
                ->order('time_sent DESC')
        )
        ->toArray();

        $emails = [];
        foreach ($emailsRaw as $email) {
            if (!array_key_exists($email['id'], $emails)) {
                $emails[$email['id']] = $email;
                $emails[$email['id']]['recipients'] = [];
            }
            if (!array_key_exists($email['recipient_type'], $emails[$email['id']]['recipients'])) {
                $emails[$email['id']]['recipients'][$email['recipient_type']] = [];
            }
            $emails[$email['id']]['recipients'][$email['recipient_type']][] = $email['recipient'];
        }

        $this->view->emails = $emails;
        $this->view->form = $form;
    }
}
