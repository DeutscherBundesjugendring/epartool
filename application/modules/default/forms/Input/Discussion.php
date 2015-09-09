<?php

class Default_Form_Input_Discussion extends Dbjr_Form_Web
{

    public function init()
    {
        $this->setMethod('post');

        $body = $this->createElement('textarea', 'body');
        $placeholder = Zend_Registry::get('Zend_Translate')->translate('Your discussion post');
        $body
            ->setAttrib('rows', 5)
            ->setAttrib('placeholder', $placeholder);
        $this->addElement($body);


        $email = $this->createElement('text', 'email');
        $email
            ->setLabel('Your email')
            ->setRequired(true)
            ->setAttrib('type', 'email')
            ->setAttrib('placeholder', '@')
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        $placeholder = Zend_Registry::get('Zend_Translate')->translate('e.g.');
        $videoId = $this->createElement('text', 'video_id');
        $videoId
            ->setLabel('YouTube video ID')
            ->setAttrib('class', 'form-control')
            ->setAttrib('placeholder', $placeholder . ' tiGLudbJits')
            ->setDescription('https://www.youtube.com/watch?v=');
        $videoId->setDecorators(['ViewHelper',
            [
                'Description',
                ['tag' => 'span', 'class' => 'input-group-addon', 'placement' => 'prepend']
            ],
            [
                ['inputGroup' => 'HtmlTag'],
                ['tag' => 'div', 'class' => 'input-group'],
            ],
            ['Label'],
            [
                ['formGroup' => 'HtmlTag'],
                ['tag' => 'div', 'class' => 'form-group']
            ],
        ]);
        $this->addElement($videoId);


        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-default')
            ->setLabel('Send');
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
