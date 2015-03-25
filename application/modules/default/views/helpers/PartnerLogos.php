<?php

class Module_Default_View_Helper_PartnerLogos extends Zend_View_Helper_Abstract
{
    public function partnerLogos()
    {
        $partnerModel = new Model_Partner();
        $partners = $partnerModel->fetchAll(
            $partnerModel
                ->select()
                ->order('order ASC')
        );

        return $this->view->partial(
            '_helpers/partner-logos.phtml',
            ['partners' => $partners]
        );
    }
}
