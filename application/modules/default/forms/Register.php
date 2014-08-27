<?php

class Default_Form_Register extends Zend_Form
{

    public function init()
    {
        $view = new Zend_View();

        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/register');

        $kid = $this->createElement('hidden', 'kid');
        $this->addElement($kid);

        $email = $this->createElement('text', 'email');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', '@')
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        $hint = $this->createElement('hidden', ' hint');
        $description = $view->translate('Die folgenden Angaben sind freiwillig und dienen dazu, uns einen Überblick zu geben, welche Zielgruppen erreicht werden.');
        $hint
            ->setDescription('<dd><p class="help-block help-block-offset">' . $description . '</p></dd>')
            ->setDecorators([['Description', ['escape' => false]]]);

        $groupType = $this->createElement('radio', 'group_type');
        $singleOpt = $view->translate('Ich antworte als Einzelperson');
        $groupOpt = $view->translate('Ich antworte für eine Gruppe');
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

        $name = $this->createElement('text', 'name');
        $placeholder = $view->translate('Vorname Nachname');
        $name
            ->setLabel('Name')
            ->setRequired(false)
            ->setAttrib('placeholder', $placeholder)
            ->setAttrib('class', 'input-xlarge')
            ->setValidators(['NotEmpty'])
            ->setFilters(['StripTags']);
        $this->addElement($name);



        // subform for group_type == "group"
        $groupSubForm = new Zend_Form_SubForm();
        $this->addSubForm($groupSubForm, 'group_specs', 6);

        $source = $this->createElement('multiCheckbox', 'source');
        $source
            ->setLabel('Bitte beschreibt, woher eure Beiträge stammen:')
            ->setMultiOptions(
                [
                    'd' => 'Ergebnisse aus einer Veranstaltung/einem Projekt zum Thema',
                    'g' => 'In der Gruppe erarbeitet (z.B. in einem Workshop, einer Gruppenstunde)',
                    'p' => 'Positionspapier/Beschluss unserer Gruppe/Organisation',
                    'm' => 'Sonstiges, und zwar:',
                ]
            );
        $groupSubForm->addElement($source);

        $srcMisc = $this->createElement('textarea', 'src_misc');
        $srcMisc
            ->setAttrib('cols', 60)
            ->setAttrib('rows', 2)
            ->setAttrib('class', 'input-xlarge')
            ->setFilters(['StripTags', 'HtmlEntities']);
        $groupSubForm->addElement($srcMisc);

        $groupSize = $this->createElement('select', 'group_size');
        $grpSizeDef = Zend_Registry::get('systemconfig')
            ->group_size_def
            ->toArray();
        unset($grpSizeDef['0']);
        unset($grpSizeDef['1']);
        $groupSize
            ->setLabel('Wie viele Personen waren beteiligt?')
            ->setMultioptions($grpSizeDef)
            ->setAttrib('class', 'input-xlarge');
        $groupSubForm->addElement($groupSize);

        $nameGroup = $this->createElement('text', 'name_group');
        $nameGroup
            ->setLabel('Gruppenbezeichnung')
            ->setAttrib('class', 'input-xlarge')
            ->setFilters(['StripTags']);
        $groupSubForm->addElement($nameGroup);

        $namePers = $this->createElement('text', 'name_pers');
        $namePers
            ->setLabel('Ansprechpartner_in')
            ->setAttrib('class', 'input-xlarge')
            ->setFilters(['StripTags']);
        $groupSubForm->addElement($namePers);



        $age = $this->createElement('select', 'age_group');
        $age
            ->setLabel('Alter')
            ->setAttrib('class', 'input-xlarge')
            ->setMultiOptions(
                [
                    '1' => $view->translate('bis 17 Jahre'),
                    '2' => $view->translate('bis 26 Jahre'),
                    '3' => $view->translate('ab 27 Jahre'),
                    '4' => $view->translate('Alle Altersgruppen'),
                    '5' => $view->translate('keine Angabe'),
                ]
            );
        $this->addElement($age);

        $regioPax = $this->createElement('text', 'regio_pax');
        $regioPax
            ->setLabel('Bundesland')
            ->setAttrib('class', 'input-xlarge')
            ->setFilters(['StripTags', 'HtmlEntities']);
        $this->addElement($regioPax);

        $sendResults = $this->createElement('checkbox', 'cnslt_results');
        $sendResults
            ->setLabel('Ich möchte über die Ergebnisse der Beteiligungsrunde informiert werden.')
            ->setCheckedValue('y')
            ->setUnCheckedValue('n');
        $this->addElement($sendResults);

        $newsletter = $this->createElement('checkbox', 'newsl_subscr');
        $newsletter
            ->setLabel('Ich möchte den Newsletter erhalten.')
            ->setCheckedValue('y')
            ->setUnCheckedValue('n');
        $this->addElement($newsletter);

        $comment = $this->createElement('textarea', 'cmnt_ext');
        $comment
            ->setLabel('Hier ist noch Platz für Kommentare:')
            ->setAttrib('cols', 100)
            ->setAttrib('rows', 3)
            ->setAttrib('maxlength', 600)
            ->setAttrib('class', 'input-block-level')
            ->setFilters(['StripTags', 'HtmlEntities']);
        $this->addElement($comment);

        $ccLicense = $this->createElement('checkbox', 'is_contrib_under_cc');
        $label = sprintf(
            (new Zend_View())->translate(
                'The contributions are published under a <a href="%s" target="_blank" title="Mehr über die Creative-Commons-Lizenz erfahren">creative commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.'
            ),
            Zend_Registry::get('systemconfig')->license->creative_commons->link
        );
        $ccLicense
            ->setLabel($label)
            ->addValidator('NotEmpty', false, ['messages' => [Zend_Validate_NotEmpty::IS_EMPTY => 'You must agree']])
            ->setCheckedValue('1')
            ->setUnCheckedValue(null)
            ->setRequired(true)
            ->getDecorator('Label')
                ->setOptions(['escape' => false]);
        $this->addElement($ccLicense);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setLabel('Send')
            ->setAttrib('class', 'btn');
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

        $this
            ->removeElement('email')
            ->addElement($emailDisabledEl)
            ->addElement($emailHiddenEl);
    }
}
