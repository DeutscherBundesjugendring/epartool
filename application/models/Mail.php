<?php

class Model_Mail extends Model_DbjrBase
{
    protected $_name = 'email';
    protected $_primary = 'id';
    protected $_dependentTables = array('Model_Mail_Recipient');
    protected $_referenceMap = array(
        'Recipient' => array(
            'columns'           => 'id',
            'refTableClass'     => 'Model_Mail_Recipient',
            'refColumns'        => 'email_id'
        ),
    );

    /**
     * Inserts the mail in db along with its recipients
     * @param  array    $data The array holding the data to be inserted
     * @return integer        The id of the mail that got inserted
     */
    public function insert($data)
    {
        $db = $this->getAdapter();
        $db->beginTransaction();
        try {
            $to = $data['to'];
            unset($data['to']);
            $cc = $data['cc'];
            unset($data['cc']);
            $bcc = $data['bcc'];
            unset($data['bcc']);

            $mailId = parent::insert($data);

            $recipientModel = new Model_Mail_Recipient();
            foreach ($to as $name => $email) {
                $recipientModel->insert(
                    array(
                        'email_id' => $mailId,
                        'type' => Model_Mail_Recipient::TYPE_TO,
                        'name' => !is_int($name) ? $name : null,
                        'email' => $email,
                    )
                );
            }
            foreach ($cc as $name => $email) {
                $recipientModel->insert(
                    array(
                        'email_id' => $mailId,
                        'type' => Model_Mail_Recipient::TYPE_CC,
                        'name' => !is_int($name) ? $name : null,
                        'email' => $email,
                    )
                );
            }
            foreach ($bcc as $name => $email) {
                $recipientModel->insert(
                    array(
                        'email_id' => $mailId,
                        'type' => Model_Mail_Recipient::TYPE_BCC,
                        'name' => !is_int($name) ? $name : null,
                        'email' => $email,
                    )
                );
            }
            $db->commit();

            return $mailId;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * Return the select object modified to only include templates from the current project
     * @param  bool                 $withFromPart Whether or not to include the from part of the select based on the table
     * @return Zend_Db_Table_Select               The select object
     */
    public function select($withFromPart = Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART)
    {
        return parent::select($withFromPart)->where('project_code=?', $this->_projectCode);
    }
}
