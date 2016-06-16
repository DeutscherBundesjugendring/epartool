<?php

class Default_Form_VotesConfirmation extends Dbjr_Form_Web
{

    public function init()
    {
        $this
            ->setMethod('post');

        $confirm = $this->createElement('submit', 'confirm');
        $confirm
            ->setAttrib('class', 'btn-success')
            ->setLabel('Confirm');
        $this->addElement($confirm);

        $reject = $this->createElement('submit', 'reject');
        $reject
            ->setAttrib('class', 'btn-danger')
            ->setLabel('Delete');
        $this->addElement($reject);

        $hash = $this->createElement('hash', 'csrf_token_votesConfirmation', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
