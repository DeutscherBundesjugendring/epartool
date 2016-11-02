<?php

class Admin_Form_Consultation extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
        $translator = Zend_Registry::get('Zend_Translate');

        $title = $this->createElement('text', 'titl');
        $title
            ->setLabel('Title')
            ->setRequired(true)
            ->setAttrib('maxlength', 120);
        $this->addElement($title);

        $desc = sprintf(
            $translator->translate(
                'The title to be used where space is limited, i.e. email subjects. Max %d characters.'
            ),
            40
        );
        $titleShort = $this->createElement('text', 'titl_short');
        $titleShort
            ->setLabel('Short title')
            ->addValidator('stringLength', ['max' => 40])
            ->setDescription($desc)
            ->setAttrib('maxlength', 40)
            ->setRequired(true);
        $this->addElement($titleShort);

        $subTitle = $this->createElement('text', 'titl_sub');
        $subTitle
            ->setLabel('Subtitle')
            ->addValidator('stringLength', ['max' => 200]);
        $this->addElement($subTitle);

        $image = $this->createElement('file', 'img_file');
        $image
            ->setLabel('Featured image')
            ->setDescription(sprintf($translator->translate('Recommended size: %s x %s pixels'), 300, 500));
        $this->addElement($image);

        $imageDesc = $this->createElement('text', 'img_expl');
        $imageDesc->setLabel('Featured image description');
        $this->addElement($imageDesc);

        $desc = sprintf(
            $translator->translate(
                'The higher number, the higher position in consultation list. The highest position is currently %d.'
            ),
            (new Model_Consultations())->getMaxOrder()
        );
        $order = $this->createElement('number', 'ord');
        $order
            ->setLabel('Order')
            ->setRequired(true)
            ->setDescription($desc)
            ->addValidator('Int');
        $this->addElement($order);

        $explShort = $this->createElement('textarea', 'expl_short');
        $explShort
            ->setLabel('Explanation short')
            ->setAttrib('rows', 5);
        $this->addElement($explShort);

        $inputShow = $this->createElement('checkbox', 'inp_show');
        $inputShow
            ->setLabel('Enable contribution phase')
            ->setRequired(true)
            ->setAttrib('data-toggle', 'disable')
            ->setAttrib('data-disable-target', '.js-input-dates')
            ->setCheckedValue('y')
            ->setUncheckedValue('n')
            ->setValue('y');
        $this->addElement($inputShow);

        $inputFrom = $this->createElement('text', 'inp_fr');
        $inputFrom
            ->setLabel('Contribution phase start')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        self::addCssClass($inputFrom, 'js-input-dates');
        $this->addElement($inputFrom);

        $inputTo = $this->createElement('text', 'inp_to');
        $inputTo
            ->setLabel('Contribution phase end')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        self::addCssClass($inputTo, 'js-input-dates');
        $this->addElement($inputTo);

        $supportShow = $this->createElement('checkbox', 'spprt_show');
        $supportShow
            ->setLabel('Enable support phase')
            ->setRequired(true)
            ->setAttrib('data-toggle', 'disable')
            ->setAttrib('data-disable-target', '.js-support-dates')
            ->setCheckedValue('y')
            ->setUncheckedValue('n')
            ->setValue('y');
        $this->addElement($supportShow);

        $supportFrom = $this->createElement('text', 'spprt_fr');
        $supportFrom
            ->setLabel('Support phase start')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        self::addCssClass($supportFrom, 'js-support-dates');
        $this->addElement($supportFrom);

        $supportTo = $this->createElement('text', 'spprt_to');
        $supportTo
            ->setLabel('Support phase end')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        self::addCssClass($supportTo, 'js-support-dates');
        $this->addElement($supportTo);

        $voteShow = $this->createElement('checkbox', 'vot_show');
        $voteShow
            ->setLabel('Enable voting phase')
            ->setRequired(true)
            ->setAttrib('data-toggle', 'disable')
            ->setAttrib('data-disable-target', '.js-vote-dates')
            ->setCheckedValue('y')
            ->setUncheckedValue('n')
            ->setValue('y');
        $this->addElement($voteShow);

        $voteFrom = $this->createElement('text', 'vot_fr');
        $voteFrom
            ->setLabel('Voting phase start')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        self::addCssClass($voteFrom, 'js-vote-dates');
        $this->addElement($voteFrom);

        $voteTo = $this->createElement('text', 'vot_to');
        $voteTo
            ->setLabel('Voting phase end')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        self::addCssClass($voteTo, 'js-vote-dates');
        $this->addElement($voteTo);

        $voteResShow = $this->createElement('checkbox', 'vot_res_show');
        $voteResShow
            ->setLabel('Make voting results public')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($voteResShow);

        $explVoting = $this->createElement('textarea', 'vot_expl');
        $explVoting
            ->setLabel('Voting phase explanation')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD)
            ->setAttrib('rows', 5)
            ->addFilter('HtmlEntities');
        $this->addElement($explVoting);

        $discussionActive = $this->createElement('checkbox', 'is_discussion_active');
        $discussionActive
            ->setLabel('Enable discussion')
            ->setRequired(true)
            ->setAttrib('data-toggle', 'disable')
            ->setAttrib('data-disable-target', '.js-discussion-dates')
            ->setCheckedValue(1)
            ->setUncheckedValue(0)
            ->setValue(1);
        $this->addElement($discussionActive);

        $discussionFrom = $this->createElement('text', 'discussion_from');
        $discussionFrom
            ->setLabel('Discussion phase start')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        self::addCssClass($discussionFrom, 'js-discussion-dates');
        $this->addElement($discussionFrom);

        $discussionTo = $this->createElement('text', 'discussion_to');
        $discussionTo
            ->setLabel('Discussion phase end')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        self::addCssClass($discussionTo, 'js-discussion-dates');
        $this->addElement($discussionTo);

        $enableVideo = $this->createElement('checkbox', 'discussion_video_enabled');
        $enableVideo
            ->setLabel('Allow videos in Discussion')
            ->setRequired(false);
        $this->addElement($enableVideo);

        $followupShow = $this->createElement('checkbox', 'follup_show');
        $followupShow
            ->setLabel('Enable follow-ups')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($followupShow);

        $followUpExplanation = $this->createElement('textarea', 'follow_up_explanation');
        $followUpExplanation
            ->setLabel('Follow-Up phase explanation')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD)
            ->setAttrib('rows', 5);
        $this->addElement($followUpExplanation);

        $licenseAgreement = $this->createElement('textarea', 'license_agreement');
        $licenseAgreement
            ->setLabel('Terms & Conditions text')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD)
            ->setAttrib('rows', 5);
        $this->addElement($licenseAgreement);

        $isPublic = $this->createElement('checkbox', 'public');
        $isPublic
            ->setLabel('Make public')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($isPublic);

        $options = [0 => 'Please select…'];
        $admins = (new Model_Users())->getAdmins();
        foreach ($admins as $admin) {
            $options[$admin->uid] = $admin->email;
        }
        $admin = $this->createElement('select', 'adm');
        $admin
            ->setLabel('Responsible administrator')
            ->setRequired(true)
            ->setMultioptions($options);

        $projects = (new Model_Projects())->getAll();
        $options = [];
        foreach ($projects as $project) {
            $options[$project['proj']] = $project['titl_short'];
        }
        $project = $this->createElement('multiCheckbox', 'proj');
        $project
            ->setLabel('Project')
            ->setDescription('Current project must be always selected.')
            ->setRequired(true)
            ->setMultiOptions($options)
            ->setValue([Zend_Registry::get('systemconfig')->project]);
        $this->addElement($project);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_consultation', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save');
        $this->addElement($submit);
    }

    /**
     * Sets the consultation to be asociated with this form
     * Needed to offer the proper media folder.
     * If the consuiltation is just being created no media folder exists and this method is not to be called.
     * @param  integer                  $kid The identifier fo the consultation
     * @return Admin_Form_Consultation       Fluent interface
     */
    public function setKid($kid)
    {
        $this->removeElement('img_file');

        $imgFile = $this->createElement('media', 'img_file');
        $imgFile
            ->setLabel('Featured image')
            ->setDescription(
                sprintf(Zend_Registry::get('Zend_Translate')->translate('Recommended size: %s x %s pixels'), 300, 500)
            )
            ->setOrder(2)
            ->setKid($kid);
        $this->addElement($imgFile);

        return $this;
    }

    public function isValid($data)
    {
        if ($data['inp_show'] === 'n') {
            $this->getElement('inp_fr')->setOptions(['required'=>false]);
            $data['inp_fr'] = null;
            $this->getElement('inp_to')->setOptions(['required'=>false]);
            $data['inp_to'] = null;
        }
        if ($data['spprt_show'] === 'n') {
            $this->getElement('spprt_fr')->setOptions(['required'=>false]);
            $data['spprt_fr'] = null;
            $this->getElement('spprt_to')->setOptions(['required'=>false]);
            $data['spprt_to'] = null;
        }
        if ($data['vot_show'] === 'n') {
            $this->getElement('vot_fr')->setOptions(['required'=>false]);
            $data['vot_fr'] = null;
            $this->getElement('vot_to')->setOptions(['required'=>false]);
            $data['vot_to'] = null;
        }
        if (!$data['is_discussion_active']) {
            $this->getElement('discussion_from')->setOptions(['required' => false]);
            $data['discussion_from'] = null;
            $this->getElement('discussion_to')->setOptions(['required' => false]);
            $data['discussion_to'] = null;
        }

        return parent::isValid($data);
    }

    public function populate(array $values)
    {
        if ($values['inp_show'] === 'n') {
            $this->getElement('inp_fr')->setAttrib('disabled', 'disabled');
            $this->getElement('inp_to')->setAttrib('disabled', 'disabled');
        }
        if ($values['spprt_show'] === 'n') {
            $this->getElement('spprt_fr')->setAttrib('disabled', 'disabled');
            $this->getElement('spprt_to')->setAttrib('disabled', 'disabled');
        }
        if ($values['vot_show'] === 'n') {
            $this->getElement('vot_fr')->setAttrib('disabled', 'disabled');
            $this->getElement('vot_to')->setAttrib('disabled', 'disabled');
        }
        if (!$values['is_discussion_active']) {
            $this->getElement('discussion_from')->setAttrib('disabled', 'disabled');
            $this->getElement('discussion_to')->setAttrib('disabled', 'disabled');
        }

        return parent::populate($values);
    }
}
