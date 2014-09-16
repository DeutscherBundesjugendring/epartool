<?php
/**
 * User Create
 *
 * @description     Form for User Create
 * @author                Jan Suchandt
 */
class Admin_Form_User_Create extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/User/Create.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        // set form-config
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/user/create');

        // JSU options für select-feld setzen
        $options = array(
            'usr'=>'Benutzer',
            'edt'=>'Redakteur',
            'adm'=>'Administrator',
        );
        $this->getElement('lvl')->setMultioptions($options);

        $options = array(
            'y'=>'Ja',
            'n'=>'Nein'
        );
        $this->getElement('newsl_subscr')->setMultioptions($options);

        $options = array(
            'b'=>'Blockiert',
            'u'=>'Unbestätigt',
            'c'=>'Bestätigt'
        );
        $this->getElement('block')->setMultioptions($options);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_usercreate', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
