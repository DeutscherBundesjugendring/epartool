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
     * @param string $templateName
     * @return \Zend_Db_Table_Rowset_Abstract
     * @throws \Zend_Db_Table_Exception
     */
    public function getByTemplateName($templateName)
    {
        $select = $this
            ->select(true)
            ->from(['mp' => $this->_name])
            ->setIntegrityCheck(false)
            ->joinLeft(
                ['ethetp' => 'email_template_has_email_placeholder'],
                'ethetp.email_placeholder_id = mp.id',
                []
            )
            ->join(
                ['mt' => (new Model_Mail_Template())->info(Model_Mail_Template::NAME)],
                'ethetp.email_template_id = mt.id',
                []
            )
            ->where('mp.is_global=?', true)
            ->orWhere('mt.name=?', $templateName)
            ->order('mp.name');

        return $this->fetchAll($select);
    }

    /**
     * Returns global placeholder values
     * @return array   The placeholders in array [name => value]
     */
    public function getGlobalValues()
    {
        $params = (new Model_Parameter())->getAsArray(
            ['name IN (?)' => ['contact.name', 'contact.www', 'contact.email']]
        );

        $mailer = new Zend_Mail();
        return [
            self::GLOBAL_PLACEHOLDER_FROM_NAME => $mailer->getDefaultFrom()['name'],
            self::GLOBAL_PLACEHOLDER_FROM_ADDRESS => $mailer->getDefaultFrom()['email'],
            self::GLOBAL_PLACEHOLDER_CONTACT_NAME => $params['contact.name'],
            self::GLOBAL_PLACEHOLDER_CONTACT_WWW => $params['contact.www'],
            self::GLOBAL_PLACEHOLDER_CONTACT_EMAIL => $params['contact.email'],
            self::GLOBAL_PLACEHOLDER_SEND_DATE => Zend_Date::now()->get(Zend_Date::DATE_MEDIUM),
        ];
    }
}
