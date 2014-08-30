<?php

class Default_Form_Input_SubscriptionQuestion extends Zend_Form
{
    public function init()
    {
        $this
            ->prepareElements()
            ->setAttrib('class', 'form-inline offset-top-large offset-bottom-large')
            ->setMethod('post');
    }

    /**
     * Generates the elements for the form and adds them to the form
     * @return Default_Form_Input_Subscription  Fluent interface
     */
    private function prepareElements()
    {
        $label = '<span class="icon-rss"></span>' . (new Zend_View())->translate('Subscribe thread');
        $subscribe = $this->createElement('button', 'subscribe')
            ->setAttrib('class', 'btn')
            ->setAttrib('type', 'submit')
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

    public function addEmailField()
    {
        $emailEl = $this->createElement('text', 'email');
        $emailEl
            ->setOrder(0)
            ->setAttrib('type', 'email')
            ->setAttrib('placeholder', 'me@example.com')
            ->setAttrib('class', 'input-xlarge')
            ->setLabel('Email')
            ->addValidator('emailAddress')
            ->setRequired(true);
        $this->addElement($emailEl);
    }
}
