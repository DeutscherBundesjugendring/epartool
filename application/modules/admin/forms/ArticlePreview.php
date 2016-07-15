<?php

class Admin_Form_ArticlePreview extends Dbjr_Form_Admin
{
    public function init()
    {
        $id = $this->createElement('hidden', 'art_id');
        $this->addElement($id);

        $desc = $this->createElement('hidden', 'desc');
        $desc
            ->addValidator('NotEmpty');
        $this->addElement($desc);

        $refName = $this->createElement('hidden', 'ref_nm');
        $this->addElement($refName);

        $parentId = $this->createElement('hidden', 'parent_id');
        $this->addElement($parentId);

        $body = $this->createElement('hidden', 'artcl');
        $body
            ->addValidator('NotEmpty')
            ->addFilter('HtmlEntities');
        $this->addElement($body);

        $sidebar = $this->createElement('hidden', 'sidebar');
        $sidebar
            ->addValidator('NotEmpty')
            ->addFilter('HtmlEntities');
        $this->addElement($sidebar);

        $hide = $this->createElement('hidden', 'hid');
        $hide->setRequired(true);
        $this->addElement($hide);

        $project = $this->createElement('hidden', 'proj');
        $project->addValidator('NotEmpty');
        $this->addElement($project);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_article_preview', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'backFromPreview');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('< Back to edit mode');
        $this->addElement($submit);


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->setDecorators(array('ViewHelper'));
        }
    }
}
