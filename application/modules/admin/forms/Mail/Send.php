<?php

class Admin_Form_Mail_Send extends Dbjr_Form_Admin
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');
        $this->setAttrib('class', 'offset-bottom js-send-mail')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/mail-sent']);


        $mailto = $this->createElement('email', 'mailto');
        $mailto
            ->setLabel('To')
            ->setAttrib('maxlength', 255)
            ->setAttrib('placeholder', sprintf($translator->translate('Email address (max %d characters)'), 255))
            ->addValidator('EmailAddress');
        $this->addElement($mailto);


        $mailcc = $this->createElement('email', 'mailcc');
        $mailcc
            ->setLabel('Cc')
            ->setAttrib('maxlength', 255)
            ->setAttrib('placeholder', sprintf($translator->translate('Email address (max %d characters)'), 255))
            ->addValidator('EmailAddress');
        $this->addElement($mailcc);


        $mailbcc = $this->createElement('email', 'mailbcc');
        $mailbcc
            ->setLabel('Bcc')
            ->setAttrib('maxlength', 255)
            ->setAttrib('placeholder', sprintf($translator->translate('Email address (max %d characters)'), 255))
            ->addValidator('EmailAddress');
        $this->addElement($mailbcc);


        $consulModel = new Model_Consultations();
        $consultations = $consulModel
            ->fetchAll(
                $consulModel
                    ->select()
                    ->from($consulModel->getName(), array('kid', 'titl_short'))
            )
            ->toArray();
        foreach ($consultations as &$consultation) {
            $consultation['hasParticipant'] = $consulModel->hasParticipants($consultation['kid']);
            $consultation['hasNewsletter'] = $consulModel->hasNewsletterSubscribers($consultation['kid']);
            $consultation['hasFollowup'] = $consulModel->hasFollowupSubscribers($consultation['kid']);
            $consultation['hasVoter'] = $consulModel->hasVoters($consultation['kid']);
        }
        $consuls = array();
        $consulElement = $this->createElement('select', 'mail_consultation');
        foreach ($consultations as $cons) {
            $consulElement->addMultiOption($cons['kid'], $cons['titl_short']);
            $consuls[$cons['kid']] = $cons;
        }
        $consulElement
            ->addMultiOption('0', 'Please select…')
            ->setAttrib('class', 'js-consultation-selector')
            ->setLabel('Consultation')
            ->setAttrib('data-consultations', json_encode($consuls));
        $this->addElement($consulElement);


        $consulParticipant = $this->createElement('checkbox', 'mail_consultation_participant');
        $consulParticipant
            ->setLabel('All participants')
            ->setAttrib('class', 'js-consultation-participant')
            ->setAttrib('disabled', 'disabled');
        $this->addElement($consulParticipant);


        $consulVoter = $this->createElement('checkbox', 'mail_consultation_voter');
        $consulVoter
            ->setLabel('Participants who voted')
            ->setAttrib('class', 'js-consultation-vote')
            ->setAttrib('disabled', 'disabled');
        $this->addElement($consulVoter);


        $consulFollowup = $this->createElement('checkbox', 'mail_consultation_followup');
        $consulFollowup
            ->setLabel('Participants who signed up to receive follow-ups')
            ->setAttrib('class', 'js-consultation-followup')
            ->setAttrib('disabled', 'disabled');
        $this->addElement($consulFollowup);


        $consulNewsletter = $this->createElement('checkbox', 'mail_consultation_newsletter');
        $consulNewsletter
            ->setLabel('Participants who signed up to receive newsletter')
            ->setAttrib('class', 'js-consultation-newsletter')
            ->setAttrib('disabled', 'disabled');
        $this->addElement($consulNewsletter);


        $subject = $this->createElement('text', 'subject');
        $subject
            ->setLabel('Subject')
            ->setRequired(true)
            ->setAttrib('class', 'js-subject')
            ->setAttrib('size', 75)
            ->setAttrib('maxlength', 75)
            ->setAttrib('placeholder', sprintf($translator->translate('Subject (max %d characters)'), 75))
            ->addValidator('notEmpty');
        $this->addElement($subject);


        $bodyHtml = $this->createElement('textarea', 'body_html');
        $bodyHtml
            ->setLabel('Message (HTML)')
            ->setAttrib('class', 'js-body-html')
            ->setAttrib('cols', 100)
            ->setAttrib('rows', 5)
            ->setAttrib('placeholder', $translator->translate('Message as HTML'))
            ->addValidator('notEmpty');
        $this->addElement($bodyHtml);


        $bodyText = $this->createElement('textarea', 'body_text');
        $bodyText
            ->setLabel('Message (plain text)')
            ->setAttrib('class', 'js-body-text')
            ->setAttrib('cols', 100)
            ->setAttrib('rows', 5)
            ->setAttrib('placeholder', $translator->translate('Message as plain text'))
            ->addValidator('notEmpty');
        $this->addElement($bodyText);


        $this->getElement('body_html')->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_EMAIL);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_mailsend', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);


        // Media elements are to be inserted here with order > 500
        // All elements after this point must therefore have order > 1000 to be safe


        $addAttachment = $this->createElement('button', 'addAttachment');
        $addAttachment
            ->setAttrib('class', 'btn-default')
            ->setLabel('Add attachment')
            ->setOrder(1000);
        self::addCssClass($addAttachment, 'js-email-add-attachment');
        $this->addElement($addAttachment);


        $attachment = $this->createElement('media', 'TOKEN_TO_BE_REPLACED_BY_JS');
        $attachment
            ->setBelongsTo('attachments')
            ->setAttrib('disabled', 'disabled')
            ->setIsRemovable(true)
            ->setOrder(1001);
        self::addCssClass($attachment, 'hidden');
        $this->addElement($attachment);


        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary')
            ->setLabel('Send')
            ->setOrder(1002);
        $this->addElement($submit);
    }

    /**
     * Validates the form
     * @param  array   $data Data passed in by the user
     * @return boolean       Indicates if the form is valid
     */
    public function isValid($data)
    {
        $isFormValid = parent::isValid($data);

        if (empty($data['mailto'])
            && empty($data['mail_consultation_participant'])
            && empty($data['mail_consultation_voter'])
            && empty($data['mail_consultation_followup'])
            && empty($data['mail_consultation_newsletter'])
        ) {
            $isRecipientValid = false;
            $this->getElement('mailto')->setErrors(array('Mail recipient or mail recipient group must be specified.'));
        } else {
            $isRecipientValid = true;
        }

        if (!empty($data['mail_consultation'])) {
            $consulModel = new Model_Consultations();
            $this->getElement('mail_consultation_participant')->setAttrib(
                'disabled',
                $consulModel->hasParticipants($data['mail_consultation']) ? null : true
            );
            $this->getElement('mail_consultation_followup')->setAttrib(
                'disabled',
                $consulModel->hasFollowupSubscribers($data['mail_consultation']) ? null : true
            );
            $this->getElement('mail_consultation_newsletter')->setAttrib(
                'disabled',
                $consulModel->hasNewsletterSubscribers($data['mail_consultation']) ? null : true
            );
            $this->getElement('mail_consultation_voter')->setAttrib(
                'disabled',
                $consulModel->hasVoters($data['mail_consultation']) ? null : true
            );
        }

        if ($isFormValid && $isRecipientValid) {
            return true;
        }

        return false;
    }
}
