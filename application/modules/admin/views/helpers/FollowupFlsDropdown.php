<?php
/**
 * Article Navigation
 *
 * @desc Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 * @author Markus Hackel
 */
class Zend_View_Helper_FollowupFlsDropdown extends Zend_View_Helper_Abstract
{
    public function followupFlsDropdown($activeItem = NULL, $name = NULL, $excludeFfid = NULL)
    {
        $con = $this->view->consultation;

        $Model_FollowupFiles = new Model_FollowupFiles();
        $followupFiles = $Model_FollowupFiles->getByKid($con->kid,'when DESC');

        $name = $name ? 'name="'.$name.'"' : '';

        $html = '<select '.$name.'>';

        foreach ($followupFiles as $item) {
         if ($excludeFfid && $excludeFfid == $item['ffid']) {

             continue;
         }
            if ($activeItem == $item['ffid']) {

                $html.= '<option selected="selected" value="'.$item['ffid'].'">';
            } else {

                $html.= '<option value="'.$item['ffid'].'">';
            }
            $html.= $item['titl'];

            $html.= '</option>';

        }
        $html.= '</select>';

        return $html;
    }
}
