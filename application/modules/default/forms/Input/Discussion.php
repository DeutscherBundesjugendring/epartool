<?php

class Default_Form_Input_Discussion extends Dbjr_Form_Web
{

    /**
     * @var bool
     */
    protected $videoEnabled;

    public function __construct($options = null, $videoEnabled = false)
    {
        $this->videoEnabled = $videoEnabled;
        parent::__construct($options);
    }

    public function init()
    {
        $this->setMethod('post');
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'input/addDiscussionForm.phtml'))));

        $body = $this->createElement('textarea', 'body');
        $placeholder = Zend_Registry::get('Zend_Translate')->translate('Your discussion post');
        $body
            ->setAttrib('rows', 5)
            ->setAttrib('placeholder', $placeholder);
        $this->addElement($body);


        $email = $this->createElement('text', 'email');
        $email
            ->setLabel('Your email')
            ->setRequired(true)
            ->setAttrib('type', 'email')
            ->setAttrib('placeholder', '@')
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        if ($this->videoEnabled && (new Model_Projects())->getVideoServiceStatus()) {
            $project = (new Model_Projects())->find((new Zend_Registry())->get('systemconfig')->project)->current();
            $videoServiceEl = $this->createElement('select', 'video_service');
            $videoServiceOptions = [];
            $urls = [];
            foreach (['youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook'] as $service => $name) {
                if ($project['video_' . $service . '_enabled']) {
                    $videoServiceOptions[$service] = $name;
                    $urls[$service] = sprintf(Zend_Registry::get('systemconfig')->video->url->$service->format->link, '');
                }
            }
            $videoServiceEl->setMultioptions($videoServiceOptions)->setOptions(['data-url' => json_encode($urls)]);
            $this->addElement($videoServiceEl);

            $placeholder = Zend_Registry::get('Zend_Translate')->translate('e.g.');
            $videoId = $this->createElement('text', 'video_id');
            $videoId
                ->setAttrib('class', 'form-control')
                ->addValidator(new Dbjr_Validate_VideoValidator());
            $videoId->setDecorators(['ViewHelper',
                [
                    ['inputGroup' => 'HtmlTag'],
                    ['tag' => 'div'],
                ],
            ]);
            $this->addElement($videoId);
        }

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-default')
            ->setLabel('Send');
        $this->addElement($submit);

        $hash = $this->createElement('hash', 'csrf_token_register', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isValid($data)
    {
        $bodyEl = $this->getElement('body');
        $videoIdEl = $this->getElement('video_id');
        $bodyEl->clearErrorMessages();
        if ($videoIdEl !== null) {
            $videoIdEl->clearErrorMessages();
        }
        if (empty($data['body']) && empty($data['video_id'])) {
            $msg = Zend_Registry::get('Zend_Translate')->translate('Either text or video have to be submitted.');
            $bodyEl->addError($msg);
            if ($videoIdEl !== null) {
                $videoIdEl->addError($msg);
            }
            $this->markAsError();
        }

        return parent::isValid($data);
    }
}
