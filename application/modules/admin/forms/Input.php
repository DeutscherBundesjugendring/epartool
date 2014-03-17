<?php
/**
 * Input
 *
 * @description     Form of Input
 * @author                Markus Hackel
 */
class Admin_Form_Input extends Zend_Form
{
    protected $_iniFile = '/modules/admin/forms/Input.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        $this->addPrefixPath('Dbjr_Form', 'Dbjr/Form/');
        // set form-config
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        // Select für Fragezuordnung:
        $questionModel = new Model_Questions();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $kid = $request->getParam('kid', 0);
        $selectOptions = $questionModel->getAdminInputFormSelectOptions($kid);
        $this->getElement('qi')->setMultiOptions($selectOptions);

        // Multicheckbox für Tags
        $tagModel = new Model_Tags();
        $multiOptions = $tagModel->getAdminInputFormMulticheckboxOptions();
        if (!empty($multiOptions)) {
            $tags = $this->getElement('tags');
            $tags->setMultiOptions($multiOptions);
            $htmlTag = $tags->getDecorator('HtmlTag');
            // css Klasse für individuelles Styling im Decorator 'HtmlTag' setzen:
            $htmlTag->setOption('class', 'multicheckbox');
            // JSU remove the "<br />" seperator
            $tags->setSeparator(' ');
        } else {
            // falls keine Tags definiert
            $this->removeElement('tags');
        }

        // CSRF Protection
        $hash = $this->getHash();
        $this->addElement($hash);
    }

	 public function getHash() {
	 	$hash = $this->createElement('hash', 'csrf_token_inputadmin', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
		return $hash;
	 }
}
