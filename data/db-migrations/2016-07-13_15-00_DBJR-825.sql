ALTER TABLE `proj`
ADD `field_switch_name` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_age` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_state` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_comments` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `proj`
ADD `allow_groups` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_contribution_origin` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_individuals_num` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_group_name` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_contact_person` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `proj`
ADD `field_switch_notification` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_newsletter` tinyint(1) NOT NULL DEFAULT '1';
