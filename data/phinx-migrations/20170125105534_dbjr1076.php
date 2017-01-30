<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1076 extends AbstractMigration
{
    public function up()
    {
        $this->getAdapter()->beginTransaction();
        $this->migrateArticles();
        $this->migrateConsultations();
        $this->migrateContributions();
        $this->migrateDiscussions();
        $this->migrateEmailComponents();
        $this->migrateEmailTemplates();
        $this->migrateFollowUpFiles();
        $this->migrateFooters();
        $this->migrateHelpTexts();
        $this->migrateQuestions();
        $this->getAdapter()->commitTransaction();
    }

    private function migrateArticles()
    {
        $this->migrate('articles', 'art_id', ['artcl', 'sidebar']);
    }

    private function migrateConsultations()
    {
        $this->migrate('cnslt', 'kid', [
            'vot_expl',
            'titl',
            'titl_short',
            'titl_sub',
            'expl_short',
            'follow_up_explanation',
            'contribution_confirmation_info',
            'license_agreement',
        ]);
    }

    private function migrateEmailComponents()
    {
        $this->migrate('email_component', 'id', ['body_html', 'body_text', 'description']);
    }

    private function migrateEmailTemplates()
    {
        $this->migrate('email_template', 'id', ['body_html', 'body_text']);
    }

    private function migrateFooters()
    {
        $this->migrate('footer', 'id', ['text']);
    }

    private function migrateFollowUpFiles()
    {
        $this->migrate('fowup_fls', 'ffid', ['ref_view']);
    }

    private function migrateHelpTexts()
    {
        $this->migrate('help_text', 'id', ['body']);
    }

    private function migrateContributions()
    {
        $this->migrate('inpt', 'tid', ['thes', 'expl']);
    }

    private function migrateDiscussions()
    {
        $this->migrate('input_discussion', 'id', ['body']);
    }

    private function migrateQuestions()
    {
        $this->migrate('quests', 'qi', ['q_xpl']);
    }

    /**
     * @param string $table
     * @param string $idColumn
     * @param array $columns
     */
    private function migrate($table, $idColumn, array $columns)
    {
        $adapter = $this->getAdapter();
        $escapedColumns = [$adapter->quoteColumnName($idColumn)];
        foreach ($columns as $column) {
            $escapedColumns[] = $adapter->quoteColumnName($column);
        }
        $entities = $this->fetchAll("SELECT " . implode(', ', $escapedColumns) . " FROM "
            . $adapter->quoteTableName($table));
        foreach ($entities as $entity) {
            $this->execute("UPDATE " . $adapter->quoteTableName($table) . " SET "
                . implode(', ', $this->createUpdateColumnsExpressions($entity, $idColumn, $columns))
                . " WHERE " . $adapter->quoteColumnName($idColumn) . "=".$entity[$idColumn]);
        }
    }

    /**
     * @param array $entity
     * @param string $idColumn
     * @param array $columns
     * @return array
     */
    private function createUpdateColumnsExpressions($entity, $idColumn, array $columns)
    {
        $connection = $this->getAdapter()->getConnection();
        $expressions = [];

        foreach ($entity as $column => $data) {
            if ($column === $idColumn || !in_array((string) $column, $columns)) {
                continue;
            }
            $expressions[] = sprintf(
                "%s=%s",
                $this->getAdapter()->quoteColumnName($column),
                $connection->quote($this->decode($data))
            );
        }

        return $expressions;
    }

    /**
     * @param $string
     * @return string
     */
    private function decode($string)
    {
        return stripslashes(html_entity_decode($string));
    }
}
