<?php
/**
 * Question
 *
 * @description     Form of question
 * @author                Markus Hackel
 */
class Admin_Form_Question extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Question.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        // set form-config
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_question', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
