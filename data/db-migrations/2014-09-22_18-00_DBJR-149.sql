DROP TABLE IF EXISTS `email_attachment`;
CREATE TABLE `email_attachment` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `email_id` int UNSIGNED NOT NULL,
    `filepath` varchar(255) NOT NULL,
    PRIMARy KEY (`id`),
    CONSTRAINT `email_attachment_email_id_ibfk` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`)
);
