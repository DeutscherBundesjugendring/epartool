<?php

class Default_Form_Input_Create extends Dbjr_Form_Web
{
    public function init()
    {
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
            ->setAttrib('class', 'btn-default btn-default-alt')
            ->setAttrib('type', 'submit')
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
    public function generateInputFields($theses)
    {
        if (!$theses) {
            $theses = [['thes' => '', 'expl' => '', 'video_service' => '', 'video_id' => '']];
        }

        foreach ($theses as $inputNum => $input) {
            $this->addInputField(
                $inputNum,
                $input['thes'],
                $input['expl'],
                $input['video_service'],
                $input['video_id']
            );
        }

        $this->addInputField(isset($inputNum) ? $inputNum + 1 : 0);

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
     * @return Default_Form_Input_Create              Fluent interface
     */
    protected function addInputField($inputName, $thes = null, $expl = null, $videoService = null, $videoId = null)
    {
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
        $thesEl = $this->createElement('textarea', 'thes');
        $thesEl
            ->setOptions($thesElOpts)
            ->setValue($thes);

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
        $explEl = $this->createElement('textarea', 'expl');
        $explEl
            ->setOptions($explElOpts)
            ->setValue($expl);

        $videoServiceEl = $this->createElement('select', 'video_service');
        $videoServiceOptions = ['youtube' => 'Youtube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook'];
        $videoServiceEl->setMultioptions($videoServiceOptions)->setOptions([
            'belongsTo' => 'inputs[' . $inputName . ']',
            'data-url' => json_encode([
            'youtube' => sprintf(Zend_Registry::get('systemconfig')->video->url->youtube->format->link, ''),
            'vimeo' => sprintf(Zend_Registry::get('systemconfig')->video->url->vimeo->format->link, ''),
            'facebook' => sprintf(Zend_Registry::get('systemconfig')->video->url->facebook->format->link, ''),
            ])
        ])->setValue($videoService);

        $videoIdEl = $this->createElement('text', 'video_id');
        $videoIdEl->addValidator(new Dbjr_Validate_VideoValidator());
        $videoIdEl->setOptions(['belongsTo' => 'inputs[' . $inputName . ']'])->setValue($videoId);

        if (!$this->getSubForm('inputs')) {
            $this->addSubForm(new Zend_Form(), 'inputs');
        }
        $inputForm = new Zend_Form();
        $inputForm->addElement($explEl);
        $inputForm->addElement($thesEl);
        $inputForm->addElement($videoServiceEl);
        $inputForm->addElement($videoIdEl);
        $this->getSubForm('inputs')->addSubForm($inputForm, $inputName);

        return $this;
    }
}
