<?php

class Admin_Form_Question extends Dbjr_Form_Admin
{

    public function init()
    {
        $view = new Zend_View();
        $this->setMethod('post');

        $order = $this->createElement('text', 'nr');
        $order
            ->setLabel('Order')
            ->setRequired(true)
            ->setAttrib('maxlength', 4);
        $this->addElement($order);

        $desc = sprintf($view->translate('Max %d characters'), 300);
        $question = $this->createElement('text', 'q');
        $question
            ->setLabel('Question')
            ->setRequired(true)
            ->setAttrib('maxlength', 300)
            ->setDescription($desc);
        $this->addElement($question);

        $expl = $this->createElement('textarea', 'q_xpl');
        $expl
            ->setLabel('Explanation')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD)
            ->setAttrib('rows', 5);
        $this->addElement($expl);

        $desc = sprintf($view->translate('Max %d characters'), 300);
        $votingQuestion = $this->createElement('text', 'vot_q');
        $votingQuestion
            ->setLabel('Voting question')
            ->setAttrib('maxlength', 300)
            ->setDescription($desc);
        $this->addElement($votingQuestion);


        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_question', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }
}
