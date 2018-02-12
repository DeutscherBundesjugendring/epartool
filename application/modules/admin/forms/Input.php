<?php

class Admin_Form_Input extends Dbjr_Form_Admin
{
    const AFTER_SUBMIT_RETURN_TO_INDEX = 'after_submit_return_to_index';
    const AFTER_SUBMIT_SPLIT_NEXT = 'after_submit_split_next';
    const AFTER_SUBMIT_VIEW_COPY = 'after_submit_view_copy';

    /**
     * @var string
     */
    protected $cancelUrl;

    /**
     * @var bool
     */
    protected $videoEnabled;

    /**
     * @var bool
     */
    protected $locationEnabled;

    /**
     * @var string
     */
    private $afterSubmitAction;

    /**
     * @var bool
     */
    private $autoVotingInfo;

    /**
     * @var array
     */
    private $question;

    /**
     * @var Service_RequestInfo
     */
    private $requestInfoService;

    /**
     * Admin_Form_Input constructor.
     * @param Service_RequestInfo
     * @param null $cancelUrl
     * @param string $afterSubmitAction
     */
    public function __construct(
        Service_RequestInfo $requestInfoService,
        $cancelUrl = null,
        $afterSubmitAction = self::AFTER_SUBMIT_RETURN_TO_INDEX
    ) {
        $this->requestInfoService = $requestInfoService;
        $this->cancelUrl = $cancelUrl;
        $this->afterSubmitAction = $afterSubmitAction;
        parent::__construct();
    }

    public function init()
    {
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'input/inputForm.phtml'))));

        $kid = Zend_Controller_Front::getInstance()->getRequest()->getParam('kid', 0);
        $translator = Zend_Registry::get('Zend_Translate');

        $this
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => $this->cancelUrl]);

        $selectOptions = (new Model_Questions())->getAdminInputFormSelectOptions($kid);
        $questionId = $this->createElement('select', 'qi');
        $questionId
            ->setLabel('Question')
            ->setRequired(true)
            ->setMultiOptions($selectOptions)
            ->addValidator('Int');
        $this->addElement($questionId);

        $thes = $this->createElement('textarea', 'thes');
        $thes
            ->setLabel('Thesis')
            ->addValidator(new Zend_Validate_StringLength(array('min' => 1, 'max' => 300)))
            ->setRequired(true)
            ->setAttrib('rows', 5);
        $this->addElement($thes);

        $expl = $this->createElement('textarea', 'expl');
        $expl
            ->setLabel('Explanation')
            ->addValidator(new Zend_Validate_StringLength(array('min' => 0, 'max' => 2000)))
            ->setAttrib('rows', 5);
        $this->addElement($expl);

        $multiOptions = (new Model_Tags())->getAdminInputFormMulticheckboxOptions();
        $tags = $this->createElement('multiselect', 'tags')
            ->setLabel('Tags')
            ->setMultiOptions($multiOptions)
            ->setSeparator(' ')
            ->setIsSelect2(true);
        $this->addElement($tags);

        $userConfirmation = $this->createElement('radio', 'is_confirmed_by_user');
        $userConfirmation
            ->setLabel('User confirmation')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    null => $translator->translate('Unknown'),
                    '1' => $translator->translate('Confirmed'),
                    '0' => $translator->translate('Rejected'),
                ]
            );
        $this->addElement($userConfirmation);

        $adminConfirmation = $this->createElement('radio', 'is_confirmed');
        $adminConfirmation
            ->setLabel('Admin confirmation')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    null => $translator->translate('Unknown'),
                    '1' => $translator->translate('Confirmed'),
                    '0' => $translator->translate('Blocked'),
                ]
            );
        $this->addElement($adminConfirmation);

        $enableVoting = $this->createElement('radio', 'is_votable');
        $enableVoting
            ->setLabel('Enable voting')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    null => $translator->translate('Unknown'),
                    '0' => $translator->translate('No'),
                    '1' => $translator->translate('Yes'),
                ]
            );
        $this->addElement($enableVoting);

        $this->addElement('hidden', 'is_votable_edited');

        $note = $this->createElement('textarea', 'notiz');
        $note
            ->setLabel('Internal note')
            ->setAttrib('rows', 5);
        $this->addElement($note);

        $this->addElement('videoService', 'video_service');
        $this->addElement('videoId', 'video_id');

        $this->addElement('hidden', 'latitude');
        $this->addElement('hidden', 'longitude');

        // CSRF Protection
        $hash = $this->getHash();
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit->setAttrib('class', 'btn-primary btn-raised');
        if ($this->afterSubmitAction === self::AFTER_SUBMIT_RETURN_TO_INDEX) {
            $submit->setLabel('Save and return to index');
        } elseif ($this->afterSubmitAction === self::AFTER_SUBMIT_SPLIT_NEXT) {
            $submit->setLabel('Save and continue');
        } elseif ($this->afterSubmitAction === self::AFTER_SUBMIT_VIEW_COPY) {
            $submit->setLabel('Save and view copy');
        }
        $this->addElement($submit);

        $submit = $this->createElement('submit', 'delete');
        $submit->setAttrib('class', 'btn-danger btn-raised');
        $submit->setAttrib('data-toggle', 'confirm');
        $submit->setAttrib('data-confirm-message', $this->getTranslator()->translate('Are you sure?'));
        $submit->setAttrib('data-confirm-yes', $this->getTranslator()->translate('Yes'));
        $submit->setAttrib('data-confirm-no', $this->getTranslator()->translate('No'));
        $submit->setLabel('Delete');
        $this->addElement($submit);
    }

    /**
     * @return \Zend_Form_Element
     * @throws \Zend_Exception
     * @throws \Zend_Form_Exception
     */
    public function getHash()
    {
        $hash = $this->createElement('hash', 'csrf_token_inputadmin', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        return $hash;
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }


    /**
     * @return bool
     */
    public function getVideoEnabled()
    {
        return $this->videoEnabled;
    }

    /**
     * @param bool $videoEnabled
     * @return \Admin_Form_Input
     */
    public function setVideoEnabled($videoEnabled)
    {
        $this->videoEnabled = $videoEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function getLocationEnabled()
    {
        return $this->locationEnabled;
    }

    /**
     * @param bool $locationEnabled
     * @return \Admin_Form_Input
     */
    public function setLocationEnabled($locationEnabled)
    {
        $this->locationEnabled = $locationEnabled;

        return $this;
    }

    public function isValid($data)
    {
        if (isset($data['video_id']) && $data['video_id']) {
            $this->getElement('thes')->setRequired(false);
        }

        foreach (['is_confirmed_by_user', 'is_confirmed', 'is_votable'] as $element) {
            if (array_key_exists($element, $data)) {
                $this->getElement($element)->setRequired(false);
            }
        }

        return parent::isValid($data);
    }

    /**
     * @return Admin_Form_Input
     */
    public function setAutoVotingInfo($status)
    {
        $this->autoVotingInfo = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoVotingInfo()
    {
        return $this->autoVotingInfo;
    }

    /**
     * @return array
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param array $question
     * @return \Admin_Form_Input
     */
    public function setQuestion(array $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConnectionSecured()
    {
        return $this->requestInfoService->isSecure();
    }
}
