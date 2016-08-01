ALTER TABLE `proj`
DROP `field_switch_name`,
DROP `field_switch_age`,
DROP `field_switch_state`,
DROP `field_switch_comments`,
DROP `field_switch_contribution_origin`,
DROP `field_switch_individuals_num`,
DROP `field_switch_group_name`,
DROP `field_switch_contact_person`,
DROP `field_switch_notification`,
DROP `field_switch_newsletter`,
DROP `allow_groups`;

ALTER TABLE `cnslt`
ADD `field_switch_name` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_age` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_state` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_comments` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `cnslt`
ADD `allow_groups` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_contribution_origin` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_individuals_sum` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_group_name` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_contact_person` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `cnslt`
ADD `field_switch_notification` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_newsletter` tinyint(1) NOT NULL DEFAULT '1';
