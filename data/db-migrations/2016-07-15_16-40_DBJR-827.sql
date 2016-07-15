INSERT INTO `license` (`number`, `title`, `description`, `text`, `link`, `icon`, `alt`, `locale`)
VALUES
    (
        1,
        'Creative commons license',
        'Creative Commons 4.0: Namensnennung, nicht kommerziell, keine Bearbeitung',
        'Die Beiträge werden unter einer <a href="http://creativecommons.org/licenses/by-nc/4.0/deed.en" target="_blank" title="Mehr über die Creative-Commons-Lizenz erfahren">Creative-Commons-Lizenz</a> veröffentlicht. Das bedeutet, dass eure Beiträge in Zusammenfassungen und Publikationen zu nicht-kommerziellen Zwecken weiterverwendet werden dürfen. Da alle Beiträge hier anonym veröffentlicht werden, wird auch bei Weiterverwendung als Quelle nur diese Website genannt werden.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.de',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'de_DE'
    );

UPDATE `license`
SET
    `text` = 'The contributions are published under a <a href="http://creativecommons.org/licenses/by-nc/4.0/deed.en" target="_blank" title="More about creative commons license">creative commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.'
WHERE `number` = 1 AND locale = 'en_US';
