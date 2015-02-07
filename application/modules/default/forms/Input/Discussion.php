<?php

class Default_Form_Input_Discussion extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        $body = $this->createElement('textarea', 'body');
        $placeholder = Zend_Registry::get('Zend_Translate')->translate('Your discussion post');
        $body
            ->setAttrib('rows', 5)
            ->setAttrib('class', 'input-block-level')
            ->setAttrib('placeholder', $placeholder);
        $this->addElement($body);


        $email = $this->createElement('text', 'email');
        $email
            ->setLabel('Your email')
            ->setRequired(true)
            ->setAttrib('type', 'email')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib('placeholder', '@')
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        $videoId = $this->createElement('text', 'video_id');
        $videoId
            ->setLabel('Video Id')
            ->setAttrib('class', 'input-xlarge');
        $this->addElement($videoId);


        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setLabel('Send')
            ->setAttrib('class', 'btn');
        $this->addElement($submit);

        $hash = $this->createElement('hash', 'csrf_token_register', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }

    public function isValid($data)
    {
        $bodyEl = $this->getElement('body');
        $videoIdEl = $this->getElement('video_id');
        $bodyEl->clearErrorMessages();
        $videoIdEl->clearErrorMessages();
        if (!$data['body'] && !$data['video_id']) {
            $msg = Zend_Registry::get('Zend_Translate')->translate('Either text or video have to be submitted.');
            $bodyEl->addError($msg);
            $videoIdEl->addError($msg);
            $this->markAsError();
        }

        return parent::isValid($data);
    }
}
