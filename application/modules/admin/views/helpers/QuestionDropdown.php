<?php
/**
 * Article Navigation
 *
 * @desc Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 * @author Markus Hackel
 */
class Zend_View_Helper_QuestionDropdown extends Zend_View_Helper_Abstract
{
    public function questionDropdown($activeItem = NULL, $name = NULL)
    {
        $con = $this->view->consultation;

        $questionModel = new Model_Questions();
        $items = $questionModel->getByConsultation($con->kid);
        $name = $name ? 'name="'.$name.'"' : '';

        $html = '<select '.$name.'>';

        foreach ($items as $item) {

            if ($activeItem == $item['qi']) {
                
                $html.= '<option selected="selected" value="'.$item['qi'].'">';
            } else {

                $html.= '<option value="'.$item['qi'].'">';
            }
            $html.= (isset($item['nr']) ? $item['nr'] : '') .' '.$item['q'];

            $html.= '</option>';

        }
        $html.= '</select>';

        return $html;
    }
}
