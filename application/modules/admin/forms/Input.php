<?php

class Admin_Form_Input extends Dbjr_Form_Admin
{
    const AFTER_SUBMIT_RETURN_TO_INDEX = 'after_submit_return_to_index';
    const AFTER_SUBMIT_SPLIT_NEXT = 'after_submit_split_next';

    /**
     * @var string
     */
    protected $cancelUrl;

    /**
     * @var array
     */
    protected $contribution;

    /**
     * @var string
     */
    private $afterSubmitAction;

    /**
     * Admin_Form_Input constructor.
     * @param null $cancelUrl
     * @param string $afterSubmitAction
     */
    public function __construct($cancelUrl = null, $afterSubmitAction = self::AFTER_SUBMIT_RETURN_TO_INDEX)
    {
        $this->cancelUrl = $cancelUrl;
        $this->afterSubmitAction = $afterSubmitAction;
        parent::__construct();
    }

    public function init()
    {
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'input/inputForm.phtml'))));
        
        $kid = Zend_Controller_Front::getInstance()->getRequest()->getParam('kid', 0);
        $translator = Zend_Registry::get('Zend_Translate');

        $this->setMethod('post')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => $this->cancelUrl]);

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

        $multiOptions = (new Model_Tags())->getAdminInputFormMulticheckboxOptions();
        $tags = $this->createElement('multiselect', 'tags')
            ->setLabel('Tags')
            ->setMultiOptions($multiOptions)
            ->setSeparator(' ')
            ->setIsSelect2(true);
        $this->addElement($tags);

        $userConfirmation = $this->createElement('radio', 'user_conf');
        $userConfirmation
            ->setLabel('User confirmation')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'u' => $translator->translate('Unknown'),
                    'c' => $translator->translate('Confirmed'),
                    'r' => $translator->translate('Rejected'),
                ]
            );
        $this->addElement($userConfirmation);

        $adminConfirmation = $this->createElement('radio', 'block');
        $adminConfirmation
            ->setLabel('Admin confirmation')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'u' => $translator->translate('Unknown'),
                    'n' => $translator->translate('Confirmed'),
                    'y' => $translator->translate('Blocked'),
                ]
            );
        $this->addElement($adminConfirmation);

        $enableVoting = $this->createElement('radio', 'vot');
        $enableVoting
            ->setLabel('Enable voting')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'u' => $translator->translate('Unknown'),
                    'n' => $translator->translate('No'),
                    'y' => $translator->translate('Yes'),
                ]
            );
        $this->addElement($enableVoting);

        $note = $this->createElement('textarea', 'notiz');
        $note
            ->setLabel('Internal note')
            ->setAttrib('rows', 5);
        $this->addElement($note);

        $videoServiceEl = $this->createElement('select', 'video_service');
        $videoServiceOptions = ['youtube' => 'Youtube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook'];
        $videoServiceEl->setMultioptions($videoServiceOptions)->setOptions([
            'data-url' => json_encode([
                'youtube' => sprintf(Zend_Registry::get('systemconfig')->video->url->youtube->format->link, ''),
                'vimeo' => sprintf(Zend_Registry::get('systemconfig')->video->url->vimeo->format->link, ''),
                'facebook' => sprintf(Zend_Registry::get('systemconfig')->video->url->facebook->format->link, ''),
            ])
        ]);
        $this->addElement($videoServiceEl);

        $videoIdEl = $this->createElement('text', 'video_id');
        $videoIdEl->addValidator(new Dbjr_Validate_VideoValidator());
        $this->addElement($videoIdEl);

        // CSRF Protection
        $hash = $this->getHash();
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit->setAttrib('class', 'btn-primary');
        if ($this->afterSubmitAction === self::AFTER_SUBMIT_RETURN_TO_INDEX) {
            $submit->setLabel('Save and return to index');
        } elseif ($this->afterSubmitAction === self::AFTER_SUBMIT_SPLIT_NEXT) {
            $submit->setLabel('Save and continue');
        }
        $this->addElement($submit);
    }

    /**
     * @return \Zend_Form_Element
     * @throws \Zend_Exception
     * @throws \Zend_Form_Exception
     */
    public function getHash()
    {
        $hash = $this->createElement('hash', 'csrf_token_inputadmin', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        return $hash;
    }
    
    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * @param array $contribution
     */
    public function setContribution(array $contribution)
    {
        $this->contribution = $contribution;
    }

    /**
     * @return array
     */
    public function getContribution()
    {
        return $this->contribution;
    }
}
