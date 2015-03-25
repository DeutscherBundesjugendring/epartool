<?php

class Admin_Form_Settings extends Dbjr_Form_Admin
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this
            ->setMethod('post')
            ->setAttrib('class', 'offset-bottom');


        $siteTitle = $this->createElement('text', 'site_title');
        $siteTitle
            ->setLabel('Site Title')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
        $this->addElement($siteTitle);

        $siteDescription = $this->createElement('text', 'site_description');
        $siteDescription
            ->setLabel('Site Description')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
        $this->addElement($siteDescription);

        $siteMotto = $this->createElement('text', 'site_motto');
        $siteMotto
            ->setLabel('Motto')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
        $this->addElement($siteMotto);


        $this->addDisplayGroup(
            ['site_title', 'site_motto', 'site_description'],
            'site',
            ['legend' => $translator->translate('Site Information')]
        );


        $contactName = $this->createElement('text', 'contact_name');
        $contactName
            ->setLabel('Name')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
        $this->addElement($contactName);

        $contactStreet = $this->createElement('text', 'contact_street');
        $contactStreet
            ->setLabel('Street')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
        $this->addElement($contactStreet);

        $contactTown = $this->createElement('text', 'contact_town');
        $contactTown
            ->setLabel('Town')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
        $this->addElement($contactTown);

        $contactZip = $this->createElement('text', 'contact_zip');
        $contactZip
            ->setLabel('Zip')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
        $this->addElement($contactZip);

        $contactEmail = $this->createElement('text', 'contact_email');
        $contactEmail
            ->setLabel('Email')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
        $this->addElement($contactEmail);

        $contactWww = $this->createElement('text', 'contact_www');
        $contactWww
            ->setLabel('Website URL')
            ->setRequired(true)
            ->setAttrib('maxlength', 1000);
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
            ->setAttrib('class', 'btn-primary')
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
