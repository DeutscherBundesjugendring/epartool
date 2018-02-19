<?php
class Plugin_Auth_AuthAdapter implements Zend_Auth_Adapter_Interface
{
    /**
     * The identification string to be checked
     * @var string
     */
    protected $_identity;

    /**
     * The password to be checked
     * @var string
     */
    protected $_password;

    /**
     * The user data
     * @var Zend_Db_Table_Row
     */
    protected $_user;

    /**
     * Constructor
     * @param string $identity The identification string to be checked
     * @param string $password The password to be checked
     */
    public function __construct($identity, $password)
    {
        $this->_identity = $identity;
        $this->_password = $password;
    }

    /**
     * Performs the authentication
     * @return Zend_Auth_Result The result of the authentication process
     */
    public function authenticate()
    {
        $userModel = new Model_Users();
        $user = $userModel->getByEmail($this->_identity);
        $translator = Zend_Registry::get('Zend_Translate');
        if (!$user) {
            $code = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $message = $translator->translate('A record with the supplied identity could not be found.');
        } else {
            if (crypt($this->_password, $user->password) === $user->password) {
                $code = Zend_Auth_Result::SUCCESS;
                $message = $translator->translate('Login successful!');
                $this->_user = $user;
            } else {
                $code = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                $message = $translator->translate('Supplied credential is invalid.');
            }
        }

        return new Zend_Auth_Result($code, $this->_identity, $message ? array($message) : array());
    }

    /**
     * Returns the usee if authentication was success
     * @return  Zend_Db_Table_Row The user data
     */
    public function getResultRowObject()
    {
        return $this->_user ? $this->_user : null;
    }
}
