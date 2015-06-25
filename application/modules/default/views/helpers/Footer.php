<?php

class Module_Default_View_Helper_Footer extends Zend_View_Helper_Abstract
{
    public function footer()
    {
        $footerModel = new Model_Footer();
        $footers = $footerModel->fetchAll($footerModel->select()->order('id ASC'));

        return $this->view->partial('_helpers/footer.phtml', ['footers' => $footers]);
    }
}
