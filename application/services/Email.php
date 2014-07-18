<?php

class Service_Email
{
    /**
     * Queues an email for later send
     * @param  Dbjr_Mail     $mail The properly initiated email object to be used as source of data
     * @return Service_Email       Fluent interface
     */
    public function queueForSend(Dbjr_Mail $mail)
    {
        $data = $mail->getEmailData();
        (new Model_Mail())->insert($data);

        return $this;
    }
}
