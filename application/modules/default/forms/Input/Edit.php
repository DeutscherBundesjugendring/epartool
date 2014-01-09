<?php
/**
 * Edit Form
 * Formular für Beiträge zu Fragen
 *
 */
class Default_Form_Input_Edit extends Zend_Form
{
    protected $_iniFile = '/modules/default/forms/Input/Edit.ini';
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

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_inputedit', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
