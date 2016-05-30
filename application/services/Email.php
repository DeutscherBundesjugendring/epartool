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

    /**
     * Sends all unsent emails
     * @return Service_Email   Provides fluent interface
     */
    public function sendQueued()
    {
        $mailModel = new Model_Mail();
        $emails = $mailModel->fetchAll(
            $mailModel
                ->select()
                ->where('time_sent IS NULL')
                ->where('project_code=?', Zend_Registry::get('systemconfig')->project)
        );

        foreach ($emails as $email) {
            $mailer = new Zend_Mail('UTF-8');
            $mailer
                ->setSubject($email->subject)
                ->setBodyHtml($email->body_html)
                ->setBodyText($email->body_text);
            foreach ($email->findModel_Mail_Attachment() as $attachment) {
                $atFilepathArr = explode('/', $attachment->filepath);
                $at = $mailer->createAttachment(file_get_contents(MEDIA_PATH . $attachment->filepath));
                $at->type = mime_content_type(MEDIA_PATH . '/' . $attachment->filepath);
                $at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $at->filename = end($atFilepathArr);
            }
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
        }

        return $this;
    }
}
