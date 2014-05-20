<?php

class Dbjr_Mail extends Zend_Mail
{
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
            $templateModel->select()->where('name', $templateName)
        );

        if (!$template) {
            throw new Dbjr_Mail_Exception('The requested template doesnt exist.');
        }

        $this->setSubject($template->subject);
        $this->setBodyHtml($template->bodyHtml);
        $this->setBodyText($template->bodyText);

        return $this;
    }

    /**
     * Sets the placeholders for the email
     * @param  array     $placeholders  An array holding the placeholders [placeholderName => value]
     */
    public function setPlaceholders(array $placeholders)
    {
        $this->_placeholders = array_merge($placeholders, $this->_placeholders);
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
            $text = str_replace('{{' . $key . '}}', $val, $text);
        }

        return $text;
    }

    /**
     * Replaces the placeholders in subject, bodyText and bodyHtml
     * @return Dbjr_Mail  Fluent interface
     */
    protected function replacePlaceholders()
    {
        $subject = $this->getSubject();
        $subject = $this->replaceTokens($subject, $this->_placeholders);
        $this->_subject = null;
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

        $bodyText = $this->getBodyText()->getRawContent();
        $bodyText = $this->replaceTokens($bodyText, $components['bodyText']);
        $this->setBodyText($bodyText);

        $bodyHtml = $this->getBodyHtml()->getRawContent();
        $bodyHtml = $this->replaceTokens($bodyHtml, $components['bodyHtml']);
        $this->setBodyHtml($bodyHtml);

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

        $data = array(
            'project_code' => Zend_Registry::get('systemconfig')->project,
            'sent_by_user' => $sentByUser,
            'subject' => $this->getSubject(),
            'body_text' => $this->getBodyText()->getRawContent(),
            'body_html' => $this->getBodyHtml()->getRawContent(),
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
}
