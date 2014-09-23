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
            $postData = $this->getRequest()->getPost();
            if (isset($postData['attachments'])) {
                foreach ($postData['attachments'] as $i => $file) {
                    $attachment = $form->createElement('media', (string) $i);
                    $attachment
                        ->setBelongsTo('attachments')
                        ->setValue($file)
                        ->setOrder(500 + $i);
                    $form->addElement($attachment);
                }
            }

            if ($form->isValid($postData)) {
                $values = $form->getValues();
                $userTableName = (new Model_Users())->info(Model_Users::NAME);
                $userConsultDataTableName = (new Model_User_Info())->info(Model_User_Info::NAME);
                $mailer = new Dbjr_Mail();
                $mailer
                    ->setManualSent(true)
                    ->setSubject($values['subject'])
                    ->setBodyHtml($values['body_html'])
                    ->setBodyText($values['body_text']);
                if ($values['mailto']) {
                    $mailer->addTo($values['mailto']);
                }
                if ($values['mailcc']) {
                    $mailer->addCc($values['mailcc']);
                }
                if ($values['mailbcc']) {
                    $mailer->addBcc($values['mailbcc']);
                }
                if ($values['mail_consultation_participant']) {
                    $mailer->addRecipientsConsultationParticipants($values['mail_consultation'], Dbjr_Mail::RECIPIENT_TYPE_BCC);
                }
                if ($values['mail_consultation_voter']) {
                    $mailer->addRecipientsConsultationParticipants(
                        $values['mail_consultation'],
                        Dbjr_Mail::RECIPIENT_TYPE_BCC,
                        Model_User_Info::PARTICIPANT_TYPE_VOTER
                    );
                }
                if ($values['mail_consultation_newsletter']) {
                    $mailer->addRecipientsConsultationParticipants(
                        $values['mail_consultation'],
                        Dbjr_Mail::RECIPIENT_TYPE_BCC,
                        Model_User_Info::PARTICIPANT_TYPE_NEWSLETTER_SUBSCRIBER
                    );
                }
                if ($values['mail_consultation_followup']) {
                    $mailer->addRecipientsConsultationParticipants(
                        $values['mail_consultation'],
                        Dbjr_Mail::RECIPIENT_TYPE_BCC,
                        Model_User_Info::PARTICIPANT_TYPE_FOLLOWUP_SUBSCRIBER
                    );
                }
                if ($postData['attachments']) {
                    foreach ($postData['attachments'] as $file) {
                        if ($file) {
                            $mailer->addAttachmentFile($file);
                        }
                    }
                }
                (new Service_Email)->queueForSend($mailer);

                $this->_flashMessenger->addMessage('Email sent.', 'success');
                $this->_redirect('/admin/mail-send');
            }
        }

        $placeholderModel = new Model_Mail_Placeholder();
        $this->view->placeholders = $placeholderModel->fetchAll(
            $placeholderModel->select()->where('is_global=?', true)
        );

        $componentModel = new Model_Mail_Component();
        $this->view->components = $componentModel->fetchAll($componentModel->select());

        $templateModel = new Model_Mail_Template();
        $this->view->templates = $templateModel->getAllbyType(Model_Mail_Template_Type::TEMPLATE_TYPE_CUSTOM);

        $this->view->form = $form;
    }

    /**
     * Echoes a json with template data (subject, body_text, body_html)
     */
    public function templateJsonAction()
    {
        $templateId = $this->getRequest()->getParam('templateId');
        $templateModel = new Model_Mail_Template();
        $templateTypeModel = new Model_Mail_Template_Type();
        $template = $templateModel->fetchRow(
            $templateModel
                ->select()
                ->from(
                    $templateModel->getName(),
                    array('subject', 'body_html', 'body_text')
                )
                ->where($templateModel->getName() . '.id=?', $templateId)
        );
        if ($template) {
            echo json_encode($template->toArray());
        }
        die();
    }
}
