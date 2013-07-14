<?php

/**
 * Second Navigation
 *
 * @desc Navigation der 2. Ebene (Hauptaspekte einer Konsultation)
 * @author Markus Hackel
 */
class Zend_View_Helper_SecondNavigation extends Zend_View_Helper_Abstract {

	public function secondNavigation($activeItem = null) {
		$date = new Zend_Date();
		$nowDate = Zend_Date::now();
		$con = $this->view->consultation;
		$disabled = array(
			'article' => false,
			'question' => false,
			'input' => ($nowDate->isEarlier($con->inp_fr)),
			//'voting' => ($nowDate->isEarlier($con->vot_fr) || $nowDate->isLater($con->vot_to)),
			'follow-up' => (!$nowDate->isLater($con->vot_to) || $con->follup_show == 'n'),
			'voting' => ($nowDate->isEarlier($con->vot_fr) || $con->vot_to == '0000-00-00 00:00:00'),
		);

		// Voting disable result
		if ($con->vot_to != '0000-00-00 00:00:00' && $nowDate->isLater($con->vot_to) && $con->vot_res_show == 'n') {
			$disabled['voting'] = true;
		}

		$items = array(
			'article' => array(
				'url' => $this->view->baseUrl() . '/article/index/kid/' . $con->kid,
				'text' => '<h2>Infos</h2>',
				'showBubble' => FALSE
			),
			'question' => array(
				'url' => $this->view->baseUrl() . '/question/index/kid/' . $con->kid,
				'text' => '<h2>Fragen</h2>',
				'showBubble' => FALSE
			),
			'input' => array(
				'url' => $this->view->baseUrl() . '/input/index/kid/' . $con->kid,
				'text' => '<h2>Beiträge</h2>',
				'showBubble' => FALSE
			),
			'voting' => array(
				'url' => $this->view->baseUrl() . '/voting/index/kid/' . $con->kid,
				'text' => '<h2>Abstimmung</h2>',
				'showBubble' => FALSE
			),
			'follow-up' => array(
				'url' => $this->view->baseUrl() . '/followup/index/kid/' . $con->kid,
				'text' => '<h2>Reaktionen & Wirkung</h2> <small class="info">nach Ende der Abstimmung</small>',
				'showBubble' => FALSE
			),
		);

		// Add dates
		if ($con->inp_show == 'y') {
			$items['input']['text'].= ' <small class="info">vom '
					. $date->set($con->inp_fr)->get(Zend_Date::DATE_MEDIUM)
					. '<br />'
					. 'bis '
					. $date->set($con->inp_to)->get(Zend_Date::DATE_MEDIUM)
					. '</small>';
		}
		if ($con->vot_show == 'y') {
			$items['voting']['text'].= ' <small class="info">vom '
					. $date->set($con->vot_fr)->get(Zend_Date::DATE_MEDIUM)
					. '<br />'
					. 'bis '
					. $date->set($con->vot_to)->get(Zend_Date::DATE_MEDIUM)
					. '</small>';
		}

		// Add bubbles
		if ($nowDate->isLater($con->inp_fr) && $nowDate->isEarlier($con->inp_to)) {
			$items['input']['showBubble'] = TRUE;
		}
		if ($nowDate->isLater($con->vot_fr) && $nowDate->isEarlier($con->vot_to)) {
			$items['voting']['showBubble'] = TRUE;
		}

		// Render
		$html = '<nav role="navigation" class="consultation-nav secondary-navigation">'
				. '<ul class="nav nav-tabs">';
		foreach ($items as $item => $val) {
			$liClasses = array();
			if ($item == $activeItem) {
				$liClasses[] = 'active';
			}
			if ($disabled[$item]) {
				$liClasses[] = 'item-disabled';
			}
			$html .= '<li class="' . implode(' ', $liClasses) . '">';
			if (!empty($val['url']) && !in_array('disabled', $liClasses)) {
				$html .= '<a href="' . $val['url'] . '">';
				$html .= $val['text'];
				if ($val['showBubble']) {
					$html .= '<span class="bubble bubble-middle"><h3>';
					if ($item == 'input') {
						$html .= 'Jetzt mitmachen!';
					} elseif ($item == 'voting') {
						$html .= 'Jetzt abstimmen!';
					}
					$html .= '</h3></span>';
				}
				$html .= '</a>';
			} else {
				$html .= '<div>' . $val['text'] . '</div>';
			}
			$html .= '</li>';
		}
		$html .= '</ul></nav>';
		return $html;
	}

}
