<?php

use Phinx\Migration\AbstractMigration;

class Dbjr972 extends AbstractMigration
{
    const CHARSET = 'utf8mb4';
    const COLLATE = 'utf8mb4_unicode_ci';

    const TABLES = [
        'articles' => [
            'proj' => 'varchar(255)',
            'desc' => 'varchar(255)',
            'hid' => 'enum(\'y\',\'n\')',
            'ref_nm' => 'varchar(191)',
            'artcl' => 'text',
            'sidebar' => 'text',
        ],
        'articles_refnm' => [
            'ref_nm' => 'varchar(191)',
            'lng' => 'char(2)',
            'desc' => 'varchar(255)',
            'type' => 'enum(\'g\',\'n\')',
            'scope' => 'enum(\'none\',\'info\',\'voting\',\'followup\',\'static\')',
        ],
        'cnslt' => [
            'proj' => 'varchar(255)',
            'inp_show' => 'enum(\'y\',\'n\')',
            'spprt_show' => 'enum(\'y\',\'n\')',
            'vot_show' => 'enum(\'y\',\'n\')',
            'vot_expl' => 'text',
            'vot_res_show' => 'enum(\'y\',\'n\')',
            'follup_show' => 'enum(\'y\',\'n\')',
            'titl' => 'varchar(255)',
            'titl_short' => 'varchar(255)',
            'titl_sub' => 'varchar(255)',
            'img_file' => 'text',
            'img_expl' => 'varchar(255)',
            'expl_short' => 'text',
            'ln' => 'char(2)',
            'public' => 'enum(\'y\',\'n\')',
            'vt_finalized' => 'enum(\'y\',\'n\')',
            'vt_anonymized' => 'enum(\'y\',\'n\')',
            'phase_info' => 'varchar(255)',
            'phase_support' => 'varchar(255)',
            'phase_input' => 'varchar(255)',
            'phase_voting' => 'varchar(255)',
            'phase_followup' => 'varchar(255)',
            'follow_up_explanation' => 'text',
            'state_field_label' => 'varchar(255)',
            'contribution_confirmation_info' => 'text',
            'license_agreement' => 'text',
        ],
        'contributor_age' => [],
        'dirs' => [
            'dir_name' => 'varchar(255)',
        ],
        'email' => [
            'project_code' => 'char(2)',
            'sent_by_user' => 'varchar(255)',
            'subject' => 'varchar(255)',
            'body_html' => 'text',
            'body_text' => 'text',
        ],
        'email_attachment' => [
            'filepath' => 'varchar(255)',
        ],
        'email_component' => [
            'name' => 'varchar(191)',
            'project_code' => 'char(2)',
            'body_html' => 'text',
            'body_text' => 'text',
            'description' => 'text',
        ],
        'email_placeholder' => [
            'name' => 'varchar(191)',
            'description' => 'text',
        ],
        'email_recipient' => [
            'type' => 'enum(\'to\',\'cc\',\'bcc\')',
            'name' => 'varchar(255)',
            'email' => 'varchar(255)',
        ],
        'email_template' => [
            'name' => 'varchar(191)',
            'project_code' => 'char(2)',
            'subject' => 'varchar(255)',
            'body_html' => 'text',
            'body_text' => 'text',
        ],
        'email_template_has_email_placeholder' => [],
        'email_template_type' => [
            'name' => 'varchar(191)',
        ],
        'footer' => [
            'proj' => 'char(2)',
            'text' => 'text',
        ],
        'fowup_fls' => [
            'titl' => 'varchar(255)',
            'who' => 'varchar(255)',
            'show_no_day' => 'enum(\'n\',\'y\')',
            'ref_doc' => 'varchar(255)',
            'ref_view' => 'varchar(255)',
            'gfx_who' => 'varchar(255)',
        ],
        'fowups' => [
            'embed' => 'varchar(255)',
            'expl' => 'text',
            'typ' => 'enum(\'g\',\'s\',\'a\',\'r\',\'e\')',
        ],
        'fowups_rid' => [],
        'fowups_supports' => [
            'tmphash' => 'char(32)',
        ],
        'group_size' => [],
        'help_text' => [
            'name' => 'varchar(191)',
            'body' => 'text',
            'project_code' => 'char(2)',
            'module' => 'varchar(191)',
        ],
        'help_text_module' => [
            'name' => 'varchar(191)',
        ],
        'inpt' => [
            'thes' => 'varchar(255)',
            'expl' => 'varchar(255)',
            'block' => 'enum(\'y\',\'n\',\'u\')',
            'user_conf' => 'enum(\'u\',\'c\',\'r\')',
            'vot' => 'enum(\'y\',\'n\',\'u\')',
            'typ' => 'enum(\'p\',\'f\',\'l\',\'bp\')',
            'tg_nrs' => 'varchar(255)',
            'notiz' => 'varchar(255)',
            'confirmation_key' => 'varchar(255)',
            'video_service' => 'varchar(191)',
            'video_id' => 'varchar(255)',
        ],
        'inpt_tgs' => [],
        'input_discussion' => [
            'body' => 'text',
            'video_service' => 'varchar(191)',
            'video_id' => 'varchar(255)',
        ],
        'input_relations' => [],
        'language' => [
            'code' => 'varchar(191)',
        ],
        'license' => [
            'title' => 'varchar(255)',
            'description' => 'varchar(255)',
            'text' => 'text',
            'link' => 'varchar(255)',
            'icon' => 'varchar(255)',
            'alt' => 'varchar(255)',
            'locale' => 'varchar(191)',
        ],
        'notification' => [],
        'notification_parameter' => [
            'name' => 'varchar(191)',
            'value' => 'text',
        ],
        'notification_type' => [
            'name' => 'varchar(191)',
        ],
        'parameter' => [
            'name' => 'varchar(191)',
            'proj' => 'char(2)',
            'value' => 'text',
        ],
        'proj' => [
            'proj' => 'char(2)',
            'titl_short' => 'varchar(255)',
            'vot_q' => 'varchar(255)',
            'color_accent_1' => 'varchar(255)',
            'color_primary' => 'varchar(255)',
            'color_accent_2' => 'varchar(255)',
            'logo' => 'varchar(255)',
            'favicon' => 'varchar(255)',
            'locale' => 'varchar(191)',
        ],
        'quests' => [
            'nr' => 'varchar(255)',
            'q' => 'varchar(255)',
            'q_xpl' => 'text',
            'ln' => 'char(2)',
            'vot_q' => 'varchar(255)',
        ],
        'tgs' => [
            'tg_de' => 'varchar(191)',
        ],
        'theme' => [
            'name' => 'varchar(191)',
            'color_accent_1' => 'varchar(255)',
            'color_primary' => 'varchar(255)',
            'color_accent_2' => 'varchar(255)',
        ],
        'urlkey_action' => [
            'urlkey' => 'varchar(191)',
            'handler_class' => 'varchar(255)',
        ],
        'urlkey_action_parameter' => [
            'name' => 'varchar(191)',
            'value' => 'text',
        ],
        'user_info' => [
            'cmnt' => 'varchar(255)',
            'source' => 'set(\'d\',\'g\',\'p\',\'m\')',
            'src_misc' => 'varchar(255)',
            'name_group' => 'varchar(255)',
            'name_pers' => 'varchar(255)',
            'regio_pax' => 'varchar(255)',
            'cnslt_results' => 'enum(\'y\',\'n\')',
            'cmnt_ext' => 'varchar(255)',
            'confirmation_key' => 'varchar(255)',
            'name' => 'varchar(255)',
            'newsl_subscr' => 'enum(\'y\',\'n\')',
        ],
        'users' => [
            'block' => 'enum(\'b\',\'u\',\'c\')',
            'name' => 'varchar(255)',
            'email' => 'varchar(191)',
            'password' => 'varchar(255)',
            'newsl_subscr' => 'enum(\'y\',\'n\')',
            'cmnt' => 'text',
            'lvl' => 'enum(\'usr\',\'adm\',\'edt\')',
            'source' => 'set(\'d\',\'g\',\'p\',\'m\')',
            'src_misc' => 'varchar(255)',
            'name_group' => 'varchar(255)',
            'name_pers' => 'varchar(255)',
            'regio_pax' => 'varchar(255)',
            'cnslt_results' => 'enum(\'y\',\'n\')',
            'nick' => 'varchar(255)',
        ],
        'vt_final' => [
            'fowups' => 'enum(\'y\',\'n\')',
            'id' => 'varchar(191)',
        ],
        'vt_grps' => [
            'sub_user' => 'varchar(255)',
            'sub_uid' => 'char(32)',
            'member' => 'enum(\'y\',\'n\',\'u\')',
            'vt_inp_list' => 'text',
            'vt_rel_qid' => 'text',
            'vt_tg_list' => 'text',
        ],
        'vt_indiv' => [
            'sub_uid' => 'char(32)',
            'pimp' => 'enum(\'y\',\'n\')',
            'status' => 'enum(\'v\',\'s\',\'c\')',
            'confirmation_hash' => 'char(32)',
        ],
        'vt_rights' => [
            'vt_code' => 'char(8)',
        ],
        'vt_settings' => [
            'btn_important' => 'enum(\'y\',\'n\')',
            'btn_important_label' => 'varchar(255)',
            'btn_numbers' => 'enum(\'0\',\'1\',\'2\',\'3\',\'4\')',
            'btn_labels' => 'varchar(255)',
        ],
    ];

    public function up()
    {
        $this->execute(
            "ALTER DATABASE `" . $this->getDatabaseName() . "` CHARACTER SET = " . self::CHARSET . " COLLATE = "
            . self::COLLATE . ";"
        );

        foreach (self::TABLES as $table => $columns) {
            $this->execute("SET foreign_key_checks = 0;");
            $quotedTable = $this->getAdapter()->quoteTableName($table);
            foreach ($columns as $column => $type) {
                $quotedColumn = $this->getAdapter()->quoteColumnName($column);
                $this->execute(
                    "ALTER TABLE " . $quotedTable . " CHANGE " . $quotedColumn . " " . $quotedColumn
                    . " " . $type . " CHARACTER SET " . self::CHARSET . " COLLATE " . self::COLLATE . ";"
                );
            }
            $this->execute(
                "ALTER TABLE " . $quotedTable . " CONVERT TO CHARACTER SET " . self::CHARSET . " COLLATE "
                . self::COLLATE . ";"
            );
            $this->execute("SET foreign_key_checks = 1;");
        }
    }

    public function execute($sql)
    {
        echo $sql . PHP_EOL;
        return parent::execute($sql);
    }
    /**
     * @return string
     * @throws Exception
     */
    private function getDatabaseName()
    {
        $configuration = $this->getInput()->getOption('configuration');
        if (!file_exists($configuration)) {
            throw new \Exception(
                sprintf('This migration needs to read database name from phinx config file and %s is not a valid phinx config file', $configuration)
            );
        }

        $config = Phinx\Config\Config::fromYaml($configuration);
        $environment = $this->getInput()->getOption('environment');
        $env = $config->getEnvironment($environment);
        if (!array_key_exists('name', $env)) {
            throw new \Exception(
                sprintf('DB name is not defined in phinx config file %s for environment %s', $configuration, $environment)
            );
        }

        return $env['name'];
    }
}
