<?php

class Default_Form_Input_Create extends Zend_Form
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
            ->setAttrib('class', 'btn pull-left')
            ->setAttrib('type', 'submit')
            ->setLabel('+');
        $this->addElement($addInputFieldBtn);

        $nextQuestionBtn = $this->createElement('button', 'next_question');
        $nextQuestionBtn
            ->setAttrib('class', 'btn arrow-right')
            ->setAttrib('type', 'submit')
            ->setLabel('Speichern und nächste Frage');
        $this->addElement($nextQuestionBtn);

        $finishedBtn = $this->createElement('button', 'finished');
        $finishedBtn
            ->setAttrib('class', 'btn pull-right')
            ->setAttrib('type', 'submit')
            ->setLabel('Beenden');
        $this->addElement($finishedBtn);
    }

    /**
     * Generates the input subForms.
     * @param array $theses Array of inputs that are already in the session
     */
    public function generateInputFields($theses)
    {
        if (!$theses) {
            $theses = [['thes' => '', 'expl' => '']];
        }

        foreach ($theses as $inputNum => $input) {
            $this->addInputField($inputNum, $input['thes'], $input['expl']);
        }

        $this->addInputField(isset($inputNum) ? $inputNum + 1 : 0);
    }

    /**
     * Adds a subForm with elements related to single input to the inputs subForm
     * If the inputs subForm doesnt exist it is created
     * @param string $inputName The name of the input subgroup to be created
     */
    protected function addInputField($inputName, $thes = null, $expl = null)
    {
        $thesElOpts = array(
            'cols' => 85,
            'rows' => 2,
            'belongsTo' => 'inputs[' . $inputName . ']',
            'attribs' => array(
                'class' => 'input-block-level input-extensible input-alt js-has-counter',
                'placeholder' => 'Hier könnt ihr euren Beitrag mit bis zu 300 Buchstaben schreiben',
                'maxlength' => '300',
            ),
            'filters' => array(
                'striptags' => 'StripTags',
                'htmlentities' => 'HtmlEntities',
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
                'class' => 'extension input-block-level input-extensible input-alt js-has-counter',
                'style' => 'display: none;',
                'placeholder' => 'Hier könnt ihr euren Beitrag mit bis zu 2000 Buchstaben erläutern',
                'maxlength' => '2000'
            ),
            'filters' => array(
                'striptags' => 'StripTags',
                'htmlentities' => 'HtmlEntities',
            ),
        );
        $explEl = $this->createElement('textarea', 'expl');
        $explEl
            ->setOptions($explElOpts)
            ->setValue($expl);

        if (!$this->getSubForm('inputs')) {
            $this->addSubForm(new Zend_Form(), 'inputs');
        }
        $inputForm = new Zend_Form();
        $inputForm->addElement($explEl);
        $inputForm->addElement($thesEl);
        $this->getSubForm('inputs')->addSubForm($inputForm, $inputName);
    }
}
