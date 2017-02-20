<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1103 extends AbstractMigration
{
    public function up()
    {
        foreach (['fr_FR', 'pl_PL', 'cs_CZ', 'ru_RU', 'ar_AE'] as $locale)
        {
            $this->execute("INSERT INTO `language` (`code`) VALUES ('" . $locale . "')");
            $this->addLicense($locale);
        }
    }

    public function down()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0");
        $this->execute("DELETE FROM `license` WHERE `locale` IN ('fr_FR', 'pl_PL', 'cs_CZ', 'ru_RU', 'ar_AE')");
        $this->execute("DELETE FROM `language` WHERE `code` IN ('fr_FR', 'pl_PL', 'cs_CZ', 'ru_RU', 'ar_AE')");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1");
    }

    /**
     * @param string $locale
     */
    private function addLicense($locale)
    {
        $query = <<<'EOD'
INSERT INTO `license` (`number`, `title`, `description`, `text`, `link`, `icon`, `alt`, `locale`)
VALUES (
1, 'Creative Commons license', 'Creative Commons license 4.0: attribution, non-commercial',
'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\"
target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>.
This means that your contribution may be re-used in summaries and publications for non-commercial use.
As all contributions are published anonymously on this page, this website will be referred to as the source when
re-using contributions.',
'http://creativecommons.org/licenses/by-nc/4.0/deed.%lang%', 'license_cc.svg', 'CC-BY-NC 4.0', '%locale%')
EOD;
        $this->execute(str_replace(['%lang%', '%locale%'], [substr($locale, 0, 2), $locale], $query));
    }
}
