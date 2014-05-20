--
-- Revision of formerly unused table vt_final
-- Purpose: saves preliminary results of groups and in total
-- 
-- Renaming of table inpt.pts to inpt.place, so that it is more consistent with vt_final.place
--


DROP TABLE vt_final;

CREATE TABLE `vt_final` (
  `kid` smallint(5) unsigned NOT NULL COMMENT 'rel to Consultation ID',
  `tid` int(10) unsigned NOT NULL COMMENT 'related to which tid',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'related to which uid',
  `place` smallint(5) unsigned NOT NULL,
  `points` float NOT NULL COMMENT 'summary points (accumulated value)',
  `cast` int(11) NOT NULL COMMENT 'summary votes (accumulated value)',
  `rank` float NOT NULL COMMENT 'divident points/cast',
  `fowups` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'follow up exists?',
  PRIMARY KEY (`tid`) USING BTREE,
  UNIQUE KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='All votes cast';

ALTER TABLE `inpt` CHANGE `pts` `place` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Place (rank)';