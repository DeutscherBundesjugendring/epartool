<?php
/**
 * Input Form
 * Formular für Beiträge zu Fragen
 *
 */
class Default_Form_Input extends Zend_Form
{
    protected $_iniFile = '/modules/default/forms/Input.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        // set form-config
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $this->setDecorators(array('FormElements', 'Form'));

        // für alle per ini gesetzten Elemente:
        // nur die Dekoratoren ViewHelper, Errors und Description verwenden
        $this->setElementDecorators(array('ViewHelper', 'Errors', 'Description'));

        // hidden Felder brauchen nur den ViewHelper Dekorator:
        $this->setElementDecorators(array('ViewHelper'), array('submitmode'));

        // Element für zusätzliches Markup:
        $this->addElement('hidden', 'plaintext', array(
            'description' => '<span class="label sticker label-mitmachen-black hidden-print">Mitmachen</span>'
                . '<h2>Beiträge verfassen</h2>'
                . '<br />',
            'ignore' => true,
            'order' => 0, // Am Anfang des Formulars
            'decorators' => array(
                array('Description', array('escape'=>false, 'tag'=>'')),
            ),
        ));

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_input', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl);
        }
        $this->addElement($hash);

    }

    /**
     * Generate the dynamic fields for thes and expl
     *
     * @param array $theses Array of inputs that are already in the session
     */
    public function generate($theses = array())
    {
        $i = 0;
        if (!empty($theses)) {
            // add fields for every input from the session
            foreach ($theses as $thes_item) {

                // add dynamic elements

                $this->addDynamicThesFields($i, $thes_item);

                $i++;
            };
        }

        // add empty field for next new input
        $this->addDynamicThesFields($i);

        if ($i == 0) {
            // in the beginning if no input is written to session yet:
            // add another field
            $i++;
            $this->addDynamicThesFields($i);
        }

        $this->addDisplayGroup(
            array(
                $this->getElement('plus'),
                $this->getElement('submitbutton'),
                $this->getElement('finish')
            ),
            'controlgroup99',
            array(
                'Decorators' => array(
                        'FormElements',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'form-actions form-actions-unstyled text-center'))
                )
            )
        );
    }

    /**
     * Adds the needed number of input fields for the theses
     *
     * @param integer $i         Position of element
     * @param array   $thes_item
     */
    protected function addDynamicThesFields($i, $thes_item = array())
    {
        $thesCounter = $this->createElement('hidden', 'thes_' . $i . '_counter');
        $thesCounterOptions = array(
            'ignore' => true,
            'description' => '<span class="js-character-counter">Characters left: <span id="thes_' . $i . '_counter">0</span></span>'
        );
        $thesCounter->setOptions($thesCounterOptions);
        $thesCounter->setDecorators(array(array('Description', array('escape' => false, 'tag' => ''))));


        $thes = $this->createElement('textarea', 'thes_' . $i);
        $thesOptions = array(
            'label' => '',
            'cols' => 85,
            'rows' => 2,
            'required' => false,
            'belongsTo' => 'thes',
            //                     'isArray' => true,
            'attribs' => array(
                'class' => 'input-block-level input-extensible input-alt js-has-counter',
                'placeholder' => 'Hier könnt ihr euren Beitrag mit bis zu 300 Buchstaben schreiben',
                'id' => 'thes_' . $i,
                'maxlength' => '300',
            ),
            'filters' => array(
                'striptags' => 'StripTags',
                'htmlentities' => 'HtmlEntities',
            ),
        );
        $thes->setOptions($thesOptions);


        $toggle = $this->createElement('hidden', 'toggle_' . $i);
        $toggleOptions = array(
            'ignore' => true,
            'description' => '<a href="#" class="btn btn-block btn-small btn-extend js-toggle-extended-input">'
                . '<i class="icon-angle-down icon-large"></i> Klicken, um Eintrag zu erläutern <i class="icon-angle-down icon-large"></i>'
                . '</a>'
        );
        $toggle->setOptions($toggleOptions);
        $toggle->setDecorators(array(array('Description', array('escape' => false, 'tag' => ''))));


        $explCounter = $this->createElement('hidden', 'expl_' . $i . '_counter');
        $explCounterOptions = array(
            'ignore' => true,
            'description' => '<span class="js-character-counter" style="display: none">Characters left: <span id="expl_' . $i . '_counter">0</span></span>',
        );
        $explCounter->setOptions($explCounterOptions);
        $explCounter->setDecorators(array(array('Description', array('escape' => false, 'tag' => ''))));


        $expl = $this->createElement('textarea', 'expl_' . $i);
        $explOptions = array(
            'label' => '',
            'cols' => 85,
            'rows' => 5,
            'required' => false,
            'belongsTo' => 'expl',
            //                     'isArray' => true,
            'attribs' => array(
                'class' => 'extension input-block-level input-extensible input-alt js-has-counter',
                'style' => 'display: none;',
                'placeholder' => 'Hier könnt ihr euren Beitrag mit bis zu 2000 Buchstaben erläutern',
                'id' => 'expl_' . $i,
                'maxlength' => '2000'
            ),
            'filters' => array(
                'striptags' => 'StripTags',
                'htmlentities' => 'HtmlEntities',
            ),
        );
        $expl->setOptions($explOptions);


        if (!empty($thes_item)) {
            $thes->setValue($thes_item['thes']);
            $expl->setValue($thes_item['expl']);
        }

        $this->addDisplayGroup(
            array(
                $thesCounter,
                $thes,
                $toggle,
                $expl,
                $explCounter,
            ),
            'controlgroup' . $i,
            array(
                'Decorators' => array(
                    'FormElements',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'control-group'))
                )
            )
        );
    }
}
