<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1077 extends AbstractMigration
{
    const NULL_COLUMNS = [
        'articles' => ['hid'],
        'inpt' => ['block', 'user_conf', 'vot'],
        'user_info' => ['newsl_subscr'],
        'vt_grps' => ['member'],
        'users' => ['block'],
    ];

    const REVERSE_BOOL_VALUES = [
        'articles' => ['hid'],
        'inpt' => ['block'],
    ];

    const TABLES = [
        'articles' => ['hid' => 'is_showed'],
        'cnslt' => [
            'inp_show' => 'is_input_phase_showed',
            'spprt_show' => 'is_support_phase_showed',
            'vot_show' => 'is_voting_phase_showed',
            'vot_res_show' => 'is_voting_result_phase_showed',
            'follup_show' => 'is_followup_phase_showed',
            'public' => 'is_public',
            'vt_finalized' => 'is_vt_finalized',
            'vt_anonymized' => 'is_vt_anonymized',
        ],
        'fowup_fls' => ['show_no_day' => 'is_only_month_year_showed'],
        'inpt' => [
            'block' => 'is_confirmed',
            'user_conf' => 'is_confirmed_by_user',
            'vot' => 'is_votable',
        ],
        'user_info' => [
            'cnslt_results' => 'is_receiving_consultation_results',
            'newsl_subscr' => 'is_subscribed_newsletter',
        ],
        'users' => [
            'block' => 'is_confirmed',
            'newsl_subscr' => 'is_subscribed_newsletter',
            'cnslt_results' => 'is_receiving_consultation_results',
        ],
        'vt_final' => ['fowups' => 'is_followups'],
        'vt_grps' => ['member' => 'is_member'],
        'vt_indiv' => ['pimp' => 'is_pimp'],
        'vt_settings' => ['btn_important' => 'is_btn_important'],
    ];

    public function up()
    {
        $adapter = $this->getAdapter();
        foreach (self::TABLES as $table => $columns) {
            foreach ($columns as $column => $newName) {
                $this->execute(
                    "ALTER TABLE " . $adapter->quoteTableName($table) . " CHANGE " . $adapter->quoteColumnName($column)
                    . " " . $adapter->quoteColumnName($column) . " varchar(191) NULL;"
                );
                $this->transformValues(
                    $table,
                    $column,
                    (array_key_exists($table, self::REVERSE_BOOL_VALUES)
                        && in_array($column, self::REVERSE_BOOL_VALUES[$table]))
                );
                $this->execute(
                    "ALTER TABLE " . $adapter->quoteTableName($table) . " CHANGE " . $adapter->quoteColumnName($column)
                    . " " . $adapter->quoteColumnName($newName) . " tinyint(1) "
                    . ((array_key_exists($table, self::NULL_COLUMNS) && in_array($column, self::NULL_COLUMNS[$table]))
                        ? ""
                        : "NOT "
                    ) . "NULL;"
                );
            }
        }
    }

    /**
     * @param string $table
     * @param string $column
     * @param bool $reverse
     */
    private function transformValues($table, $column, $reverse)
    {
        $adapter = $this->getAdapter();
        $this->execute(
            "UPDATE " . $adapter->quoteTableName($table) . " SET " . $adapter->quoteColumnName($column)
            . " = " . ($reverse ? '0' : '1') . " WHERE " . $adapter->quoteColumnName($column) . " IN ('y', 'c')"
        );
        $this->execute(
            "UPDATE " . $adapter->quoteTableName($table) . " SET " . $adapter->quoteColumnName($column)
            . " = " . ($reverse ? '1' : '0') . " WHERE " . $adapter->quoteColumnName($column) . " IN ('n', 'r', 'b')"
        );
        $this->execute(
            "UPDATE " . $adapter->quoteTableName($table) . " SET " . $adapter->quoteColumnName($column)
            . " = NULL WHERE " . $adapter->quoteColumnName($column) . " = 'u'"
        );
    }
}
