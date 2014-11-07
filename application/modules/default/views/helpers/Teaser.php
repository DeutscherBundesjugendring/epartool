<?php
/**
 * Teaser, shows the most relevant consultations with links to the current
 * participation stage
 */
class Zend_View_Helper_Teaser extends Zend_View_Helper_Abstract
{
    public function teaser()
    {
        $html = '<ul class="nav nav-list teaser hidden-print">';

        $consultationModel = new Model_Consultations();
        $items = $consultationModel->getTeaserEntries();

        foreach ($items as $item) {
            $html.= '<li>';
            switch ($item['relevantField']) {
                case 'inp_fr':
                    $url = $this->view->url(array(
                        'controller' => 'input',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = $this->view->translate('You can publish your contribution now!')
                        . $this->view->translate('From') . ' '
                        . $this->view->formatDate($item['inp_fr'], 'dd.MM.yyyy') . ' '
                        . $this->view->translate('until') . ' '
                        . $this->view->formatDate($item['inp_to'], 'dd.MM.yyyy') . ' …';
                    break;
                case 'inp_to':
                    $url = $this->view->url(array(
                        'controller' => 'input',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = $this->view->translate('The contribution phase has finished. You can vote soon:')
                        . $this->view->translate('from') . ' '
                        . $this->view->formatDate($item['vot_fr'], 'dd.MM.yyyy') . ' '
                        . $this->view->translate('until') . ' '
                        . $this->view->formatDate($item['vot_to'], 'dd.MM.yyyy') . ' …';
                    break;
                case 'vot_fr':
                    $url = $this->view->url(array(
                        'controller' => 'voting',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = $this->view->translate('Vote now on the most important contributions!') . ' …';
                    break;
                case 'vot_to':
                    $url = $this->view->url(array(
                        'controller' => 'input',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = $this->view->translate('Voting has finished! There will soon be reactions available') . ' …';
                    break;
                default:
                    $url = $this->view->url(array(
                        'controller' => 'article',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = $this->view->translate('Get informed now') . ' …';
            }
            $html.= '<a href="' . $url . '"><span class="title">'
                . '<h2>' . $item['titl'] . ':</h2> '
                . $text
                . '</span>'
                . '<i class="icon-angle-right icon-white icon-2x"></i>'
                . '</a>'
                . '</li>';
        }

        // Link to consultation overview
        $html.= '<li><a href="' . $this->view->url(array(
                'controller' => 'consultation'
            ), 'default', true) . '">'
            . '<strong>' . $this->view->translate('Looking for other consultations? View all') . ' …</strong>'
            . '<i class="icon-angle-right icon-white icon-2x"></i>'
            . '</a></li>';

        $html.= '</ul>';

        return $html;
    }
}
