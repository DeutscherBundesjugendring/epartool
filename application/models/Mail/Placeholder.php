<?php

class Model_Mail_Placeholder extends Dbjr_Db_Table_Abstract
{
    const GLOBAL_PLACEHOLDER_FROM_NAME = 'from_name';
    const GLOBAL_PLACEHOLDER_FROM_ADDRESS = 'from_address';
    const GLOBAL_PLACEHOLDER_CONTACT_NAME = 'contact_name';
    const GLOBAL_PLACEHOLDER_CONTACT_WWW = 'contact_www';
    const GLOBAL_PLACEHOLDER_CONTACT_EMAIL = 'contact_email';
    const GLOBAL_PLACEHOLDER_SEND_DATE = 'send_date';

    protected $_name = 'email_placeholder';
    protected $_primary = 'id';

    /**
     * @param string $name
     * @throws \Exception
     * @throws \Zend_Exception
     * @return mixed
     */
    public static function getPlaceholderInfo($name)
    {
        $translator = Zend_Registry::get('Zend_Translate');
        $descriptions = [
            'voter_email' => $translator->translate('The email of the original voter.'),
            'to_name' => $translator->translate('The name of the recipient. If the name is not known, teh value of {{to_email}} is used.'),
            'to_email' => $translator->translate('The email address of the recipient.'),
            'password_reset_url' => $translator->translate('The url where user can reset their password.'),
            'confirmation_url' => $translator->translate('The confirmation link for the user to visit.'),
            'rejection_url' => $translator->translate('The rejection link for the user to visit.'),
            'consultation_title_short' => $translator->translate('The short version of the consultation title.'),
            'consultation_title_long' => $translator->translate('The long version of the consultation title.'),
            'input_phase_end' => $translator->translate('The end of the input phase.'),
            'input_phase_start' => $translator->translate('The start of the input phase.'),
            'voting_phase_end' => $translator->translate('The end of the voting phase.'),
            'voting_phase_start' => $translator->translate('The start of the voting phase.'),
            'inputs_html' => $translator->translate('The users inputs in html formatting.'),
            'inputs_text' => $translator->translate('The users inputs in plain text formatting.'),
            'voting_weight' => $translator->translate('The voting weight of the relevant user.'),
            'voting_url' => $translator->translate('the url where voting takes place.'),
            'group_category' => $translator->translate('The type of the relevant group'),
            'from_name' => $translator->translate('The name of the sender.'),
            'from_address' => $translator->translate('The email address of the sender.'),
            'contact_name' => $translator->translate('The name from the contact info.'),
            'contact_www' => $translator->translate('The www from the contact info.'),
            'contact_email' => $translator->translate('The email address from the contact info.'),
            'send_date' => $translator->translate('The date the email was send'),
            'website_url' => $translator->translate('Link to the relevant page on the website.'),
            'question_text' => $translator->translate('The number and the text of the relevant question.'),
            'unsubscribe_url' => $translator->translate('Link to remove user from the relevant subscription or mailing list.'),
            'contribution_text' => $translator->translate('The text of the contribution.'),
            'input_thes' => $translator->translate('The theses part of the input.'),
            'input_expl' => $translator->translate('The explanation part of the input.'),
            'video_url' => $translator->translate('Link to the video contribution.'),
        ];

        if (!isset($descriptions[$name])) {
            throw new \Exception(sprintf('Description of placeholder %s is not defined.', $name));
        }

        return $descriptions[$name];
    }

    /**
     * @param string $templateName
     * @return \Zend_Db_Table_Rowset_Abstract
     * @throws \Zend_Db_Table_Exception
     */
    public function getByTemplateName($templateName)
    {
        $select = $this
            ->select(true)
            ->from(['mp' => $this->_name])
            ->setIntegrityCheck(false)
            ->joinLeft(
                ['ethetp' => 'email_template_has_email_placeholder'],
                'ethetp.email_placeholder_id = mp.id',
                []
            )
            ->joinLeft(
                ['mt' => (new Model_Mail_Template())->info(Model_Mail_Template::NAME)],
                'ethetp.email_template_id = mt.id',
                []
            )
            ->where('mp.is_global=?', true)
            ->orWhere('mt.name=?', $templateName)
            ->group('mp.name')
            ->order('mp.name');

        return $this->fetchAll($select);
    }

    /**
     * Returns global placeholder values
     * @return array   The placeholders in array [name => value]
     */
    public function getGlobalValues()
    {
        $project = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current();

        $mailer = new Zend_Mail();
        return [
            self::GLOBAL_PLACEHOLDER_FROM_NAME => $mailer->getDefaultFrom()['name'],
            self::GLOBAL_PLACEHOLDER_FROM_ADDRESS => $mailer->getDefaultFrom()['email'],
            self::GLOBAL_PLACEHOLDER_CONTACT_NAME => $project['contact_name'],
            self::GLOBAL_PLACEHOLDER_CONTACT_WWW => $project['contact_www'],
            self::GLOBAL_PLACEHOLDER_CONTACT_EMAIL => $project['contact_email'],
            self::GLOBAL_PLACEHOLDER_SEND_DATE => Zend_Date::now()->get(Zend_Date::DATE_MEDIUM),
        ];
    }
}
