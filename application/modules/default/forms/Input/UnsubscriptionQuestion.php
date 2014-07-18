<?php

class Default_Form_Input_UnsubscriptionQuestion extends Zend_Form
{
    public function init()
    {
        $this
            ->prepareElements()
            ->setMethod('post');
    }

    /**
     * Generates the elements for the form and adds them to the form
     * @return Default_Form_Input_Subscription  Fluent interface
     */
    private function prepareElements()
    {
        $subscribe = $this->createElement('submit', 'unsubscribe');
        $subscribe->setAttrib('class', 'btn');

        $hash = $this->createElement('hash', 'csrf_token_input_unsubscribe_question', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl);
        }

        $this->addElements([$subscribe, $hash]);

        return $this;
    }
}
