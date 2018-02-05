<?php

class Default_Form_Input_Edit extends Dbjr_Form_Web
{
    /**
     * @var bool
     */
    protected $videoEnabled;

    /**
     * @var bool
     */
    protected $locationEnabled;

    /**
     * @var array
     */
    private $question;

    /**
     * @var Service_RequestInfo
     */
    private $requestInfoService;

    /**
     * @param Service_RequestInfo $requestInfoService
     * @param array|null $opriotns
     */
    public function __construct(Service_RequestInfo $requestInfoService, array $options = null)
    {
        parent::__construct($options);
        $this->requestInfoService = $requestInfoService;
    }

    public function init()
    {
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'input/inputEditForm.phtml'))));
        $translator = Zend_Registry::get('Zend_Translate');

        $this->setMethod('post');

        $thes = $this->createElement('textarea', 'thes');
        $placeholder = sprintf(
            $translator->translate('Here you can type in your contribution (up to %s characters).'),
            300
        );
        $thes
            ->setLabel('Contribution')
            ->setAttrib('cols', 85)
            ->setAttrib('rows', 3)
            ->setRequired(false)
            ->setAttrib('placeholder', $placeholder)
            ->setFilters(['StripTags'])
            ->addValidators(['NotEmpty']);
        $this->addElement($thes);

        $expl = $this->createElement('textarea', 'expl');
        $placeholder = sprintf($translator->translate('Here you explain your contribution more in depth, e.g. with examples (up to %s characters).'), 2000);
        $expl
            ->setLabel('Explanation')
            ->setAttrib('cols', 85)
            ->setAttrib('rows', 5)
            ->setAttrib('placeholder', $placeholder)
            ->setFilters(['StripTags']);
        $this->addElement($expl);

        $this->addElement('videoService', 'video_service');
        $this->addElement('videoId', 'video_id');

        $this->addElement('checkbox', 'location_enabled');

        $this->addElement('hidden', 'latitude');
        $this->getElement('latitude')->setAttrib('id', 'inputs-0-latitude');

        $this->addElement('hidden', 'longitude');
        $this->getElement('longitude')->setAttrib('id', 'inputs-0-longitude');

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-default btn-default-alt pull-right')
            ->setLabel('Save');
        $this->addElement($submit);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_inputedit', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }

    /**
     * @return bool
     */
    public function getVideoEnabled()
    {
        return $this->videoEnabled && (new Model_Projects())->getVideoServiceStatus();
    }

    /**
     * @param bool $videoEnabled
     * @return \Default_Form_Input_Edit
     */
    public function setVideoEnabled($videoEnabled)
    {
        $this->videoEnabled = $videoEnabled;
        return $this;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isValid($data)
    {
        $thesEl = $this->getElement('thes');
        $videoIdEl = $this->getElement('video_id');
        $thesEl->clearErrorMessages();
        if ($videoIdEl !== null) {
            $videoIdEl->clearErrorMessages();
        }
        if (empty($data['thes']) && empty($data['video_id'])) {
            $msg = Zend_Registry::get('Zend_Translate')->translate('Either text or video have to be submitted.');
            $thesEl->addError($msg);
            if ($videoIdEl !== null) {
                $videoIdEl->addError($msg);
            }
            $this->markAsError();
        }

        return parent::isValid($data);
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
     * @return \Default_Form_Input_Create
     */
    public function setLocationEnabled($locationEnabled)
    {
        $this->locationEnabled = $locationEnabled;

        return $this;
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
