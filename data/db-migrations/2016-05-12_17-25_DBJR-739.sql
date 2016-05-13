INSERT INTO user_info (
    SELECT (SELECT '' AS user_info_uid), u.uid, vtr.kid, (SELECT '' AS cmnt), u.source, u.src_misc,
    u.group_size, u.name_group, u.name_pers, u.age_group, u.regio_pax, u.cnslt_results,
    (SELECT NOW() AS date_added), (SELECT '' AS cmnt_ext), (SELECT NOW() AS time_user_confirmed), NULL, u.name,
    u.newsl_subscr
    FROM vt_rights AS vtr
    INNER JOIN users AS u ON (vtr.uid = u.uid)
    LEFT JOIN user_info AS ui ON (vtr.kid = ui.kid AND u.uid = ui.uid)
    WHERE ui.user_info_id IS NULL
);
