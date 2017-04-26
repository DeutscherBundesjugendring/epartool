<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1133 extends AbstractMigration
{
    public function up()
    {
        $this->getAdapter()->beginTransaction();
        $this->migrateFollowUpSnippets();
        $this->getAdapter()->commitTransaction();
    }

    private function migrateFollowUpSnippets()
    {
        $this->migrate('fowups', 'fid', ['expl']);
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
