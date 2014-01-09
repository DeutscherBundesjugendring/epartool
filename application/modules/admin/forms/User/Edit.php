<?php
/**
 * User Edit
 *
 * @description     Form for User Edit
 * @author                Jan Suchandt
 */
class Admin_Form_User_Edit extends Zend_Form
{
    protected $_iniFile = '/modules/admin/forms/User/Edit.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        $this->addPrefixPath('Dbjr_Form', 'Dbjr/Form/');
        // set form-config
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/user/edit');

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
//        $userModel = new Model_Users();
//        $transferOptions = array(0=>'Bitte auswählen');
//        $users = $userModel->getAllConfirmed();
//        foreach ($users As $user) {
//            if (!empty($user['email'])) {
//                $transferOptions[$user['uid']] = $user['email'];
//            }
//        }
//        $this->getElement('transfer')->setMultioptions($transferOptions);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_useredit', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
