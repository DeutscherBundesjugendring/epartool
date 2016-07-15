<?php

class Admin_Form_Settings_Site extends Dbjr_Form_Admin
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this->setAttrib('class', 'offset-bottom');


        $desc = $translator->translate(
            'Used in page <title> tag, in DV.title and og:title meta tags and as a logo image alt text.'
        );

        $locale = $this->createElement('select', 'locale');
        $locale
            ->setLabel('Application Language')
            ->setRequired(true)
            ->setMultiOptions(['en_US' => 'English', 'de_DE' => 'Deutsch']);
        $this->addElement($locale);

        $siteTitle = $this->createElement('text', 'site_title');
        $siteTitle
            ->setLabel('Site Title')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000)
            ->setDescription($desc);
        $this->addElement($siteTitle);

        $desc = $translator->translate('Used in description and og:description meta tags.');
        $siteDescription = $this->createElement('text', 'site_description');
        $siteDescription
            ->setLabel('Site Description')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000)
            ->setDescription($desc);
        $this->addElement($siteDescription);

        $desc = $translator->translate('Used as a subtitle on all pages that are not consultation specific.');
        $siteMotto = $this->createElement('text', 'site_motto');
        $siteMotto
            ->setLabel('Motto')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000)
            ->setDescription($desc);
        $this->addElement($siteMotto);


        $this->addDisplayGroup(
            ['locale', 'site_title', 'site_motto', 'site_description'],
            'site',
            ['legend' => $translator->translate('Site Information')]
        );

        $desc = $translator->translate('Used as a placeholder in email templates.');
        $contactName = $this->createElement('text', 'contact_name');
        $contactName
            ->setLabel('Name')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000)
            ->setDescription($desc);
        $this->addElement($contactName);

        $desc = $translator->translate('Used as a placeholder in email templates.');
        $contactEmail = $this->createElement('text', 'contact_email');
        $contactEmail
            ->setLabel('Email')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000)
            ->setDescription($desc);
        $this->addElement($contactEmail);

        $desc = $translator->translate('Used as a placeholder in email templates.');
        $contactWww = $this->createElement('text', 'contact_www');
        $contactWww
            ->setLabel('Website URL')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000)
            ->setDescription($desc);
        $this->addElement($contactWww);

        $this->addDisplayGroup(
            ['contact_name', 'contact_street', 'contact_town', 'contact_zip', 'contact_email', 'contact_www'],
            'contact',
            ['legend' => $translator->translate('Contact Information')]
        );

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_settings', array('salt' => 'unique'));
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

    public function populate(array $params)
    {
        $paramsFin = [];
        foreach ($params as $key => $value) {
            $paramsFin[str_replace('.', '_', $key)] = $value;
        }

        return parent::populate($paramsFin);
    }
}
