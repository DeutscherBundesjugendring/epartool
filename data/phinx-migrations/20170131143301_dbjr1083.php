<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1083 extends AbstractMigration
{
    const RESTORE_DEFAULT_NULLABLE_COMMENTS = [
        'articles' => [
            'artcl' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Article itself',
                'type' => 'text',
            ],
            'desc' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Readable descr for admin',
                'type' => 'varchar(255)',
            ],
            'proj' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'which project',
                'type' => 'varchar(255)',
            ],
            'ref_nm' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Article reference name',
                'type' => 'varchar(191)',
            ],
            'sidebar' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Content for sidebar',
                'type' => 'text',
            ],
        ],
        'articles_refnm' => [
            'desc' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'readable description',
                'type' => 'varchar(255)',
            ],
            'lng' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'language code',
                'type' => 'char(2)',
            ],
            'ref_nm' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'article reference name',
                'type' => 'varchar(191)',
            ],
        ],
        'cnslt' => [
            'contribution_confirmation_info' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'expl_short' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'follow_up_explanation' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'img_expl' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'explanatory text for title graphics',
                'type' => 'varchar(255)',
            ],
            'img_file' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'license_agreement' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'ln' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Language',
                'type' => 'char(2)',
            ],
            'phase_followup' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'phase_info' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'phase_input' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'phase_support' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'phase_voting' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'proj' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'gehört zu SD oder zur eigst Jugpol',
                'type' => 'varchar(255)',
            ],
            'state_field_label' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'titl' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Title of consultation',
                'type' => 'varchar(255)',
            ],
            'titl_short' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Shortened title (for slider, mails etc.)',
                'type' => 'varchar(255)',
            ],
            'titl_sub' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'subtitle (optional)',
                'type' => 'varchar(255)',
            ],
            'vot_expl' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'info text for voting start',
                'type' => 'text',
            ],
        ],
        'dirs' => [
            'dir_name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
        ],
        'email' => [
            'body_html' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'body_text' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'project_code' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'char(2)',
            ],
            'sent_by_user' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'subject' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
        ],
        'email_attachment' => [
            'filepath' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
        ],
        'email_component' => [
            'body_html' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'body_text' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'description' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'project_code' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'char(2)',
            ],
        ],
        'email_placeholder' => [
            'description' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'email_recipient' => [
            'email' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'name' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'email_template' => [
            'body_html' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'body_text' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'project_code' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'char(2)',
            ],
            'subject' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
        ],
        'email_template_type' => [
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'footer' => [
            'proj' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'char(2)',
            ],
            'text' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
        ],
        'fowups' => [
            'embed' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'embedding for multimedia',
                'type' => 'varchar(600)',
            ],
            'expl' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Erläuterung',
                'type' => 'text',
            ],
        ],
        'fowups_supports' => [
            'tmphash' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'char(32)',
            ],
        ],
        'fowup_fls' => [
            'gfx_who' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Graphic of who',
                'type' => 'varchar(255)',
            ],
            'ref_doc' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'reference to downloadable document',
                'type' => 'varchar(255)',
            ],
            'ref_view' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'introduction to viewable version of document',
                'type' => 'varchar(2000)',
            ],
            'titl' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Title of follow-up document',
                'type' => 'varchar(300)',
            ],
            'who' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Who gave the follow-up',
                'type' => 'varchar(255)',
            ],
        ],
        'help_text' => [
            'body' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'module' => [
                'nullable' => false,
                'default' => 'default',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'name' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'project_code' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'char(2)',
            ],
        ],
        'help_text_module' => [
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'inpt' => [
            'confirmation_key' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'expl' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Longer explanation',
                'type' => 'varchar(2000)',
            ],
            'notiz' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Notes for internal use',
                'type' => 'varchar(300)',
            ],
            'tg_nrs' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Nummern der Keywords (100-999), max 14 Tags',
                'type' => 'varchar(255)',
            ],
            'thes' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'User reply',
                'type' => 'varchar(330)',
            ],
            'video_id' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'video_service' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'input_discussion' => [
            'body' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'video_id' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'video_service' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'language' => [
            'code' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'license' => [
            'alt' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'description' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'icon' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'link' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'locale' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'text' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
            'title' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
        ],
        'notification_parameter' => [
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'value' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
        ],
        'notification_type' => [
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'parameter' => [
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'proj' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'char(2)',
            ],
            'value' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
        ],
        'proj' => [
            'color_accent_1' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'color_accent_2' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'color_primary' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'favicon' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'locale' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'logo' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'proj' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'char(2)',
            ],
            'titl_short' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Short title/name for project',
                'type' => 'varchar(255)',
            ],
            'vot_q' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Question used for voting',
                'type' => 'varchar(200)',
            ],
        ],
        'quests' => [
            'ln' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Language',
                'type' => 'char(2)',
            ],
            'nr' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Number shown in ordered list',
                'type' => 'varchar(4)',
            ],
            'q' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'The question itself',
                'type' => 'varchar(300)',
            ],
            'q_xpl' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Explanation for question',
                'type' => 'text',
            ],
            'vot_q' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Introducing voting question',
                'type' => 'varchar(220)',
            ],
        ],
        'tgs' => [
            'tg_de' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'German translation of tag',
                'type' => 'varchar(191)',
            ],
        ],
        'theme' => [
            'color_accent_1' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'color_accent_2' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'color_primary' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'urlkey_action' => [
            'handler_class' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'urlkey' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'urlkey_action_parameter' => [
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
            'value' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'text',
            ],
        ],
        'users' => [
            'cmnt' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Internal comments for admins',
                'type' => 'varchar(400)',
            ],
            'email' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Mail Address',
                'type' => 'varchar(191)',
            ],
            'name' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'name_group' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Name of group',
                'type' => 'varchar(255)',
            ],
            'name_pers' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Name of contact person',
                'type' => 'varchar(255)',
            ],
            'nick' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'password' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'regio_pax' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'source' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Dialogue, Group, Misc, Position paper',
                'type' => 'set(\'d\',\'g\',\'p\',\'m\')',
            ],
            'src_misc' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Explanation of misc source',
                'type' => 'varchar(300)',
            ],
        ],
        'user_info' => [
            'cmnt' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Internal comments for admins',
                'type' => 'varchar(400)',
            ],
            'cmnt_ext' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(600)',
            ],
            'confirmation_key' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'name' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(255)',
            ],
            'name_group' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Name of group',
                'type' => 'varchar(255)',
            ],
            'name_pers' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Name of contact person',
                'type' => 'varchar(255)',
            ],
            'regio_pax' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Bundesländer',
                'type' => 'varchar(255)',
            ],
            'source' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'Dialogue, Group, Misc, Position paper',
                'type' => 'set(\'d\',\'g\',\'p\',\'m\')',
            ],
            'src_misc' => [
                'nullable' => true,
                'default' => '',
                'comment' => 'explanation of misc source',
                'type' => 'varchar(300)',
            ],
        ],
        'video_service' => [
            'name' => [
                'nullable' => false,
                'default' => '',
                'comment' => '',
                'type' => 'varchar(191)',
            ],
        ],
        'vt_final' => [
            'id' => [
                'nullable' => false,
                'default' => '',
                'comment' => "md5 (tid\'.-.\'uid)",
                'type' => 'varchar(191)',
            ],
        ],
        'vt_grps' => [
            'sub_uid' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'md5 of mail.kid',
                'type' => 'char(32)',
            ],
            'sub_user' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'email address',
                'type' => 'varchar(255)',
            ],
            'vt_inp_list' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'list of votable tids',
                'type' => 'text',
            ],
            'vt_rel_qid' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'list of rel QIDs',
                'type' => 'text',
            ],
            'vt_tg_list' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'list of all (still) available tags for this user',
                'type' => 'text',
            ],
        ],
        'vt_indiv' => [
            'confirmation_hash' => [
                'nullable' => true,
                'default' => '',
                'comment' => '',
                'type' => 'char(32)',
            ],
            'sub_uid' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'individual subuser',
                'type' => 'char(32)',
            ],
        ],
        'vt_rights' => [
            'vt_code' => [
                'nullable' => false,
                'default' => '',
                'comment' => 'Voting access code for this group',
                'type' => 'char(8)',
            ],
        ],
        'vt_settings' => [
            'btn_important_label' => [
                'nullable' => false,
                'default' => 'Ist mir besonders wichtig',
                'comment' => 'Label for the super preference button',
                'type' => 'varchar(255)',
            ],
            'btn_labels' => [
                'nullable' => false,
                'default' => 'Stimme nicht zu,Nicht wichtig,Wichtig,Sehr wichtig,Super wichtig',
                'comment' => 'labels of voting buttons, comma-separated',
                'type' => 'varchar(255)',
            ],
        ]
    ];
    const NEW_COLUMNS_DEFAULT_VALUES = [
        'articles' => [
            'is_showed' => 1,
        ],
        'cnslt' => [
            'is_followup_phase_showed' => 0,
            'is_input_phase_showed' => 1,
            'is_public' => 0,
            'is_support_phase_showed' => 0,
            'is_voting_result_phase_showed' => 0,
            'is_voting_phase_showed' => 1,
            'is_vt_anonymized' => 0,
            'is_vt_finalized' => 1,
        ],
        'fowup_fls' => ['is_only_month_year_showed' => 0],
        'inpt' => [
            'is_confirmed' => null,
            'is_confirmed_by_user' => null,
            'is_votable' => null,
        ],
        'users' => [
            'is_confirmed' => null,
            'is_subscribed_newsletter' => 0,
            'is_receiving_consultation_results' => 1,
        ],
        'user_info' => ['is_receiving_consultation_results' => 1],
        'vt_final' => ['is_followups' => 0],
        'vt_grps' => ['is_member' => null],
        'vt_indiv' => ['is_pimp' => 0],
        'vt_settings' => ['is_btn_important' => 0],
    ];

    public function up()
    {
        $adapter = $this->getAdapter();

        foreach (self::RESTORE_DEFAULT_NULLABLE_COMMENTS as $table => $columns) {
            foreach ($columns as $column => $params) {
                $default = "";
                if (strpos($params['type'], 'text') === FALSE) {
                    $default = "DEFAULT '" . $params['default'] . "' ";
                }

                $this->execute(
                    "ALTER TABLE " . $adapter->quoteTableName($table) . " CHANGE " . $adapter->quoteColumnName($column)
                    . " " . $adapter->quoteColumnName($column) . " " . $params['type'] . " "
                    . ($params['nullable'] ? "" : "NOT ") . "NULL " . $default . "COMMENT '" . $params['comment'] . "'"
                );
            }
        }

        foreach (self::NEW_COLUMNS_DEFAULT_VALUES as $table => $columns) {
            foreach ($columns as $column => $default) {
                $this->execute(
                    "ALTER TABLE " . $adapter->quoteTableName($table) . " ALTER " . $adapter->quoteColumnName($column)
                    . " SET DEFAULT " . ($default === null ? 'NULL' : (is_int($default) ? $default : "'".$default."'"))
                );
            }
        }
    }
}
