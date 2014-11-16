<?php

class Admin_Form_Input extends Dbjr_Form_Admin
{
    protected $_cancelUrl;

    public function __construct($cancelUrl = null)
    {
        $this->_cancelUrl = $cancelUrl;
        parent::__construct();
    }

    public function init()
    {
        $kid = Zend_Controller_Front::getInstance()->getRequest()->getParam('kid', 0);
        $view = new Zend_View();

        $this->setMethod('post')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => $this->_cancelUrl]);

        $selectOptions = (new Model_Questions())->getAdminInputFormSelectOptions($kid);
        $questionId = $this->createElement('select', 'qi');
        $questionId
            ->setLabel('Question')
            ->setRequired(true)
            ->setMultiOptions($selectOptions)
            ->addValidator('Int');
        $this->addElement($questionId);

        $thes = $this->createElement('textarea', 'thes');
        $thes
            ->setLabel('Theses')
            ->setRequired(true)
            ->setAttrib('rows', 5);
        $this->addElement($thes);

        $expl = $this->createElement('textarea', 'expl');
        $expl
            ->setLabel('Explanation')
            ->setAttrib('rows', 5);
        $this->addElement($expl);

        $note = $this->createElement('textarea', 'notiz');
        $note
            ->setLabel('Internal note')
            ->setAttrib('rows', 5);
        $this->addElement($note);

        $userConfirmation = $this->createElement('radio', 'user_conf');
        $userConfirmation
            ->setLabel('User confirmation')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'u' => $view->translate('Unknown'),
                    'c' => $view->translate('Confirmed'),
                    'r' => $view->translate('Rejected'),
                ]
            );
        $this->addElement($userConfirmation);

        $adminConfirmation = $this->createElement('radio', 'block');
        $adminConfirmation
            ->setLabel('Admin confirmation')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'u' => $view->translate('Unknown'),
                    'n' => $view->translate('Confirmed'),
                    'y' => $view->translate('Blocked'),
                ]
            );
        $this->addElement($adminConfirmation);

        $enableVoting = $this->createElement('radio', 'vot');
        $enableVoting
            ->setLabel('Enable voting')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'u' => $view->translate('Unknown'),
                    'n' => $view->translate('No'),
                    'y' => $view->translate('Yes'),
                ]
            );
        $this->addElement($enableVoting);


        $multiOptions = (new Model_Tags())->getAdminInputFormMulticheckboxOptions();
        $tags = $this->createElement('multiselect', 'tags')
            ->setLabel('Tags')
            ->setMultiOptions($multiOptions)
            ->setSeparator(' ')
            ->setIsSelect2(true);
        $this->addElement($tags);

        // CSRF Protection
        $hash = $this->getHash();
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }

	 public function getHash() {
	 	$hash = $this->createElement('hash', 'csrf_token_inputadmin', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
		return $hash;
	 }
}
