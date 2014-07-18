<?php

class Model_Mail_Placeholder extends Dbjr_Db_Table_Abstract
{
    const GLOBAL_PLACEHOLDER_FROM_NAME = 'from_name';
    const GLOBAL_PLACEHOLDER_FROM_ADDRESS = 'from_address';
    const GLOBAL_PLACEHOLDER_CONTACT_NAME = 'contact_name';
    const GLOBAL_PLACEHOLDER_CONTACT_WWW = 'contact_www';
    const GLOBAL_PLACEHOLDER_CONTACT_EMAIL = 'contact_email';
    const GLOBAL_PLACEHOLDER_SEND_DATE = 'send_date';

    protected $_name = 'email_placeholder';
    protected $_primary = 'id';

    /**
     * Returns mail placeholders that can be used in the given template
     * @param  integer               $templateId The template identifier
     * @return Zend_Db_Table_Rowset              The relevant placeholders
     */
    public function getByTemplateId($templateId)
    {
        $select = $this
            ->select(true)
            ->from(['mp' => $this->_name])
            ->setIntegrityCheck(false)
            ->joinLeft(
                array('ethetp' => 'email_template_has_email_placeholder'),
                'ethetp.email_placeholder_id = mp.id',
                []
            )
            ->where('mp.is_global=?', true);
        if ($templateId) {
            $select->where('ethetp.email_template_id=?', $templateId);
        }

        return $this->fetchAll($select);
    }

    /**
     * Returns global placeholder values
     * @return array   The placeholders in array [name => value]
     */
    public function getGlobalValues()
    {
        $config = Zend_Registry::get('systemconfig');
        $mailer = new Zend_Mail();
        return array(
            self::GLOBAL_PLACEHOLDER_FROM_NAME => $mailer->getDefaultFrom()['name'],
            self::GLOBAL_PLACEHOLDER_FROM_ADDRESS => $mailer->getDefaultFrom()['email'],
            self::GLOBAL_PLACEHOLDER_CONTACT_NAME => $config->contact->name,
            self::GLOBAL_PLACEHOLDER_CONTACT_WWW => $config->contact->www,
            self::GLOBAL_PLACEHOLDER_CONTACT_EMAIL => $config->contact->email,
            self::GLOBAL_PLACEHOLDER_SEND_DATE => Zend_Date::now()->get(Zend_Date::DATE_MEDIUM),
        );
    }
}
