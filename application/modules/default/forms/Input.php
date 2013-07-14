<?php
/**
 * Input Form
 * Formular für Beiträge zu Fragen
 *
 */
class Default_Form_Input extends Zend_Form {
  protected $_iniFile = '/modules/default/forms/Input.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

    $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/input/save');

    $this->setDecorators(array('FormElements', 'Form'));

    // für alle per ini gesetzten Elemente:
    // nur die Dekoratoren ViewHelper, Errors und Description verwenden
    $this->setElementDecorators(array('ViewHelper', 'Errors', 'Description'));

    // hidden Felder brauchen nur den ViewHelper Dekorator:
    $this->setElementDecorators(array('ViewHelper'), array('submitmode'));

    // Element für zusätzliches Markup:
    $this->addElement('hidden', 'plaintext', array(
      'description' => '<span class="label sticker label-mitmachen-black">Mitmachen</span>'
        . '<h2>Beiträge verfassen</h2>'
        . '<p>Mit der [+]-Schaltfläche kannst du weitere Felder hinzufügen, wenn du mehr als einen Vorschlag/Beitrag schreiben möchtest. Bei „Beenden“ kannst du eine E-Mail-Adresse hinterlegen: So können wir nachvollziehen, von wem die Beiträge stammen.</p>',
      'ignore' => true,
      'order' => 0, // Am Anfang des Formulars
      'decorators' => array(
        array('Description', array('escape'=>false, 'tag'=>'')),
      ),
    ));

  }

  /**
   * Generate the dynamic fields for thes and expl
   *
   * @param array $theses Array of inputs that are already in the session
   */
  public function generate($theses = array()) {

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

    $this->addDisplayGroup(array(
        $this->getElement('plus'),
        $this->getElement('submit'),
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

    // Script Tag für zusätzliches Javascript
    $this->addElement('hidden', 'script', array(
        'description' => '<script type="text/javascript">' . "\n"
        . '$(document).ready(function() {' . "\n"
        . '  $("#finish").click(function(){' . "\n"
        . '    $("#submitmode").val(\'save_finish\');' . "\n"
        . '  });' . "\n"
        . '  $("#plus").click(function(){' . "\n"
        . '    $("#submitmode").val(\'save_plus\');' . "\n"
        . '  });' . "\n"
        . '});' . "\n"
        . '</script>',
        'ignore' => true,
        'decorators' => array(
            array('Description', array('escape'=>false, 'tag'=>'')),
        ),
    ));
  }

  /**
   * Adds the needed number of input fields for the theses
   *
   * @param integer $i Position of element
   * @param array $thes_item
   */
  protected function addDynamicThesFields($i, $thes_item = array()) {
    // thes
    $thes = null;
    $thes = $this->createElement('textarea', 'thes_' . $i);
    $thesOptions = array(
        'label' => '',
        'cols' => 85,
        'rows' => 2,
        'required' => false,
        'belongsTo' => 'thes',
        //           'isArray' => true,
        'attribs' => array(
            'class' => 'input-block-level input-extensible input-alt',
            'placeholder' => 'Hier könnt ihr euren Beitrag mit bis zu 300 Buchstaben schreiben',
            'id' => 'thes_' . $i,
            'maxlength' => '300'
        )
    );
    $thes->setOptions($thesOptions);

    // toggle
    $toggle = null;
    $toggle = $this->createElement('hidden', 'toggle_' . $i);
    $toggleOptions = array(
        'ignore' => true,
        'description' => '<a href="#" class="btn btn-block btn-small btn-extend js-toggle-extended-input">'
		. '<i class="icon-angle-down icon-large"></i> Klicken, um Eintrag zu erläutern <i class="icon-angle-down icon-large"></i>'
		. '</a>'
    );
    $toggle->setOptions($toggleOptions);
    $toggle->setDecorators(array(array('Description', array('escape' => false, 'tag' => ''))));

    // expl
    $expl = null;
    $expl = $this->createElement('textarea', 'expl_' . $i);
    $explOptions = array(
        'label' => '',
        'cols' => 85,
        'rows' => 5,
        'required' => false,
        'belongsTo' => 'expl',
        //           'isArray' => true,
        'attribs' => array(
            'class' => 'extension input-block-level input-extensible input-alt',
            'style' => 'display: none;',
            'placeholder' => 'Hier könnt ihr euren Beitrag mit bis zu 2000 Buchstaben erläutern',
            'id' => 'expl_' . $i,
            'maxlength' => '2000'
        )
    );
    $expl->setOptions($explOptions);

    if (!empty($thes_item)) {
      $thes->setValue($thes_item['thes']);
      $expl->setValue($thes_item['expl']);
    }

    $this->addDisplayGroup(array(
        $thes,
        $toggle,
        $expl
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