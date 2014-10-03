<?php

class Admin_Form_Voting_Participantedit extends Dbjr_Form_Admin
{
    public function init()
    {

        $this->setMethod('post');

        $note = $this->createElement('note', 'description');
        $note
            ->setLabel('Mit folgendem Teilnehmer zusammenlegen:')
            ->setValue('Die Daten des auszuwählenden Teilnehmers werden dem zu bearbeitenden Teilnehmer hinzugefügt. <br />Der hier auszuwählende Teilnehmer wird anschließend gelöscht.');
        $this->addElement($note);

        $merge = $this->createElement('select', 'merge');
        $merge
            ->setLabel('Bitte hier den zu löschenden Teilnehmer wählen')
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
        $submit->setLabel('Save');
        $this->addElement($submit);
    }
}
