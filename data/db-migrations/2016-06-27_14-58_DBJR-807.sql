UPDATE `proj` SET `theme_id` = (SELECT `id` FROM `theme` ORDER BY `id` LIMIT 1)
WHERE `theme_id` IS NULL AND color_accent_1 IS NULL AND color_accent_2 IS NULL AND color_primary IS NULL;
