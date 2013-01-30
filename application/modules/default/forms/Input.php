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
    
    $this->setDecorators(array('FormElements', 'Form'));
    
    // für alle per ini gesetzten Elemente:
    // nur die Dekoratoren ViewHelper, Errors und Description verwenden
    $this->setElementDecorators(array('ViewHelper', 'Errors', 'Description'));
    
    // hidden Felder brauchen nur den ViewHelper Dekorator:
    $this->setElementDecorators(array('ViewHelper'), array('submitmode'));
    
    // Element für zusätzliches Markup:
    $this->addElement('hidden', 'plaintext', array(
      'description' => '<span class="label label-mitmachen">Mitmachen</span>'
        . '<h2>Beiträge verfassen</h2>'
        . '<p>Hierzu solltest du dich spätestens am Ende einloggen oder registrieren (Abfrage erfolgt automatisch bei „Beenden“).</p>',
      'ignore' => true,
      'order' => 0, // Am Anfang des Formulars
      'decorators' => array(
        array('Description', array('escape'=>false, 'tag'=>'')),
      ),
    ));
    
    // Umschalter für Erläuterungsfeld
    $toggle = $this->getElement('toggle');
    $toggle->setDescription('<a id="toggle_expl" href="" class="btn btn-block"'
      . 'onclick="javascript:if (document.getElementById(\'expl\').style.display == \'none\')'
      . ' { document.getElementById(\'expl\').style.display = \'inline\';'
      . ' document.getElementById(\'toggle_expl\').innerHTML = \'Wieder einklappen\';}'
      . ' else { document.getElementById(\'expl\').style.display = \'none\';'
      . ' document.getElementById(\'toggle_expl\').innerHTML = \'Klicken, um Eintrag zu erläutern\';}'
      . ' return false;"><i class="icon-chevron-down"></i>'
      . ' Klicken, um Eintrag zu erläutern <i class="icon-chevron-down"></i></a>');
    $toggle->setDecorators(array(array('Description', array('escape' => false, 'tag' => ''))));
    
    $this->addDisplayGroup(array(
        $this->getElement('thes'),
        $this->getElement('toggle'),
        $this->getElement('expl')
      ),
      'controlgroup1',
      array(
        'Decorators' => array(
          'FormElements',
          array('HtmlTag', array('tag' => 'div', 'class' => 'control-group'))
        )
      )
    );
    
    $this->addDisplayGroup(array(
        $this->getElement('submit'),
        $this->getElement('finish')
      ),
      'controlgroup2',
      array(
        'Decorators' => array(
          'FormElements',
          array('HtmlTag', array('tag' => 'div', 'class' => 'form-actions'))
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
        . '});' . "\n"
        . '</script>',
      'ignore' => true,
      'decorators' => array(
        array('Description', array('escape'=>false, 'tag'=>'')),
      ),
    ));
  }
}