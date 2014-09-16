<?php
/**
 * Email to send
 *
 * @description     Form new email to send
 * @author                Jan Suchandt
 */
class Admin_Form_Mail_Send extends Zend_Form
{
    protected $_iniFile = '/modules/admin/forms/Mail/Send.ini';

    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $consulModel = new Model_Consultations();
        $consultations = $consulModel
            ->fetchAll(
                $consulModel
                    ->select()
                    ->from($consulModel->getName(), array('kid', 'titl_short'))
            )
            ->toArray();
        foreach ($consultations as &$consultation) {
            $consultation['hasParticipant'] = $consulModel->hasParticipants($consultation['kid']);
            $consultation['hasNewsletter'] = $consulModel->hasNewsletterSubscribers($consultation['kid']);
            $consultation['hasFollowup'] = $consulModel->hasFollowupSubscribers($consultation['kid']);
            $consultation['hasVoter'] = $consulModel->hasVoters($consultation['kid']);
        }
        $consulElement = $this->getElement('mail_consultation');
        $consulElement->addMultiOption('0', 'Select');
        $consuls = array();
        foreach ($consultations as $cons) {
            $consulElement->addMultiOption($cons['kid'], $cons['titl_short']);
            $consuls[$cons['kid']] = $cons;
        }
        $consulElement->setAttrib('data-consultations', json_encode($consuls));


        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_mailsend', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }

    /**
     * Validates the form
     * @param  array   $data Data passed in by the user
     * @return boolean
     */
    public function isValid($data)
    {
        $isFormValid = parent::isValid($data);

        if (!$data['mailto']
            && !$data['mail_consultation_participant']
            && !$data['mail_consultation_voter']
            && !$data['mail_consultation_followup']
            && !$data['mail_consultation_newsletter']
        ) {
            $isRecipientValid = false;
            $this->getElement('mailto')->setErrors(array('Mail recipient or mail recipient group must be specified.'));
        } else {
            $isRecipientValid = true;
        }

        if ($data['mail_consultation']) {
            $consulModel = new Model_Consultations();
            $this->getElement('mail_consultation_participant')->setAttrib(
                'disabled',
                $consulModel->hasParticipants($data['mail_consultation']) ? null : true
            );
            $this->getElement('mail_consultation_followup')->setAttrib(
                'disabled',
                $consulModel->hasFollowupSubscribers($data['mail_consultation']) ? null : true
            );
            $this->getElement('mail_consultation_newsletter')->setAttrib(
                'disabled',
                $consulModel->hasNewsletterSubscribers($data['mail_consultation']) ? null : true
            );
            $this->getElement('mail_consultation_voter')->setAttrib(
                'disabled',
                $consulModel->hasVoters($data['mail_consultation']) ? null : true
            );
        }


        if ($isFormValid && $isRecipientValid) {
            return true;
        } else {
            return false;
        }
    }
}
