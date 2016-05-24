<?php

class Admin_View_Helper_HelpText extends Zend_View_Helper_Abstract
{
    public function helpText($name)
    {
        $helpText = (new Model_HelpText())->fetchRow(['name = ?' => $name]);
        return '<button type="button" class="item-action pull-right" data-toggle="modal" data-target="#helpTextModal">'
            . '<span class="glyphicon glyphicon-question-sign"></span></button>'
            . '<div class="modal fade" id="helpTextModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">'
            . '    <div class="modal-dialog" role="document">'
            . '        <div class="modal-content">'
            . '            <div class="modal-header">'
            . '                <button type="button" class="close" data-dismiss="modal" aria-label="Close">'
            . '                    <span aria-hidden="true">&times;</span>'
            . '                </button>'
            . '                <h4 class="modal-title" id="myModalLabel">' . Model_HelpText::getTranslatedName($name) . '</h4>'
            . '            </div>'
            . '            <div class="modal-body">' . $helpText['body'] . '</div>'
            . '            <div class="modal-footer">'
            . '                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'
            . '            </div>'
            . '        </div>'
            . '    </div>'
            . '</div>';
    }
}
