<?php

class Admin_Form_Followup_Snippet extends Dbjr_Form_Admin
{

    public function init()
    {
        $view = new Zend_View();

        $this->setMethod('post');

        $expl = $this->createElement('textarea', 'expl');
        $expl
            ->setLabel('Explanation')
            ->setAttrib('rows', 5)
            ->setAttrib('rows', 10000)
            ->addFilter('HtmlEntities')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);
        $this->addElement($expl);

        $type = $this->createElement('radio', 'typ');
        $type
            ->setLabel('Type')
            ->setRequired(true)
            ->setMultioptions(Model_Followups::getTypes())
            ->setValue('g');
        $this->addElement($type);

        $hierarchy = $this->createElement('radio', 'hlvl');
        $hierarchy
            ->setLabel('Hierarchy')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    '0' => $view->translate('Footnote'),
                    '1' => $view->translate('Body'),
                    '2' => $view->translate('Heading 1'),
                    '3' => $view->translate('Heading 2'),
                    '4' => $view->translate('Heading 3'),
                    '5' => $view->translate('Heading 4'),
                    '6' => $view->translate('Heading 5'),
                ]
            )
            ->setValue(1);
        $this->addElement($hierarchy);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }
}
