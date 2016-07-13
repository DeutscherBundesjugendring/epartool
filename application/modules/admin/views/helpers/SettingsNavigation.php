<?php

class Admin_View_Helper_SettingsNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation secondary navigation html
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function settingsNavigation($activeItem = null)
    {
        $items = array(
            array(
                'name' => 'site',
                'label' => $this->view->translate('Site'),
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ),
            array(
                'name' => 'pages',
                'label' => $this->view->translate('Pages'),
                'href' => $this->view->url(array('controller' => 'article', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'article', 'action' => 'create')),
            ),
            array(
                'name' => 'keywords',
                'label' => $this->view->translate('Keywords'),
                'href' => $this->view->url(array('controller' => 'tag', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'tag', 'action' => 'create')),
            ),
            [
                'name' => 'voting',
                'label' => $this->view->translate('Voting'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'voting']),
            ],
            array(
                'name' => 'helpTexts',
                'label' => $this->view->translate('Help Texts'),
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'help-text-index')),
            ),
            array(
                'name' => 'footer',
                'label' => $this->view->translate('Footer'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'footer', 'id' => null]),
            ),
            array(
                'name' => 'services',
                'label' => $this->view->translate('Services'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'services']),
            ),
            array(
                'name' => 'lookAndFeel',
                'label' => $this->view->translate('Look and Feel'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'look-and-feel']),
            ),
            array(
                'name' => 'finishContribution',
                'label' => $this->view->translate('Finish contribution'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'finish-contribution']),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
