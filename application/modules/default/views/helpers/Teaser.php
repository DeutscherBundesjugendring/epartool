<?php
/**
 * Teaser, shows the most relevant consultations with links to the current
 * participation stage
 *
 */
class Zend_View_Helper_Teaser extends Zend_View_Helper_Abstract
{
    public function teaser()
    {
        $date = new Zend_Date();
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
                    $text = 'Jetzt kannst du deinen Beitrag einstellen! Vom '
                    . $date->set($item['inp_fr'])->get('dd.MM.yyyy') . ' bis '
                    . $date->set($item['inp_to'])->get('dd.MM.yyyy') . ' …';
                    break;
                case 'inp_to':
                    $url = $this->view->url(array(
                        'controller' => 'input',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = 'Die Beitragsphase ist vorbei! Demnächst kann abgestimmt werden: vom '
                    . $date->set($item['vot_fr'])->get('dd.MM.yyyy') . ' bis '
                    . $date->set($item['vot_to'])->get('dd.MM.yyyy') . ' …';
                    break;
                case 'vot_fr':
                    $url = $this->view->url(array(
                        'controller' => 'voting',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = 'Jetzt ist Abstimmen über die wichtigsten Beiträge angesagt! …';
                    break;
                case 'vot_to':
                    $url = $this->view->url(array(
                        'controller' => 'input',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = 'Die Abstimmungsphase ist vorbei! Demnächst wird es Reaktionen geben …';
                    break;
                default:
                    $url = $this->view->url(array(
                        'controller' => 'article',
                        'action' => 'index',
                        'kid' => $item['kid']
                    ), 'default', true);
                    $text = 'Jetzt informieren …';
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
            . '<strong>Auf der Suche nach einer anderen Beteiligungsrunde? Alle in der Übersicht ansehen …</strong>'
            . '<i class="icon-angle-right icon-white icon-2x"></i>'
            . '</a></li>';

        $html.= '</ul>';

        return $html;
    }
}
