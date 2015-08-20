<?php

class Default_Form_SubscribeNotification extends Dbjr_Form_Web
{
    public function init()
    {
        $this
            ->prepareElements()
            ->setAttrib('class', 'form-inline offset-top-large offset-bottom-large hidden-print')
            ->setMethod('post');
    }

    /**
     * Generates the elements for the form and adds them to the form
     * @return Default_Form_Input_Subscription  Fluent interface
     */
    private function prepareElements()
    {
        $label = '<span class="glyphicon glyphicon-saved icon-offset" aria-hidden="true"></span>' . Zend_Registry::get('Zend_Translate')->translate('Subscribe thread');
        $subscribe = $this->createElement('button', 'subscribe')
            ->setAttrib('type', 'submit')
            ->setAttrib('class', 'btn-default')
            ->setAttrib('escape', false)
            ->setLabel($label);

        $hash = $this->createElement('hash', 'csrf_token_input_subscribe_question', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl);
        }

        $this->addElements([$subscribe, $hash]);

        return $this;
    }

    public function requireId()
    {
        $emailEl = $this->createElement('email', 'email');
        $emailEl
            ->setOrder(0)
            ->setAttrib('placeholder', 'me@example.com')
            ->setLabel('Email')
            ->setRequired(true);
        $this->addElement($emailEl);
    }
}
