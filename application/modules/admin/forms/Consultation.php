<?php

class Admin_Form_Consultation extends Dbjr_Form_Admin
{
    public function init()
    {
        $view = new Zend_View();

        $this->setMethod('post');

        $title = $this->createElement('text', 'titl');
        $title
            ->setLabel('Title')
            ->setRequired(true)
            ->setAttrib('maxlength', 120);
        $this->addElement($title);

        $titleShort = $this->createElement('text', 'titl_short');
        $titleShort
            ->setLabel('Short title')
            ->setRequired(true);

        $subTitle = $this->createElement('text', 'titl_sub');
        $subTitle->setLabel('Subtitle');
        $this->addElement($subTitle);

        $image = $this->createElement('file', 'img_file');
        $image
            ->setLabel('Featured image')
            ->setRequired(true);
        $this->addElement($image);

        $imageDesc = $this->createElement('text', 'img_text');
        $imageDesc->setLabel('Featured image description');
        $this->addElement($imageDesc);

        $desc = sprintf(
            $view->translate('The higher number, the higher position in consultation list. The highest position is currently %d.'),
            (new Model_Consultations())->getLastId()
        );
        $order = $this->createElement('number', 'ord');
        $order
            ->setLabel('Order')
            ->setRequired(true)
            ->setDescription($desc)
            ->addValidator('Int');
        $this->addElement($order);

        $expl = $this->createElement('textarea', 'expl');
        $expl
            ->setLabel('Explanation')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD)
            ->setAttrib('rows', 5)
            ->addFilter('HtmlEntities');
        $this->addElement($expl);

        $explShort = $this->createElement('textarea', 'expl_short');
        $explShort
            ->setLabel('Explanation short')
            ->setAttrib('rows', 5);
        $this->addElement($explShort);

        $inputShow = $this->createElement('checkbox', 'inp_show');
        $inputShow
            ->setLabel('Enable contribution phase')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($inputShow);

        $inputFrom = $this->createElement('text', 'inp_fr');
        $inputFrom
            ->setLabel('Contribution phase start')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        $this->addElement($inputFrom);

        $inputTo = $this->createElement('text', 'inp_to');
        $inputTo
            ->setLabel('Contribution phase end')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        $this->addElement($inputTo);

        $supportShow = $this->createElement('checkbox', 'spprt_show');
        $supportShow
            ->setLabel('Enable support phase')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($supportShow);

        $supportFrom = $this->createElement('text', 'spprt_fr');
        $supportFrom
            ->setLabel('Support phase start')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        $this->addElement($supportFrom);

        $supportTo = $this->createElement('text', 'spprt_to');
        $supportTo
            ->setLabel('Support phase end')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        $this->addElement($supportTo);

        $voteShow = $this->createElement('checkbox', 'vot_show');
        $voteShow
            ->setLabel('Enable voting phase')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($voteShow);

        $voteFrom = $this->createElement('text', 'vot_fr');
        $voteFrom
            ->setLabel('Voting phase start')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        $this->addElement($voteFrom);

        $voteTo = $this->createElement('text', 'vot_to');
        $voteTo
            ->setLabel('Voting phase end')
            ->setRequired(true)
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
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
            ->setCheckedValue(1)
            ->setUncheckedValue(0);
        $this->addElement($discussionActive);

        $discussionFrom = $this->createElement('text', 'discussion_from');
        $discussionFrom
            ->setLabel('Discussion phase start')
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        $this->addElement($discussionFrom);

        $discussionTo = $this->createElement('text', 'discussion_to');
        $discussionTo
            ->setLabel('Discussion phase end')
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        $this->addElement($discussionTo);

        $followupShow = $this->createElement('checkbox', 'follup_show');
        $followupShow
            ->setLabel('Enable followups')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($followupShow);

        $followupSummaryShow = $this->createElement('checkbox', 'summ_show');
        $followupSummaryShow
            ->setLabel('Enable followup summary')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($followupSummaryShow);

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
        $submit->setLabel('Save');
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
            ->setRequired(true)
            ->setOrder(2)
            ->setKid($kid);
        $this->addElement($imgFile);

        return $this;
    }
}
