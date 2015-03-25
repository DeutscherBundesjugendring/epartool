CREATE TABLE `partner` (
    `id` int unsigned AUTO_INCREMENT,
    `proj` char(2) NOT NULL,
    `description` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `link_url` varchar(255),
    `image` varchar(255),
    `order` smallint NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `partner_order_idx` (`order`),
    CONSTRAINT `partner_proj_ibfk` FOREIGN KEY (`proj`) REFERENCES `proj`(`proj`)
) Engine=InnoDb, COLLATE=utf8_unicode_ci;
