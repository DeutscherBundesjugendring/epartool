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
     * Saves the email in db so it can be later send by a cronjob. The placeholders in body and subject field are replaced.
     * @param  Zend_Mail_Transport_Abstract $trasnport The transport class for this email
     * @return Dbjr_Mail                               Fluent interface
     */
    public function send($transport = null)
    {
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

        $data = array(
            'project_code' => Zend_Registry::get('systemconfig')->project,
            'sent_by_user' => $sentByUser,
            'subject' => $this->getSubjectRaw(),
            'body_text' => $bodyText,
            'body_html' => $bodyHtml,
            'to' => $this->_toFull,
            'cc' => $this->_ccFull,
            'bcc' => $this->_bccFull,
        );
        $mailModel = new Model_Mail();
        $mailModel->insert($data);

        return $this;
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
     * @param integer           $kid         The consultation identifier
     * @param Dbjr_Db_Criteria  $dbCriteria  Criteria to limit the search
     */
    public function addRecipientsConsultationParticipants($kid, $dbCriteria, $recipientType = null)
    {
        $userModel = new Model_Users();
        $users = $userModel->getParticipantsByConsultation($kid, $dbCriteria);
        $this->addRecipientsUsers($users, $recipientType);
    }

    /**
     * Adds all consultatioin  voters as bcc recipients
     * @param integer           $kid         The consultation identifier
     * @param Dbjr_Db_Criteria  $dbCriteria  Criteria to limit the search
     */
    public function addRecipientsConsultationVoters($kid, $dbCriteria, $recipientType = null)
    {
        $userModel = new Model_Users();
        $users = $userModel->getVotersByConsultation($kid, $dbCriteria);
        $this->addRecipientsUsers($users, $recipientType);
    }

    /**
     * Adds user emails as recipients
     * @param Zend_Db_Table_Rowset $users  The users for whom the email addresses are to be added
     */
    protected function addRecipientsUsers($users, $recipientType = null)
    {
        if ($recipientType === null) {
            $recipientType = self::RECIPIENT_TYPE_BCC;
        }

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
}
