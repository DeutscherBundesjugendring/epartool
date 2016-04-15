-- Drop unused tables
DROP TABLE quests_choic;
DROP TABLE edt_cnslt;
DROP TABLE sessns;
DROP TABLE supports;

-- Drop unused columns
ALTER TABLE users DROP COLUMN is_contrib_under_cc;
ALTER TABLE user_info DROP COLUMN is_contrib_under_cc;

-- Make all tables use InnoDb Engine
ALTER TABLE articles_refnm ENGINE=INNODB;
ALTER TABLE cnslt ENGINE=INNODB;
ALTER TABLE dirs ENGINE=INNODB;
ALTER TABLE fowups ENGINE=INNODB;
ALTER TABLE fowups_rid ENGINE=INNODB;
ALTER TABLE fowups_supports ENGINE=INNODB;
ALTER TABLE fowup_fls ENGINE=INNODB;
ALTER TABLE quests ENGINE=INNODB;
ALTER TABLE tgs ENGINE=INNODB;
ALTER TABLE vt_final ENGINE=INNODB;
ALTER TABLE vt_grps ENGINE=INNODB;
ALTER TABLE vt_indiv ENGINE=INNODB;
ALTER TABLE vt_rights ENGINE=INNODB;

-- Set correct data types on id columns
ALTER TABLE articles MODIFY COLUMN art_id int UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE articles DROP FOREIGN KEY articles_kid_fkey;
ALTER TABLE articles MODIFY COLUMN kid int UNSIGNED NULL DEFAULT NULL;
ALTER TABLE cnslt MODIFY COLUMN kid int UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE articles ADD CONSTRAINT articles_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);
ALTER TABLE dirs MODIFY COLUMN id int UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE footer MODIFY COLUMN id int UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE fowup_fls MODIFY COLUMN ffid int UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE inpt_tgs MODIFY COLUMN tg_nr int UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE quests MODIFY COLUMN qi int UNSIGNED NOT NULL;
ALTER TABLE user_info MODIFY COLUMN user_info_id int UNSIGNED AUTO_INCREMENT NOT NULL;

-- Add foreign keys
ALTER TABLE articles MODIFY COLUMN ref_nm varchar(30) NULL DEFAULT NULL COMMENT 'Article reference name';
UPDATE articles SET ref_nm = NULL WHERE ref_nm = '0';
ALTER TABLE articles ADD CONSTRAINT articles_ref_nm_fkey FOREIGN KEY (ref_nm) REFERENCES articles_refnm(ref_nm);
ALTER TABLE articles MODIFY COLUMN parent_id int UNSIGNED NULL DEFAULT NULL COMMENT 'Parent article';
UPDATE articles SET parent_id = NULL WHERE parent_id = 0;
ALTER TABLE articles ADD CONSTRAINT articles_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES articles(art_id);

ALTER TABLE dirs MODIFY COLUMN kid int UNSIGNED NOT NULL;
ALTER TABLE dirs ADD CONSTRAINT cnslt_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);

ALTER TABLE fowups MODIFY COLUMN ffid int UNSIGNED NOT NULL;
ALTER TABLE fowups ADD CONSTRAINT fowups_ffid_fkey FOREIGN KEY (ffid) REFERENCES fowup_fls(ffid);

ALTER TABLE fowups_rid DROP PRIMARY KEY;
ALTER TABLE fowups_rid ADD COLUMN id int AUTO_INCREMENT PRIMARY KEY NOT NULL FIRST;
ALTER TABLE fowups_rid MODIFY COLUMN fid int UNSIGNED NULL DEFAULT NULL;
UPDATE fowups_rid SET fid = NULL WHERE fid = 0;
DELETE FROM fowups_rid WHERE fid NOT IN (SELECT fid FROM fowups);
ALTER TABLE fowups_rid ADD CONSTRAINT fowups_rid_fid_fkey FOREIGN KEY (fid) REFERENCES fowups(fid);
ALTER TABLE fowups_rid MODIFY COLUMN tid int UNSIGNED NULL DEFAULT NULL;
UPDATE fowups_rid SET tid = NULL WHERE tid = 0;
ALTER TABLE fowups_rid ADD CONSTRAINT fowups_rid_tid_fkey FOREIGN KEY (tid) REFERENCES inpt(tid);
ALTER TABLE fowups_rid MODIFY COLUMN ffid int UNSIGNED NULL DEFAULT NULL;
UPDATE fowups_rid SET ffid = NULL WHERE ffid = 0;
ALTER TABLE fowups_rid ADD CONSTRAINT fowups_rid_ffid_fkey FOREIGN KEY (ffid) REFERENCES fowup_fls(ffid);
ALTER TABLE fowups_rid ADD CONSTRAINT fowups_rid_fid_ref_fid_tid_ffid_key UNIQUE (fid_ref, fid, tid, ffid);

ALTER TABLE fowups_supports MODIFY COLUMN fid int UNSIGNED NOT NULL;
DELETE FROM fowups_supports WHERE fid NOT IN (SELECT fid from fowups);
ALTER TABLE fowups_supports ADD CONSTRAINT fowups_supports_fid_fkey FOREIGN KEY (fid) REFERENCES fowups(fid);

ALTER TABLE fowup_fls MODIFY COLUMN kid int UNSIGNED NOT NULL;
ALTER TABLE fowup_fls ADD CONSTRAINT fowup_fls_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);

DELETE FROM inpt WHERE qi NOT IN (SELECT qi from quests);
ALTER TABLE inpt ADD CONSTRAINT inpt_qi_fkey FOREIGN KEY (qi) REFERENCES quests(qi);
ALTER TABLE inpt MODIFY COLUMN dir int UNSIGNED NULL DEFAULT NULL;
UPDATE inpt SET dir = NULL WHERE dir = 0;
ALTER TABLE inpt ADD CONSTRAINT inpt_dirs_fkey FOREIGN KEY (dir) REFERENCES dirs(id);
ALTER TABLE inpt ADD CONSTRAINT inpt_uid_fkey FOREIGN KEY (uid) REFERENCES users(uid);

ALTER TABLE quests MODIFY COLUMN qi int UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE quests MODIFY COLUMN kid int UNSIGNED NOT NULL;
ALTER TABLE quests ADD CONSTRAINT quests_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);

ALTER TABLE user_info MODIFY COLUMN kid int UNSIGNED NOT NULL;
ALTER TABLE user_info ADD CONSTRAINT user_info_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);

ALTER TABLE vt_final MODIFY COLUMN uid int UNSIGNED NOT NULL;
ALTER TABLE vt_final ADD CONSTRAINT vt_final_uid_fkey FOREIGN KEY (uid) REFERENCES users(uid);
ALTER TABLE vt_final ADD CONSTRAINT vt_final_tid_fkey FOREIGN KEY (tid) REFERENCES inpt(tid);
ALTER TABLE vt_final MODIFY COLUMN kid int UNSIGNED NOT NULL;
ALTER TABLE vt_final ADD CONSTRAINT vt_final_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);

REPLACE INTO users (uid, name, email, password, group_size)
(
    SELECT
        uid,
        'DBJR Projektbüro IchmachePolitik',
        'alteuser@ichmache-politik.de',
         '$2y$10$620ec02b306d522223c4auI32wkWGLFK9zboVrTJVh8s5ZjHMWS0i',
         200
    FROM vt_grps
    WHERE uid NOT IN (SELECT uid FROM users) AND uid = 15934
    GROUP BY uid
    LIMIT 1
);
ALTER TABLE vt_grps MODIFY COLUMN kid int UNSIGNED NOT NULL;
ALTER TABLE vt_grps ADD CONSTRAINT vt_grps_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);
ALTER TABLE vt_grps MODIFY COLUMN uid int UNSIGNED NOT NULL;
ALTER TABLE vt_grps ADD CONSTRAINT vt_grps_uid_fkey FOREIGN KEY (uid) REFERENCES users(uid);

ALTER TABLE vt_indiv MODIFY COLUMN uid int UNSIGNED NOT NULL;
ALTER TABLE vt_indiv ADD CONSTRAINT vt_indiv_uid_fkey FOREIGN KEY (uid) REFERENCES users(uid);
DELETE FROM vt_indiv WHERE tid NOT IN (SELECT tid from inpt);
ALTER TABLE vt_indiv ADD CONSTRAINT vt_indiv_tid_fkey FOREIGN KEY (tid) REFERENCES inpt(tid);

ALTER TABLE vt_rights MODIFY COLUMN uid int UNSIGNED NOT NULL;
DELETE FROM vt_rights WHERE uid NOT IN (SELECT uid from users);
ALTER TABLE vt_rights ADD CONSTRAINT vt_rights_uid_fkey FOREIGN KEY (uid) REFERENCES users(uid);
ALTER TABLE vt_rights MODIFY COLUMN kid int UNSIGNED NOT NULL;
ALTER TABLE vt_rights ADD CONSTRAINT vt_rights_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);

ALTER TABLE vt_settings MODIFY COLUMN kid int UNSIGNED NOT NULL;
ALTER TABLE vt_settings ADD CONSTRAINT vt_settings_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);
