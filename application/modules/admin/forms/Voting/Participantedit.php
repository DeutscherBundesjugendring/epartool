<?php

class Admin_Form_Voting_Participantedit extends Dbjr_Form_Admin
{
    protected $_kid;

    public function __construct($kid = null)
    {
        $this->_kid = $kid;
        parent::__construct();
    }

    public function init()
    {

        $this
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/voting/participants/kid/' . $this->_kid]);

        $merge = $this->createElement('select', 'merge');
        $merge
            ->setLabel('Select a participant')
            ->setRequired(true);
        $this->addElement($merge);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_votingrights', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save');
        $this->addElement($submit);
    }
}
