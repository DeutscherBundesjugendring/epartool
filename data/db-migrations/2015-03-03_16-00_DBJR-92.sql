CREATE TABLE `help_text` (
    `id` int unsigned AUTO_INCREMENT,
    `name` varchar(255),
    `body` text,
    PRIMARY KEY (`id`)
) Engine=InnoDb;

INSERT INTO `help_text` (`name`, `body`)
VALUES
    ('help-text-home', 'Sample home page text.'),
    ('help-text-consultation-info', 'Sample consultation-info page text.'),
    ('help-text-consultation-question', 'Sample consultation-question page text.'),
    ('help-text-consultation-input', 'Sample consultation-input page text.'),
    ('help-text-consultation-voting', 'Sample consultation-voting page text.'),
    ('help-text-consultation-followup', 'Sample consultation-followup page text.'),
    ('help-text-login', 'Sample consultation-followup page text.');
