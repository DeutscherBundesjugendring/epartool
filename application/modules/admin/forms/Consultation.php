<?php

class Admin_Form_Consultation extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Consultation.ini';

    /**
     * Sets the consultation to be asociated with this form
     * Needed to offer the proper media folder.
     * If the consuiltation is just being created no media folder exists and this method is not to be called.
     * @param  integer                  $kid The identifier fo the consultation
     * @return Admin_Form_Consultation   Fluent interface
     */
    public function setKid($kid)
    {
        $this->removeElement('img_file');

        $imgFile = $this->createElement('media', 'img_file');
        $imgFile
            ->setLabel('Featured image')
            ->setRequired(true)
            ->setOrder(3)
            ->setKid($kid);
        $this->addElement($imgFile);

        return $this;
    }

    public function init()
    {
        $this
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin'])
            ->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $consultationModel = new Model_Consultations();
        $lastId = $consultationModel->getLastId();
        $highestId = $lastId + 1;
        $this->getElement('ord')->setDescription(
            '(z.B. höher=weiter vorn; z.B. derzeit neue höchste Nummer: ' . $highestId . ')'
        );

        $this->getElement('inp_show')->setCheckedValue('y');
        $this->getElement('inp_show')->setUncheckedValue('n');
        $this->getElement('spprt_show')->setCheckedValue('y');
        $this->getElement('spprt_show')->setUncheckedValue('n');
        $this->getElement('vot_show')->setCheckedValue('y');
        $this->getElement('vot_show')->setUncheckedValue('n');
        $this->getElement('vot_res_show')->setCheckedValue('y');
        $this->getElement('vot_res_show')->setUncheckedValue('n');
        $this->getElement('summ_show')->setCheckedValue('y');
        $this->getElement('summ_show')->setUncheckedValue('n');
        $this->getElement('follup_show')->setCheckedValue('y');
        $this->getElement('follup_show')->setUncheckedValue('n');
        $this->getElement('public')->setCheckedValue('y');
        $this->getElement('public')->setUncheckedValue('n');

        $this->getElement('expl')->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);
        $this->getElement('vot_expl')->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);

        $this->getElement('inp_fr')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
        $this->getElement('inp_to')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
        $this->getElement('spprt_fr')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
        $this->getElement('spprt_to')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
        $this->getElement('vot_fr')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
        $this->getElement('vot_to')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
        $this->getElement('discussion_from')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
        $this->getElement('discussion_to')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);

        $options = array(
                0 => 'keiner ausgewählt'
            );
        $userModel = new Model_Users();
        $admins = $userModel->getAdmins();
        foreach ($admins as $admin) {
            $options[$admin->uid] = $admin->email;
        }
        $this->getElement('adm')->setMultioptions($options);

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
        $hash = $this->createElement('hash', 'csrf_token_consultation', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
