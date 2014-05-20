<?php

class Model_Mail_Placeholder extends Model_DbjrBase
{
    const GLOBAL_PLACEHOLDER_FROM_NAME = 'from_name';
    const GLOBAL_PLACEHOLDER_FROM_ADDRESS = 'from_address';
    const GLOBAL_PLACEHOLDER_CONTACT_NAME = 'contact_name';
    const GLOBAL_PLACEHOLDER_CONTACT_WWW = 'contact_www';
    const GLOBAL_PLACEHOLDER_CONTACT_EMAIL = 'contact_email';

    protected $_name = 'email_placeholder';
    protected $_primary = 'id';

    public function getByTemplateId($templateId)
    {
        $select = $this
            ->select(true)
            ->setIntegrityCheck(false)
            ->joinLeft(
                array('ethetp' => 'email_template_has_email_placeholder'),
                'ethetp.email_placeholder_id = ' . $this->_name . '.id'
            );
        if ($templateId) {
            $select
                ->where('ethetp.email_template_id=?', $templateId)
                ->orWhere($this->_name . '.is_global=?', true);
        } else {
            $select->where($this->_name . '.is_global=?', true);
        }

        return $this->fetchAll($select);
    }

    public function getGlobalValues()
    {
        return array(
            self::GLOBAL_PLACEHOLDER_FROM_NAME => 's',
            self::GLOBAL_PLACEHOLDER_FROM_ADDRESS => 's',
            self::GLOBAL_PLACEHOLDER_CONTACT_NAME => 's',
            self::GLOBAL_PLACEHOLDER_CONTACT_WWW => 's',
            self::GLOBAL_PLACEHOLDER_CONTACT_EMAIL => 's',
        );
    }
}
