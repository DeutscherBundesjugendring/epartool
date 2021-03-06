<?php
/**
 * View Helper für die User Box
 * @author Markus
 *
 */
class Module_Default_View_Helper_UserBox extends Zend_View_Helper_Abstract
{
    public function userBox()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            return $this->view->partial(
                '_helpers/user-box.phtml',
                ['user' => $auth->getIdentity()]
            );
        }
    }
}
