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
        $items = [
            [
                'name' => 'site',
                'label' => $this->view->translate('Site'),
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ],
            [
                'name' => 'pages',
                'label' => $this->view->translate('Pages'),
                'href' => $this->view->url(array('controller' => 'article', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'article', 'action' => 'create')),
            ],
            [
                'name' => 'keywords',
                'label' => $this->view->translate('Keywords'),
                'href' => $this->view->url(array('controller' => 'tag', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'tag', 'action' => 'create')),
            ],
            [
                'name' => 'contribution',
                'label' => $this->view->translate('Contribution'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'contribution-submission-form']),
            ],
            [
                'name' => 'voting',
                'label' => $this->view->translate('Voting'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'voting']),
            ],
            [
                'name' => 'helpTexts',
                'label' => $this->view->translate('Help Texts'),
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'help-text-index')),
            ],
            [
                'name' => 'footer',
                'label' => $this->view->translate('Footer'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'footer', 'id' => null]),
            ],
            [
                'name' => 'services',
                'label' => $this->view->translate('Services'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'services']),
            ],
            [
                'name' => 'lookAndFeel',
                'label' => $this->view->translate('Look and Feel'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'look-and-feel']),
            ],
        ];

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
