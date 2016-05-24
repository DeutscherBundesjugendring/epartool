CREATE TABLE `video_service` (   `name` varchar(255) NOT NULL );
ALTER TABLE `video_service` ADD PRIMARY KEY `name` (`name`);
INSERT INTO `video_service` (`name`) VALUES ('vimeo');
INSERT INTO `video_service` (`name`) VALUES ('youtube');
INSERT INTO `video_service` (`name`) VALUES ('facebook');

ALTER TABLE `inpt`
ADD INDEX `inpt_video_service_fkey` (`video_service`);

ALTER TABLE `input_discussion`
ADD INDEX `input_discussion_video_service_fkey` (`video_service`);

ALTER TABLE `input_discussion`
ADD FOREIGN KEY (`video_service`) REFERENCES `video_service` (`name`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `inpt`
ADD FOREIGN KEY (`video_service`) REFERENCES `video_service` (`name`) ON DELETE RESTRICT ON UPDATE RESTRICT;
