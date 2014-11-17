<?php

class Admin_Form_Followup_File extends Dbjr_Form_Admin
{
    protected $_kid;

    public function __construct($kid = null)
    {
        $this->_kid = $kid;
        parent::__construct();
    }

    public function init()
    {
        $view = new Zend_View();

        $this->setMethod('post')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/followup/index/kid/' . $this->_kid]);

        $title = $this->createElement('text', 'titl');
        $title
            ->setLabel('Title')
            ->setRequired(true)
            ->setAttrib('maxlength', 300);
        $this->addElement($title);

        $author = $this->createElement('text', 'who');
        $author
            ->setLabel('Author')
            ->setAttrib('maxlength', 200);
        $this->addElement($author);

        $expl = $this->createElement('textarea', 'ref_view');
        $expl
            ->setLabel('Explanation')
            ->setAttrib('rows', 5)
            ->setAttrib('maxlength', 2000);
        $this->addElement($expl);

        $timeCreated = $this->createElement('text', 'when');
        $timeCreated
            ->setLabel('Time created')
            ->setRequired(true)
            ->setDescription(sprintf($view->translate('Date format: %s'), 'yyyy-mm-dd hh:mm:ss'))
            ->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME)
            ->addValidator('date', false, ['format' => 'Y-m-d H:i:s']);
        $this->addElement($timeCreated);

        $showNoDay = $this->createElement('checkbox', 'show_no_day');
        $showNoDay
            ->setLabel('Display month and year only')
            ->setRequired(true)
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($showNoDay);

        $file = $this->createElement('media', 'ref_doc');
        $file
            ->setLabel('Document')
            ->setRequired(true);
        $this->addElement($file);

        $filePreview = $this->createElement('media', 'gfx_who');
        $filePreview
            ->setLabel('Document preview')
            ->setRequired(true);
        $this->addElement($filePreview);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_followup', array('salt' => 'unique'));
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
     * @param  integer                  $kid The identifier fo the consultation
     * @return Admin_Form_Consultation       Fluent interface
     */
    public function setKid($kid)
    {
        $this->getElement('ref_doc')
            ->setKid($kid)
            ->setIsLockDir(true);
        $this->getElement('gfx_who')
            ->setKid($kid)
            ->setIsLockDir(true);
    }
}
