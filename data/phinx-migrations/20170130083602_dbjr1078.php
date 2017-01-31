<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1078 extends AbstractMigration
{
    public function up()
    {
        $this->migrate('articles_refnm', 'type', 'article_type', 'name', 'varchar(191)', 'global', [
            'g' => 'global',
            'b' => 'consultation',
        ]);
        $this->migrate('articles_refnm', 'scope', 'article_scope', 'name', 'varchar(191)', 'none', [
            'none',
            'info',
            'voting',
            'followup',
            'static',
        ]);
        $this->migrate(
            'email_recipient',
            'type',
            'email_recipient_type',
            'name',
            'varchar(191)',
            null,
            ['to','cc','bcc']
        );
        $this->migrate('fowups', 'type', 'fowups_type', 'name', 'varchar(191)', 'general', [
            'g' => 'general',
            's' => 'supporting',
            'a' => 'action',
            'r' => 'rejected',
            'e' => 'end',
        ]);

        $this->execute(
            "ALTER TABLE `users` CHANGE `lvl` `role` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `cmnt`"
        );
        $this->migrate('users', 'role', 'users_role', 'name', 'varchar(191)', 'user', [
            'usr' => 'user',
            'adm' => 'admin',
            'edt' => 'editor',
        ]);
        $this->migrate('inpt', 'type', 'contribution_type', 'name', 'varchar(191)', null, [
            'p' => 'from_discussion',
        ]);

        $this->execute("ALTER TABLE `vt_indiv` ADD PRIMARY KEY (`uid`, `tid`, `sub_uid`), DROP INDEX `Stimmenzählung`");
        $this->migrate('vt_indiv', 'status', 'vt_indiv_status', 'name', 'varchar(191)', null, [
            'v' => 'voted',
            's' => 'skipped',
            'c' => 'confirmed',
        ]);

        $this->execute("ALTER TABLE `vt_settings`  CHANGE  `btn_numbers` `btn_numbers` int(11) NULL DEFAULT 3");
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $enumTable
     * @param string $enumTablePKColumn
     * @param string $PKColumnType
     * @param string $default
     * @param array $enumValues
     */
    private function migrate(
        $table,
        $column,
        $enumTable,
        $enumTablePKColumn,
        $PKColumnType,
        $default,
        array $enumValues
    ) {
        $adapter = $this->getAdapter();
        $qTable = $adapter->quoteTableName($table);
        $qColumn = $adapter->quoteColumnName($column);
        $qEnumTable = $adapter->quoteTableName($enumTable);
        $qEnumTablePKColumn = $adapter->quoteColumnName($enumTablePKColumn);

        $this->execute(
            "CREATE TABLE " . $qEnumTable . " (" . $qEnumTablePKColumn . " " . $PKColumnType .
            " NOT NULL) ENGINE='InnoDB'"
        );
        $this->execute(
            "ALTER TABLE " . $qEnumTable . " ADD PRIMARY KEY `pk_" . $enumTablePKColumn . "` (" . $qEnumTablePKColumn
            . ")"
        );
        $this->execute("INSERT INTO " . $qEnumTable . " VALUES " . $this->getValuesStatementPart($enumValues));

        $this->execute(
            "ALTER TABLE " . $qTable . " CHANGE " . $qColumn . " " . $qColumn . " " . $PKColumnType . " NULL"
            . ($default !== null ? (" DEFAULT " . $default) : "")
        );

        foreach ($enumValues as $key => $value) {
            if (!is_int($key)) {
                $this->execute(
                    "UPDATE " . $qTable . " SET " . $qColumn . " = '" . $value . "' WHERE " . $qColumn
                    . " = '" . $key . "'"
                );
            }
        }

        $this->execute("UPDATE " . $qTable . " SET " . $qColumn . " = NULL WHERE " . $qColumn . " = ''");
        $this->execute(
            "ALTER TABLE " . $qTable . " CHANGE " . $qColumn . " " . $qColumn . " " . $PKColumnType . " NULL"
            . ", ADD FOREIGN KEY (" . $qColumn . ") REFERENCES " . $qEnumTable . " (" . $qEnumTablePKColumn
            . ") ON DELETE RESTRICT"
        );
    }

    /**
     * @param array $enumValues
     * @return string
     */
    private function getValuesStatementPart(array $enumValues)
    {
        $values = [];
        foreach ($enumValues as $key => $value)
        {
            $values[] = "('" . $value . "')";
        }
        
        return implode(', ', $values);
    }
}
