INSERT INTO `articles_refnm` (`ref_nm`, `lng`, `desc`, `type`, `scope`)
VALUES
    ('article_explanation', 'de', 'Main consultation explanation text', 'b', 'info');

INSERT INTO `articles` (`kid`, `proj`, `desc`, `hid`, `ref_nm`, `artcl`, `sidebar`, `parent_id`)
SELECT
    `kid`, `proj`, '', 'n', 'article_explanation', `expl`, '', NULL
FROM
    `cnslt`;

ALTER TABLE `cnslt` DROP COLUMN `expl`;
