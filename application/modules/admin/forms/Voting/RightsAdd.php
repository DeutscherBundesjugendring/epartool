<?php

class Admin_Form_Voting_RightsAdd extends Dbjr_Form_Admin
{
    /**
     * @var array
     */
    private $userOptions;

    /**
     * @var array
     */
    private $consultation;

    /**
     * Admin_Form_Voting_RightsAdd constructor.
     * @param array $consultation
     * @param array $users
     * @param null $options
     */
    public function __construct($consultation, $users, $options = null)
    {
        $this->consultation = $consultation;
        $this->userOptions = [];
        foreach ($users as $user) {
            $this->userOptions[$user['uid']] = '';
            if ($user['name'] !== null) {
                $this->userOptions[$user['uid']] .= $user['name'];
            }
            if (!empty($user['email'])) {
                if (!empty($this->userOptions[$user['uid']])) {
                    $this->userOptions[$user['uid']] .= ' <' . $user['email'] . '>';
                } else {
                    $this->userOptions[$user['uid']] .= $user['email'];
                }
            }
        }
        parent::__construct($options);
    }

    public function init()
    {
        $consultation = $this->createElement('hidden', 'kid');
        $this->addElement($consultation);

        $user = $this->createElement('select', 'uid');
        $user
            ->setLabel('User')
            ->setRequired(true)
            ->setAttrib('data-onload-select2', '{}')
            ->setMultiOptions($this->userOptions);
        $this->addElement($user);

        if ($this->consultation['allow_groups']) {
            $weight = $this->createElement('text', 'vt_weight');
            $weight
                ->setLabel('Weight')
                ->setRequired(true)
                ->addValidator('Int');
            $this->addElement($weight);

            $groupSize = $this->createElement('select', 'grp_siz');
            $groupSize
                ->setLabel('Group size')
                ->setMultiOptions((new Model_GroupSize())->getOptionsByConsultation($this->consultation['kid']));
            $this->addElement($groupSize);
        } else {
            $weight = $this->createElement('hidden', 'vt_weight');
            $weight->setValue(1);
            $this->addElement($weight);
        }

        $accessCode = $this->createElement('text', 'vt_code');
        $accessCode
            ->setLabel('Access code')
            ->setRequired(true)
            ->addValidator('Alnum')
            ->addValidator('StringLength', false, ['min' => 8])
            ->addValidator(new Zend_Validate_Db_NoRecordExists(
                [
                    'table' => 'vt_rights',
                    'field' => 'vt_code',
                ]
            ));
        $this->addElement($accessCode);

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
