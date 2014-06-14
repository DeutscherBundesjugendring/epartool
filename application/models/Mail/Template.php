<?php

class Model_Mail_Template extends Dbjr_Db_Table_Abstract
{
    const SYSTEM_TEMPLATE_FORGOTTEN_PASSWORD = 'forgotten_password';
    const SYSTEM_TEMPLATE_REGISTRATION = 'registration';
    const SYSTEM_TEMPLATE_INPUT_CONFIRMATION = 'input_confirmation';
    const SYSTEM_TEMPLATE_INPUT_CONFIRMATION_NEW_USER = 'input_confirmation_new_user';
    const SYSTEM_TEMPLATE_VOTING_CONFIRMATION_SINGLE = 'voting_confirmation_single';
    const SYSTEM_TEMPLATE_VOTING_CONFIRMATION_GROUP = 'voting_confirmation_group';
    const SYSTEM_TEMPLATE_VOTING_INVITATION_SINGLE = 'voting_invitation_single';
    const SYSTEM_TEMPLATE_VOTING_INVITATION_GROUP = 'voting_invitation_group';

    protected $_name = 'email_template';
    protected $_dependentTables = array('Model_Mail_Template_Type');
    protected $_primary = 'id';
    protected $_referenceMap = array(
        'Type' => array(
            'columns'           => 'type_id',
            'refTableClass'     => 'Model_Mail_Template_Type',
            'refColumns'        => 'id'
        ),
    );

    /**
     * Adds new template to db
     * @param  array   $data The data to be inserted
     * @return integer       Primary key of the inserted entry
     */
    public function insert(array $data)
    {
        $db = $this->getAdapter();

        $db->beginTransaction();
        try {
            $typeModel = new Model_Mail_Template_Type();
            $systemType = $typeModel->fetchRow(
                $typeModel->select()->where('name=?', Model_Mail_Template_Type::TEMPLATE_TYPE_CUSTOM)
            );

            $row = $this->createRow($data);
            $row->project_code = $this->_projectCode;
            $row->type_id = $systemType['id'];

            $templateId = parent::insert($row->toArray());
            $db->commit();

            return $templateId;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * Updates existing template
     * @param  array                $data  Column-value pairs.
     * @param  array|string         $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @throws Dbjr_Mail_Exception         Thrown if editing template from another project or changing name in system template
     * @return int                         The number of rows updated.
     */
    public function update(array $data, $where)
    {
        if ($data['project_code'] !== $this->_projectCode) {
            throw new Dbjr_Mail_Exception('Can not update template belonging to another project.');
        } elseif (array_key_exists('name', $data)
            && $template->findModel_Mail_Template_Type()->current()->name === Model_Mail_Template_Type::TEMPLATE_TYPE_SYSTEM
        ) {
            throw new Dbjr_Mail_Exception('Can not update name of system tamplate.');
        }

        return parent::update($data, $where);
    }

    /**
     * Deletes template from database
     * @param  array|string         $where SQL WHERE clause(s).
     * @return integer                     The number of rows deleted
     * @throws Dbjr_Mail_Exception         Thrown if deleting template from another project or a system template
     */
    public function delete($where)
    {
        $db = $this->getAdapter();

        $db->beginTransaction();
        try {
            $select = $this->select();
            if (is_array($where)) {
                foreach ($where as $key => $val) {
                    $select->where($key, $val);
                }
            } else {
                $select->where($where);
            }
            $template = $this->fetchRow($select);

            if (!isset($template)) {
                throw new Dbjr_Mail_Exception('Can not delete template belonging to another project.');
            } elseif ($template->findModel_Mail_Template_Type()->current()->name === Model_Mail_Template_Type::TEMPLATE_TYPE_SYSTEM) {
                throw new Dbjr_Mail_Exception('Can not delete system template.');
            }
            $rowsDeleted = parent::delete($where);
            $db->commit();

            return $rowsDeleted;
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

    /**
     * Returns mail templates by type
     * @param  string               $typeName    The name of the template type
     * @return Zend_Db_Table_Rowset              The system templates matching the criteria
     */
    public function getAllByType($typeName)
    {
        $templateTypeModel = new Model_Mail_Template_Type();
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->join(
                $templateTypeModel->getName(),
                $this->getName() . '.type_id = ' . $templateTypeModel->getName() . '.id',
                array()
            )
            ->where($templateTypeModel->getName() . '.name=?', $typeName);

        return $this->fetchAll($select);
    }
}
