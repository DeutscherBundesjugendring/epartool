<?php

class Admin_Form_Voting_Settings extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setDecorators([['ViewScript', [
            'viewScript' => 'votingprepare/settings-form.phtml',
        ]]]);

        $translator = Zend_Registry::get('Zend_Translate');
        $buttonsTypeOptions = [];
        $buttonSetsForm = new Zend_Form();
        $this->addSubForm($buttonSetsForm, 'buttonSets');
        foreach (Service_Voting::BUTTONS_SET as $type => $parameters) {
            $buttonsTypeOptions[$type] = $parameters['label'];
            $buttonsForm = new Zend_Form();
            $buttonsForm->addPrefixPath('Dbjr_Form_Element', 'Dbjr/Form/Element/', 'element');
            foreach ($parameters['buttons'] as $points => $button) {
                $buttonForm = new Zend_Form();
                $buttonForm->addPrefixPath('Dbjr_Form_Element', 'Dbjr/Form/Element/', 'element');
                $enabled = $buttonForm->createElement('checkbox', 'enabled');
                $enabled
                    ->setLabel('Use Button')
                    ->setRequired(true)
                    ->setAttrib('class', 'js-button-enabled')
                    ->setAttrib('data-mandatory', (int) $button['mandatory'])
                    ->setAttrib('data-group', $type)
                    ->setOptions(['belongsTo' => 'buttonSets[' . $type . '][' . str_replace('-', '_', $points) . ']']);
                $buttonForm->addElement($enabled);

                $label = $buttonForm->createElement('text', 'label');
                $label
                    ->setLabel('Button Label')
                    ->setRequired(false)
                    ->setAttrib('class', 'js-button-label js-button-' . $type . '-label')
                    ->setAttrib('placeholder', $translator->translate($button['label']))
                    ->setOptions(['belongsTo' => 'buttonSets[' . $type . '][' . str_replace('-', '_', $points) . ']']);
                $buttonForm->addElement($label);
                $buttonsForm->addSubForm($buttonForm, $points);
            }
            $buttonsTypeOptions[$type] = $translator->translate($parameters['label']);
            $buttonSetsForm->addSubForm($buttonsForm, $type);
        }

        $buttonType = $this->createElement('radio', 'button_type');
        $buttonType
            ->setRequired(true)
            ->setMultiOptions($buttonsTypeOptions);
        $this->addElement($buttonType);

        $buttonNoOpinion = $this->createElement('radio', 'btn_no_opinion');
        $buttonNoOpinion
            ->setLabel('No Opinion')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    1 => $translator->translate('Enable'),
                    0 => $translator->translate('Disable'),
                ]
            );
        $this->addElement($buttonNoOpinion);

        $buttonImportant = $this->createElement('radio', 'is_btn_important');
        $buttonImportant
            ->setLabel('Superbutton')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    '1' => $translator->translate('Enable'),
                    '0' => $translator->translate('Disable'),
                ]
            );
        $this->addElement($buttonImportant);

        $buttonImportantLabel = $this->createElement('text', 'btn_important_label');
        $buttonImportantLabel->setLabel('Superbutton label');
        $this->addElement($buttonImportantLabel);

        $buttonImportantClicks = $this->createElement('number', 'btn_important_max');
        $buttonImportantClicks
            ->setLabel('Number of clicks allowed')
            ->setAttrib('max', 9999)
            ->setAttrib('min', 1)
            ->addValidator('Int');
        $this->addElement($buttonImportantClicks);

        $buttonImportantFactor = $this->createElement('number', 'btn_important_factor');
        $buttonImportantFactor
            ->setLabel('Rating factor')
            ->setDescription('Max. rating × factor = total points')
            ->setAttrib('max', 8)
            ->setAttrib('min', 1)
            ->addValidator('Int')
            ->addValidator('LessThan', false, ['max' => 8])
            ->addValidator('GreaterThan', false, ['min' => 1]);
        $this->addElement($buttonImportantFactor);

        $hash = $this->createElement('hash', 'csrf_token_settingsvotingsubmissionformadmin', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $csfr_ttl = Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl;
        if (is_numeric(($csfr_ttl))) {
            $hash->setTimeout($csfr_ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Submit');
        $this->addElement($submit);
    }
}
