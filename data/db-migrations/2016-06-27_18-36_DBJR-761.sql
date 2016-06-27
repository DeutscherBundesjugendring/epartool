INSERT INTO `parameter` (`name`, `proj`, `value`)
(
    SELECT
        'locale',
        `proj`.`proj`,
        'en_US'
    FROM
        proj
);
