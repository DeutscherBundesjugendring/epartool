<?php

class Admin_Form_Voting_Rights extends Dbjr_Form_Admin
{
    /**
     * @var int
     */
    private $consultationId;

    /**
     * Admin_Form_Voting_Rights constructor.
     * (Change parent constructor signature is not a best practise, but it is a good solution for zend form customization)
     * @param int $consultationId
     * @param array|Zend_Config $options
     */
    public function __construct($consultationId, $options = null)
    {
        $this->consultationId = $consultationId;
        parent::__construct($options);
    }

    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $formSettings = (new Model_Consultations())->find($this->consultationId)->current()->toArray();

        if ($formSettings['allow_groups']) {
            $weight = $this->createElement('text', 'vt_weight');
            $weight
                ->setLabel('Weight')
                ->setRequired(true)
                ->addValidator('Int');
            $this->addElement($weight);

            $groupSize = $this->createElement('select', 'grp_siz');
            $groupSize
                ->setLabel('Group size')
                ->setMultiOptions(
                    [
                        '0' => '?',
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

        $accessCode = $this->createElement('text', 'vt_code');
        $accessCode
            ->setLabel('Access code')
            ->setRequired(true)
            ->addValidator('Alnum')
            ->addValidator('StringLength', false, ['min' => 8]);
        $this->addElement($accessCode);

        $groupSizeUser = $this->createElement('text', 'group_size_user');
        $groupSizeUser
            ->setLabel('Size according to user')
            ->setAttrib('disabled', 'disabled');
        $this->addElement($groupSizeUser);

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
