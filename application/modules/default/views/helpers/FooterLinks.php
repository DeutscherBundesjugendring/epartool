<?php

class Zend_View_Helper_FooterLinks extends Zend_View_Helper_Abstract
{
    public function footerLinks()
    {
        $html = '';

        $articleModel = new Model_Articles();
        $pages = $articleModel->getStaticPages();

        foreach ($pages as $page) {
            $fc = Zend_Controller_Front::getInstance();
            $currentAid = $fc->getRequest()->getParam('aid');
            $html.= '<li';
            if ($currentAid == $page['art_id']) {
                $html.= ' class="active"';
            }
            $html.= '><a href="' . $this->view->url(array(
                    'controller' => 'article',
                    'action' => 'show',
                    'aid' => $page['art_id']
            ), $page['ref_nm'], true) . '">' . $page['desc'] . '</a></li>';
        }

        return $html;
    }
}
