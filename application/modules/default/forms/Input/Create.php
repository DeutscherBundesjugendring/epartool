<?php

class Default_Form_Input_Create extends Dbjr_Form_Web
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
    protected $question;

    /**
     * @var Service_RequestInfo
     */
    private $requestInfoService;

    /**
     * @param Service_RequestInfo $requestInfoService
     * @param array|null $options
     */
    public function __construct(Service_RequestInfo $requestInfoService, array $options = null)
    {
        parent::__construct($options);
        $this->requestInfoService = $requestInfoService;
    }

    public function init()
    {
        $this->setAttrib('class', 'js-contribution-create-form');
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'input/createForm.phtml'))));

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_input', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $addInputFieldBtn = $this->createElement('button', 'add_input_field');
        $addInputFieldBtn
            ->setAttrib('class', 'btn-default btn-default-alt js-add-contribution')
            ->setAttrib('type', 'button')
            ->setLabel('+');
        $this->addElement($addInputFieldBtn);

        $nextQuestionBtn = $this->createElement('button', 'next_question');
        $nextQuestionBtn
            ->setAttrib('class', 'btn-default btn-default-alt')
            ->setAttrib('type', 'submit')
            ->setLabel('Save and proceed');
        $this->addElement($nextQuestionBtn);

        $finishedBtn = $this->createElement('button', 'finished');
        $finishedBtn
            ->setAttrib('class', 'btn-default btn-default-alt')
            ->setAttrib('type', 'submit')
            ->setLabel('Finish');
        $this->addElement($finishedBtn);
    }

    /**
     * Generates the input subForms.
     * @param array                        $theses Array of inputs that are already in the session
     * @return Default_Form_Input_Create           Fluent interface
     */
    public function generateInputFields(array $theses, $addExtraField = true)
    {
        if (!count($theses)) {
            $theses = [['thes' => '', 'expl' => '', 'video_service' => '', 'video_id' => '']];
        }

        foreach ($theses as $inputNum => $input) {
            $this->addInputField(
                $inputNum,
                $input['thes'],
                $input['expl'],
                isset($input['video_service']) ? $input['video_service'] : null,
                isset($input['video_id']) ? $input['video_id'] : null,
                isset($input['tid']) ? $input['tid'] : null,
                isset($input['latitude']) ? $input['latitude'] : null,
                isset($input['longitude']) ? $input['longitude'] : null,
                isset($input['location_enabled']) ? $input['location_enabled'] : true
            );
        }

        if ($addExtraField) {
            $this->addInputField(isset($inputNum) ? $inputNum + 1 : 0);
        }

        return $this;
    }

    /**
     * Adds a subForm with elements related to single input to the inputs subForm
     * If the inputs subForm doesnt exist it is created
     * @param  string $inputName The name of the input subgroup to be created
     * @param  string $videoService
     * @param  string $videoId
     * @param  string $thes
     * @param  string $expl
     * @param  int $tid
     * @param  float $latitude
     * @param  float $longitude
     * @param  bool $locationEnabled
     * @return Default_Form_Input_Create              Fluent interface
     */
    protected function addInputField(
        $inputName,
        $thes = null,
        $expl = null,
        $videoService = null,
        $videoId = null,
        $tid = null,
        $latitude = null,
        $longitude = null,
        $locationEnabled = true
    ) {
        $view = new Zend_View();
        $thesElOpts = array(
            'cols' => 85,
            'rows' => 3,
            'belongsTo' => 'inputs[' . $inputName . ']',
            'attribs' => array(
                'class' => 'form-control form-control-alt js-has-counter',
                'placeholder' => sprintf($view->translate('Here you can type in your contribution (up to %s characters).'), 300),
                'maxlength' => '300',
            ),
            'filters' => array(
                'striptags' => 'StripTags',
            ),
        );

        $explElOpts = array(
            'cols' => 85,
            'rows' => 5,
            'belongsTo' => 'inputs[' . $inputName . ']',
            'attribs' => array(
                'class' => 'form-control form-control-alt js-has-counter',
                'style' => 'display: none;',
                'placeholder' => sprintf($view->translate('Here you explain your contribution more in depth, e.g. with examples (up to %s characters).'), 2000),
                'maxlength' => '2000'
            ),
            'filters' => array(
                'striptags' => 'StripTags',
            ),
        );

        $videoElOpts = array(
            'attribs' => array(
                'class' => 'form-control form-control-alt',
            ),
        );

        if (!$this->getSubForm('inputs')) {
            $this->addSubForm(new Zend_Form(), 'inputs');
        }
        $inputForm = new Zend_Form();
        $inputForm->addPrefixPath('Dbjr_Form_Element', 'Dbjr/Form/Element/', 'element');

        $inputForm->addElement('videoService', 'video_service');
        $inputForm->getElement('video_service')->setOptions(['belongsTo' => 'inputs[' . $inputName . ']'])
            ->setOptions($videoElOpts)
            ->setValue($videoService)
            ->setLabel('Video:')
            ->setDecorators(array(array("Label",array("class"=>"control-label offset-right")),"ViewHelper"));

        $inputForm->addElement('checkbox', 'location_enabled');
        $inputForm->getElement('location_enabled')->setOptions(['belongsTo' => 'inputs[' . $inputName . ']'])
            ->setValue($locationEnabled);

        $inputForm->addElement('videoId', 'video_id');
        $inputForm->getElement('video_id')->setOptions(['belongsTo' => 'inputs[' . $inputName . ']'])
            ->setOptions($videoElOpts)
            ->setValue($videoId)
            ->setLabel('Video ID')
            ->setAttrib('placeholder', $view->translate('Please paste video URL'))
            ->setDecorators(array(array("Label",array("class"=>"sr-only")),"ViewHelper"));

        $inputForm->addElement('textarea', 'expl');
        $inputForm->getElement('expl')
            ->setOptions($explElOpts)
            ->setValue($expl)
            ->setLabel('Explain your contribution')
            ->setDecorators(array(array("Label",array("class"=>"sr-only")),"ViewHelper"));

        $inputForm->addElement('textarea', 'thes');
        $inputForm->getElement('thes')
            ->setOptions($thesElOpts)
            ->setValue($thes)
            ->setLabel('Your contribution')
            ->setDecorators(array(array("Label",array("class"=>"sr-only")),"ViewHelper"));

        $inputForm->addElement('hidden', 'tid');
        $inputForm->getElement('tid')->setOptions(['belongsTo' => 'inputs[' . $inputName . ']'])->setValue($tid);

        $inputForm->addElement('hidden', 'latitude');
        $inputForm->getElement('latitude')->setOptions(['belongsTo' => 'inputs[' . $inputName . ']'])->setValue($latitude);

        $inputForm->addElement('hidden', 'longitude');
        $inputForm->getElement('longitude')->setOptions(['belongsTo' => 'inputs[' . $inputName . ']'])->setValue($longitude);

        $this->getSubForm('inputs')->addSubForm($inputForm, $inputName);

        return $this;
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
     * @return \Default_Form_Input_Create
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
     * @return $this
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
