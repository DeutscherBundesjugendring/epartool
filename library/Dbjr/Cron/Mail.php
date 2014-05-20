<?php

class Dbjr_Cron_Mail extends Dbjr_Cron
{
    /**
     * Sends all unsent emails
     * @return boolena Indicate is the operation was a success
     */
    public function execute()
    {
        $mailModel = new Model_Mail();
        $emails = $mailModel->fetchAll($mailModel->select()->where('time_sent IS NULL'));

        foreach ($emails as $email) {
            $mailer = new Zend_Mail();
            $mailer
                ->setSubject($email->subject)
                ->setBodyHtml($email->body_html)
                ->setBodyText($email->body_text);
            foreach ($email->findModel_Mail_Recipient() as $recipient) {
                if ($recipient->type == Model_Mail_Recipient::TYPE_TO) {
                    $mailer->addTo($recipient->email, $recipient->name);
                } elseif ($recipient->type == Model_Mail_Recipient::TYPE_CC) {
                    $mailer->addCc($recipient->email, $recipient->name);
                } elseif ($recipient->type == Model_Mail_Recipient::TYPE_BCC) {
                    $mailer->addBcc($recipient->email, $recipient->name);
                }
            }
            $mailer->send();

            $mailModel->update(
                array('time_sent' => Zend_Date::now()->toString('YYYY-MM-dd HH:mm:ss')),
                array('id=?' => $email->id)
            );

            return true;
        }
    }
}
