<?php
/**
 * Article
 *
 * @description     Form of Article
 * @author                Markus Hackel
 */
class Admin_Form_Article extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Article.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        // set form-config
        $this
            ->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
            //->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/article/index']);

        $options = array(
            0 => 'Please select…',
        );

        $this->getElement('ref_nm')->setMultioptions($options);

        $this->getElement('hid')->setCheckedValue('y');
        $this->getElement('hid')->setUncheckedValue('n');

        $projectModel = new Model_Projects();
        $projects = $projectModel->getAll();
        $options = array();
        foreach ($projects as $project) {
            $options[$project['proj']] = $project['titl_short'];
        }
        $this->getElement('proj')->setMultiOptions($options);
        // current project has to be checked always:
        $this->getElement('proj')->setValue(array(Zend_Registry::get('systemconfig')->project));

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_article', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
