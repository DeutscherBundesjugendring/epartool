<?php

class Default_Form_VotesConfirmation extends Dbjr_Form_Web
{

    public function init()
    {
        $confirm = $this->createElement('submit', 'confirm');
        $confirm
            ->setAttrib('class', 'btn-success')
            ->setAttrib('id', '')
            ->setLabel('Confirm');
        $this->addElement($confirm);

        $reject = $this->createElement('submit', 'reject');
        $reject
            ->setAttrib('class', 'btn-danger')
            ->setAttrib('id', '')
            ->setLabel('Cancel votes');
        $this->addElement($reject);

        $hash = $this->createElement('hash', 'csrf_token_votesConfirmation', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
