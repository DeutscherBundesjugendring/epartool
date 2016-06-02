<?php

class Admin_Form_Article extends Dbjr_Form_Admin
{
    protected $_kid;

    public function __construct($kid = null)
    {
        $this->_kid = $kid;
        parent::__construct();
    }

    public function init()
    {
        $cancelUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/article';
        if ($this->_kid) {
            $cancelUrl .= '/index/kid/' . $this->_kid;
        }

        $this->setMethod('post')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => $cancelUrl]);

        $id = $this->createElement('hidden', 'art_id');
        $this->addElement($id);

        $desc = $this->createElement('text', 'desc');
        $desc
            ->setLabel('Title')
            ->setRequired(true)
            ->setAttrib('maxlength', 44);
        $this->addElement($desc);

        $refName = $this->createElement('select', 'ref_nm');
        $refName
            ->setLabel('Reference name')
            ->setMultioptions([0 => Zend_Registry::get('Zend_Translate')->translate('Please select…')]);
        $this->addElement($refName);

        $parentId = $this->createElement('select', 'parent_id');
        $parentId
            ->setLabel('Parent page');
        $this->addElement($parentId);

        $body = $this->createElement('textarea', 'artcl');
        $body
            ->setLabel('Body')
            ->setRequired(true)
            ->setAttrib('rows', 12)
            ->addFilter('HtmlEntities')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);
        $this->addElement($body);

        $sidebar = $this->createElement('textarea', 'sidebar');
        $sidebar
            ->setLabel('Sidebar text')
            ->setAttrib('rows', 12)
            ->addFilter('HtmlEntities')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);
        $this->addElement($sidebar);

        $hide = $this->createElement('checkbox', 'hid');
        $hide
            ->setLabel('Unpublished')
            ->setCheckedValue('y')
            ->setUncheckedValue('n');
        $this->addElement($hide);

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
        $hash = $this->createElement('hash', 'csrf_token_article', array('salt' => 'unique'));
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

        $preview = $this->createElement('submit', 'preview');
        $preview
            ->setAttrib('class', 'btn-default')
            ->setLabel('Preview');
        $this->addElement($preview);
    }
}
