<?php

class Admin_Form_Article extends Dbjr_Form_Admin
{

    public function init()
    {
        $this->setMethod('post');

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
            ->setMultioptions([0 => (new Zend_View())->translate('Please select…')]);
        $this->addElement($refName);

        $parentId = $this->createElement('select', 'parent_id');
        $parentId
            ->setLabel('Parent Page');
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
            ->setRequired(true)
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
            ->setDescription('Note: current project must be always selected.')
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
        $submit->setLabel('Save');
        $this->addElement($submit);

        $preview = $this->createElement('submit', 'preview');
        $preview->setLabel('Preview');
        $this->addElement($preview);
    }
}
