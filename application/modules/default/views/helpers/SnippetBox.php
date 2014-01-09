<?php
/**
 * renders a snippet for followup timeline
 *
 * @desc renders a snippet for followup timeline
 * @author Marco Dinnbier
 */
class Zend_View_Helper_SnippetBox extends Zend_View_Helper_Abstract
{
    public function snippetBox($snippet, $followpathlink = false, $relfowupcount = true)
    {
            $html = '';
            $html .= '<div class="timeline-box openoverlay" data-href="'.$this->view->url(array('action' => 'json', 'ffid' => (int) $snippet['ffid'], 'fid' => null, 'tid' => null, 'page' => null)).'"    data-fid="'.$snippet['fid'].'">';
            $hastypoverlay = $snippet['typ'] !== 'g' ? 'has-typ-overlay' : '';
            $html .= '<div class="content clearfix '.$hastypoverlay.'">';
            if ($snippet['typ'] !== 'g') {
                    $html .= '<div class="followup-typ edge-left followup-typ-'.$snippet['typ'].'"> </div>';
                    $html .= '<div class="followup-typ gfx-who-overlay followup-typ-'.$snippet['typ'].'"> </div>';
            }
            $html .= '<div class="followup-gfx-who-wrapper">';
            $html .= '<img class="gfx_who_thumb" src="'.$snippet['gfx_who'].'" />';
            $html .= '</div>';
            $html .= $snippet['expl'];
            $html .= '<div class="clearleft">';
            $html .= '<a class="voting like" href="'.$this->view->url(array('action' => 'like', 'fid' => (int) $snippet['fid'], 'page' => null)).'"><span class="amount">('.$snippet['lkyea'].')</span><span class="thumb-up"></span></a>';
            $html .= '<a class="voting dislike" href="'.$this->view->url(array('action' => 'unlike', 'fid' => (int) $snippet['fid'], 'page' => null)).'"><span class="amount">('.$snippet['lknay'].')</span><span class="thumb-down"></span></a>';
            $html .= '</div>';
            if ($followpathlink) {
                $html .= '<a class="btn" href="'.$this->view->url(array(
                                                            'action' => 'show-by-snippet',
                                                            'controller' => 'followup',
                                                            'kid' => $this->view->kid,
                                                            'fid' => $snippet['fid']
                                                                    ), null, true).'">Diesem Pfad folgen</a>';

            }
            $html .= '</div>';
            if ($snippet['relFowupCount'] != 0 && $relfowupcount) {
                $html .= '<div class="timeline-countlink sprite">';
                $html .= '<a class="ajaxclick" href="'.$this->view->url(array('action' => 'json', 'fid' => $snippet['fid'], 'ffid' => null, 'tid' => null, 'page' => null)).'">'.$snippet['relFowupCount'].'</a>';
                $html .= '</div>';
            }
            $html .= '</div>';

        return $html;
    }
}
