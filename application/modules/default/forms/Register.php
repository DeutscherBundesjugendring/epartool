<?php

class Default_Form_Register extends Dbjr_Form_Web
{
    /**
     * @var int
     */
    private $consultationId;

    /**
     * Default_Form_Register constructor.
     * @param int $consultationId
     * @param array|Zend_Config $options
     */
    public function __construct($consultationId, $options = null)
    {
        $this->consultationId = $consultationId;
        parent::__construct($options);
    }
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $formConsultationSettings = (new Model_Consultations())->find($this->consultationId)->current()->toArray();

        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/register');

        $kid = $this->createElement('hidden', 'kid');
        $this->addElement($kid);

        $email = $this->createElement('email', 'email');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', '@')
            ->setValidators(['EmailAddress']);
        $this->addElement($email);

        $hint = $this->createElement('hidden', ' hint');
        $description = $translator->translate('The following fields are not obligatory, but the data can help us find out which target groups we are reaching.');
        $description = '<p class="help-block help-block-offset">' . $description . '</p>';
        $hint
            ->setDescription($description)
            ->setDecorators([['Description', ['escape' => false]]]);

        if ((bool) $formConsultationSettings['allow_groups']) {
            $groupType = $this->createElement('radio', 'group_type');
            $singleOpt = $translator->translate('I am responding as an individual person');
            $groupOpt = $translator->translate('I am responding in the name of a group');
            $groupType
                ->setMultiOptions(
                    [
                        'single' => $singleOpt,
                        'group' => $groupOpt,
                    ]
                )
                ->removeDecorator('Label')
                ->setValue('single');
            $this->addElement($groupType);
        } else {
            $groupType = $this->createElement('hidden', 'group_type');
            $groupType->setValue('single');
            $this->addElement($groupType);
        }

        if ((bool) $formConsultationSettings['field_switch_name']) {
            $name = $this->createElement('text', 'name');
            $placeholder = $translator->translate('First name and surname');
            $name
                ->setLabel('Name')
                ->setRequired(false)
                ->setAttrib('placeholder', $placeholder)
                ->setValidators(['NotEmpty'])
                ->setFilters(['StripTags']);
            $this->addElement($name);
        }


        // subform for group_type == "group"
        $groupSubForm = new Zend_Form_SubForm();
        $this->addSubForm($groupSubForm, 'group_specs', 6);

        if ((bool) $formConsultationSettings['field_switch_contribution_origin']) {
            $source = $this->createElement('multiCheckbox', 'source');
            $source
                ->setLabel('Please describe origin of your contributions:')
                ->setMultiOptions(
                    [
                        'd' => $translator->translate('Results from a meeting or project related to the topic'),
                        'g' => $translator->translate('Compiled by our group (e.g. in a workshop or a group session)'),
                        'p' => $translator->translate('Position paper or agreement in our group/organisation'),
                        'm' => $translator->translate('Other:'),
                    ]
                );
            $groupSubForm->addElement($source);

            $srcMisc = $this->createElement('textarea', 'src_misc');
            $srcMisc
                ->setAttrib('cols', 60)
                ->setAttrib('rows', 2)
                ->setFilters(['StripTags', 'HtmlEntities']);
            $groupSubForm->addElement($srcMisc);
        }

        if ((bool) $formConsultationSettings['field_switch_individuals_sum']) {
            $groupSize = $this->createElement('select', 'group_size');
            $grpSizeDef = (new Model_GroupSize())->getOptionsByConsultation($this->consultationId);
            $groupSize
                ->setLabel('How many individuals were involved?')
                ->setMultioptions($grpSizeDef);
            $groupSubForm->addElement($groupSize);
        }

        if ((bool) $formConsultationSettings['field_switch_group_name']) {
            $nameGroup = $this->createElement('text', 'name_group');
            $nameGroup
                ->setLabel('Group name')
                ->setFilters(['StripTags']);
            $groupSubForm->addElement($nameGroup);
        }

        if ((bool) $formConsultationSettings['field_switch_contact_person']) {
            $namePers = $this->createElement('text', 'name_pers');
            $namePers
                ->setLabel('Contact person')
                ->setFilters(['StripTags']);
            $groupSubForm->addElement($namePers);
        }


        if ((bool) $formConsultationSettings['field_switch_age']) {
            $age = $this->createElement('select', 'age_group');
            $ageOptions = (new Model_ContributorAge())->getOptionsByConsultation($this->consultationId);
            if ($formConsultationSettings['groups_no_information']) {
                $ageOptions[''] = $translator->translate('no information');
            }
            $age
                ->setLabel('Age')
                ->setMultiOptions($ageOptions);
            $this->addElement($age);
        }

        if ((bool) $formConsultationSettings['field_switch_state']) {
            $regioPax = $this->createElement('text', 'regio_pax');
            $regioPax
                ->setLabel(!empty($formConsultationSettings['state_field_label'])
                    ? $formConsultationSettings['state_field_label']
                    : 'State')
                ->setFilters(['StripTags', 'HtmlEntities']);
            $this->addElement($regioPax);
        }

        if ((bool) $formConsultationSettings['field_switch_notification']) {
            $sendResults = $this->createElement('checkbox', 'cnslt_results');
            $sendResults
                ->setLabel('I want to get informed about outcomes of the consultation round.')
                ->setCheckedValue('y')
                ->setUnCheckedValue('n');
            $this->addElement($sendResults);
        }

        if ((bool) $formConsultationSettings['field_switch_newsletter']) {
            $newsletter = $this->createElement('checkbox', 'newsl_subscr');
            $newsletter
                ->setLabel('I would like to subscribe to the newsletter.')
                ->setCheckedValue('y')
                ->setUnCheckedValue('n');
            $this->addElement($newsletter);
        }

        if ((bool) $formConsultationSettings['field_switch_comments']) {
            $comment = $this->createElement('textarea', 'cmnt_ext');
            $comment
                ->setLabel('Any comments?')
                ->setAttrib('cols', 100)
                ->setAttrib('rows', 3)
                ->setAttrib('maxlength', 600)
                ->setFilters(['StripTags', 'HtmlEntities']);
            $this->addElement($comment);
        }

        $locale = Zend_Registry::get('Zend_Locale');
        $projectSettings = (new Model_Projects())
            ->find(Zend_Registry::get('systemconfig')->project)
            ->current()
            ->toArray();
        $license = (new Model_License())
            ->find($projectSettings['license'], $locale->getLanguage() . '_' . $locale->getRegion())
            ->current()
            ->toArray();
        $ccLicense = $this->createElement('checkbox', 'is_contrib_under_cc');
        $ccLicense
            ->setLabel(
                $formConsultationSettings['license_agreement'] === null
                    ? $license['text']
                    : (new Service_Wysiwyg(Zend_Controller_Front::getInstance()->getBaseUrl()))
                        ->placeholderToBasePath(
                            $this->replaceLicensePlaceholders($license, $formConsultationSettings['license_agreement'])
                        )
            )
            ->addValidator('NotEmpty', false, [
                'messages' => [Zend_Validate_NotEmpty::IS_EMPTY => $translator->translate('You must agree.')]
            ])
            ->setCheckedValue('1')
            ->setUnCheckedValue(null)
            ->setRequired(true);
        $ccLicense->getDecorator('BootstrapCheckbox')->setOption('escapeLabel', false);
        $this->addElement($ccLicense);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-default')
            ->setLabel('Send');
        $this->addElement($submit);

        $hash = $this->createElement('hash', 'csrf_token_register', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }

    /**
     * Makes the email field element disabled, but adds a hidden field so the value still gets submitted
     */
    public function lockEmailField() {
        $emailDisabledEl = $this
            ->getElement('email')
            ->setName('email-disabled')
            ->setOrder(1)
            ->setAttrib('disabled', 'disabled');

        $emailHiddenEl = $this
            ->createElement('hidden', 'email')
            ->setValue($emailDisabledEl->getValue())
            ->setName('email');

        $this->removeElement('email');
        $this
            ->addElement($emailDisabledEl)
            ->addElement($emailHiddenEl);
    }

    /**
     * @param array $license
     * @param string $text
     * @return string
     */
    private function replaceLicensePlaceholders(array $license, $text)
    {
        return preg_replace(
            '#http[s]?\:\/\/(http[s]?)#',
            '$1',
            str_replace(['{{license_link}}', '{{license_title}}'], [$license['link'], $license['title']], $text)
        );
    }
}
