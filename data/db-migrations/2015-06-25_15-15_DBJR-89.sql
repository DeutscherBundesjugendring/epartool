DROP TABLE `partner`;

CREATE TABLE `footer` (
    `id` int AUTO_INCREMENT,
    `proj` char(2) NOT NULL,
    `text` text NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `footer_proj_ibfk` FOREIGN KEY (`proj`) REFERENCES `proj` (`proj`)
) Engine=InnoDb, COLLATE=utf8_unicode_ci;

INSERT INTO footer (proj) (SELECT proj FROM proj);
INSERT INTO footer (proj) (SELECT proj FROM proj);
INSERT INTO footer (proj) (SELECT proj FROM proj);
INSERT INTO footer (proj) (SELECT proj FROM proj);
