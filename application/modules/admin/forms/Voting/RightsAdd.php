<?php

class Admin_Form_Voting_RightsAdd extends Dbjr_Form_Admin
{
    /**
     * @var array
     */
    private $userOptions;

    public function __construct($users)
    {
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
        parent::__construct();
    }

    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $formSettings = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current()->toArray();

        $this->setMethod('post');

        $consultation = $this->createElement('hidden', 'kid');
        $this->addElement($consultation);

        $user = $this->createElement('select', 'uid');
        $user
            ->setLabel('User')
            ->setAttrib('data-onload-select2', '{}')
            ->setMultiOptions($this->userOptions);
        $this->addElement($user);

        if ($formSettings['allow_groups']) {
            $weight = $this->createElement('text', 'vt_weight');
            $weight
                ->setLabel('Weight')
                ->setRequired(true)
                ->addValidator('Int');
            $this->addElement($weight);
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

        if ($formSettings['allow_groups']) {
            $groupSize = $this->createElement('select', 'grp_siz');
            $groupSize
                ->setLabel('Group size')
                ->setMultiOptions(
                    [
                        '1' => '1-2',
                        '10' => $translator->translate('bis') . ' 10',
                        '30' => $translator->translate('bis') . ' 30',
                        '80' => $translator->translate('bis') . ' 80',
                        '150' => $translator->translate('bis') . ' 150',
                        '200' => $translator->translate('über') . ' 150',
                    ]
                );
            $this->addElement($groupSize);
        }

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
