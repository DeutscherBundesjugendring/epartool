<?php

class Dbjr_Mail extends Zend_Mail
{
    const TOKEN_OPEN = '{{';
    const TOKEN_CLOSE = '}}';

    const RECIPIENT_TYPE_TO = 'to';
    const RECIPIENT_TYPE_CC = 'cc';
    const RECIPIENT_TYPE_BCC = 'bcc';

    /**
     * Holds the email subject unencoded
     * @var string
     */
    protected $_subjectRaw;

    /**
     * Indicates if the mail was sent manually
     * @var boolean
     */
    protected $_isManualSent;

    /**
     * Holds the placeholder values in format [placeholderName => value]
     * @var array
     */
    protected $_placeholders;

    /**
     * Holds the components as array of component arrays
     * @var array
     */
    protected $_components;

    /**
     * Holds the Cc recipients in format [name => email]
     * @var array
     */
    protected $_ccFull = array();

    /**
     * Holds the Bcc recipients in format [name => email]
     * @var array
     */
    protected $_bccFull = array();

    /**
     * Holds the To recipients in format [name => email]
     * @var array
     */
    protected $_toFull = array();

    /**
     * Holds the attachment filepaths relative to media/
     * @var array
     */
    protected $_attachmentFiles = array();

    /**
     * Constructor
     * @param string $charset The charset of this mail
     */
    public function __construct($charset = null)
    {
        $placeholderModel = new Model_Mail_Placeholder();
        $this->_placeholders = $placeholderModel->getGlobalValues();

        $componentModel = new Model_Mail_Component();
        $this->_components = $componentModel->fetchAll()->toArray();

        parent::__construct();
    }

    /**
     * Sets the message body (html+text) and attachment from template
     * @param  string    $templateName  The identification of the template to use
     * @return Dbjr_Mail                Fluent interface
     */
    public function setTemplate($templateName)
    {
        $templateModel = new Model_Mail_Template();
        $template = $templateModel->fetchRow(
            $templateModel->select()->where('name=?', $templateName)
        );

        if (!$template) {
            throw new Dbjr_Mail_Exception('The requested template doesnt exist.');
        }

        $this->setSubject($template->subject);
        $this->setBodyHtml($template->body_html);
        $this->setBodyText($template->body_text);

        return $this;
    }

    /**
     * Sets the placeholders for the email
     * @param  array     $placeholders  An array holding the placeholders [placeholderName => value]
     * @return Dbjr_Mail                Fluent interface
     */
    public function setPlaceholders(array $placeholders)
    {
        $this->_placeholders = array_merge($placeholders, $this->_placeholders);

        return $this;
    }

    /**
     * Replaces a token in text
     * @param  string $text   The text where tokens are to be replaced
     * @param  array  $tokens The toekns in format: [name => value]
     * @return string         The text with tokens replaced
     */
    protected function replaceTokens($text, $tokens)
    {
        foreach ($tokens as $key => $val) {
            $text = str_replace(self::TOKEN_OPEN . $key . self::TOKEN_CLOSE, $val, $text);
        }

        return $text;
    }

    /**
     * Replaces the placeholders in subject, bodyText and bodyHtml
     * @return Dbjr_Mail  Fluent interface
     */
    protected function replacePlaceholders()
    {
        $subject = $this->getSubjectRaw();
        $subject = $this->replaceTokens($subject, $this->_placeholders);
        $this->clearSubject();
        $this->setSubject($subject);

        $bodyText = $this->getBodyText()->getRawContent();
        $bodyText = $this->replaceTokens($bodyText, $this->_placeholders);
        $this->setBodyText($bodyText);

        $bodyHtml = $this->getBodyHtml()->getRawContent();
        $bodyHtml = $this->replaceTokens($bodyHtml, $this->_placeholders);
        $this->setBodyHtml($bodyHtml);

        return $this;
    }

    /**
     * Replaces the components in text with the values
     * @return Dbjr_Mail  Fluent interface
     */
    protected function replaceComponents()
    {
        $components = array();
        foreach ($this->_components as $component) {
            $components['bodyHtml'][$component['name']] = $component['body_html'];
            $components['bodyText'][$component['name']] = $component['body_text'] ;
        }

        if ($this->_components) {
            $bodyText = $this->getBodyText()->getRawContent();
            $bodyText = $this->replaceTokens($bodyText, $components['bodyText']);
            $this->setBodyText($bodyText);

            $bodyHtml = $this->getBodyHtml()->getRawContent();
            $bodyHtml = $this->replaceTokens($bodyHtml, $components['bodyHtml']);
            $this->setBodyHtml($bodyHtml);
        }

        return $this;
    }

    /**
     * Marks this mail is manually sent by user
     * @param  boolean   $_isManualSent   Indicates if the email was sent manually
     * @return Dbjr_Mail                  Fluent interface
     */
    public function setManualSent($isManualSent)
    {
        $this->_isManualSent = $isManualSent;
        return $this;
    }

    /**
     * This method is here to disable calling parent::send()
     * @param  Zend_Mail_Transport_Abstract $trasnport The transport class for this email
     * @throws Dbjr_Mail_Exception                     This method is not to be called, hence it throws an exception
     * @see self::getEmailData()
     */
    public function send($transport = null)
    {
        throw new Dbjr_Mail_Exception('Dbjr_Mail is not to be send directly. It is to be processed by another object, i.e. Service_Email::queueForSend()');
    }

    /**
     * Saves the email in db so it can be later send by a cronjob. The placeholders in body and subject field are replaced.
     * @throws Dbjr_Mail_Exception                     Throws exception if no recipients are specified
     * @return array                                   The data to be used for sending/processing the mail.
     */
    public function getEmailData()
    {
        if (!$this->_toFull && !$this->_ccFull && !$this->_bccFull) {
            throw new Dbjr_Mail_Exception('Cant send email with no recipients.');
        }

        if ($this->_isManualSent) {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $sentByUser = $identity->email;
        } else {
            $sentByUser = null;
        }

        $this
            ->replaceComponents()
            ->replacePlaceholders();

        $bodyText = $this->getBodyText()->getRawContent();
        $bodyHtml = $this->getBodyHtml()->getRawContent();
        if (!$bodyText) {
            $html2text = new Html2Text\Html2Text($bodyHtml);
            $bodyText = $html2text->get_text();
        }
        $config = Zend_Registry::get('systemconfig');
        $view = new Zend_View();
        $view->addScriptPath(APPLICATION_PATH . '/layouts/scripts');
        $view->assign([
            'siteName' => $config->site->name,
            'logoUrl' => $config->email->logoUrl,
            'bodyHtml' => $bodyHtml,
            'contactInfo' => $config->contact,
            'links' => $config->email->links,
        ]);
        $bodyHtml = $view->render('mail.phtml');

        $data = array(
            'project_code' => Zend_Registry::get('systemconfig')->project,
            'sent_by_user' => $sentByUser,
            'subject' => $this->getSubjectRaw(),
            'body_text' => $bodyText,
            'body_html' => $bodyHtml,
            'to' => $this->_toFull,
            'cc' => $this->_ccFull,
            'bcc' => $this->_bccFull,
            'attachments' => $this->_attachmentFiles,
        );

        return $data;
    }

    /**
     * Adds Cc-header and recipient
     * @param  string|array  $email An array in format [name => email], or a single string address
     * @param  string        $name  Name to be used if $email is string
     * @return Zend_Mail            Provides fluent interface
     */
    public function addCc($email, $name = '')
    {
        if (!is_array($email)) {
            $email = array($name => $email);
        }

        foreach ($email as $n => $recipient) {
            $this->_addRecipientAndHeader('Cc', $recipient, is_int($n) ? '' : $n);
            if (is_int($n) || $n === '') {
                $this->_ccFull[] = $recipient;
            } else {
                $this->_ccFull[$n] = $recipient;
            }
        }

        return $this;
    }

    /**
     * Adds Bcc header and recipient
     * @param  string|array  $email An array in format [name => email], or a single string address
     * @param  string        $name  Name to be used if $email is string
     * @return Zend_Mail            Provides fluent interface
     */
    public function addBcc($email, $name = '')
    {
        if (!is_array($email)) {
            $email = array($name => $email);
        }

        foreach ($email as $n => $recipient) {
            $this->_addRecipientAndHeader('Bcc', $recipient, '');
            if (is_int($n) || $n === '') {
                $this->_bccFull[] = $recipient;
            } else {
                $this->_bccFull[$n] = $recipient;
            }
        }

        return $this;
    }

    /**
     * Adds To-header and recipient
     * @param  string|array  $email An array in format [name => email], or a single string address
     * @param  string        $name  Name to be used if $email is string
     * @return Zend_Mail            Provides fluent interface
     */
    public function addTo($email, $name = '')
    {
        if (!is_array($email)) {
            $email = array($name => $email);
        }

        foreach ($email as $n => $recipient) {
            $this->_addRecipientAndHeader('To', $recipient, is_int($n) ? '' : $n);
            $this->_to[] = $recipient;
            if (is_int($n) || $n === '') {
                $this->_toFull[] = $recipient;
            } else {
                $this->_toFull[$n] = $recipient;
            }
        }

        return $this;
    }

    /**
     * Sets the subject of the message
     * @param   string    $subject
     * @return  Zend_Mail           Provides fluent interface
     * @throws  Zend_Mail_Exception
     */
    public function setSubject($subject)
    {
        if ($this->_subject === null) {
            $subject = $this->_filterOther($subject);
            $this->_subject = $this->_encodeHeader($subject);
            $this->_subjectRaw = $subject;
            $this->_storeHeader('Subject', $this->_subject);
        } else {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('Subject set twice');
        }
        return $this;
    }

    /**
     * Returns the encoded subject of the message
     * @return string
     */
    public function getSubjectRaw()
    {
        return $this->_subjectRaw;
    }

    /**
     * Clears the encoded subject from the message
     * @return  Zend_Mail Provides fluent interface
     */
    public function clearSubject()
    {
        $this->_subject = null;
        $this->_subjectRaw = null;
        $this->clearHeader('Subject');

        return $this;
    }

    /**
     * Adds all consultatioin participants as bcc recipients
     * @param integer   $kid            The consultation identifier
     * @param string    $recipientType  The type of recipient header (see self::RECIPIENT_TYPE_*)
     */
    public function addRecipientsConsultationParticipants($kid, $recipientType, $participantType = null)
    {
        $userModel = new Model_Users();
        $users = $userModel->getParticipantsByConsultation($kid, $participantType);
        $this->addRecipientsUsers($users, $recipientType);
    }

    /**
     * Adds user emails as recipients
     * @param Zend_Db_Table_Rowset $users  The users for whom the email addresses are to be added
     */
    protected function addRecipientsUsers($users, $recipientType)
    {
        foreach ($users as $user) {
            if ($user->email) {
                if ($recipientType === self::RECIPIENT_TYPE_TO) {
                    $this->addTo($user->email);
                } elseif ($recipientType === self::RECIPIENT_TYPE_CC) {
                    $this->addCc($user->email);
                } elseif ($recipientType === self::RECIPIENT_TYPE_BCC) {
                    $this->addBcc($user->email);
                }
            }
        }
    }

    /**
     * Adds attachment file to the email
     * @param   string      $file   The path to the file media root folder
     * @return  Dbjr_Mail           Provide fluent interface
     */
    public function addAttachmentFile($file)
    {
        $this->_attachmentFiles[] = $file;
        return $this;
    }
}
