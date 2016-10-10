<?php

use Robo\Tasks;

/**
 * Class RoboFile
 */
class RoboFile extends Tasks
{
    const CONFIG_FILE = 'application/configs/config.ini';
    const CONFIG_LOCAL_FILE = 'application/configs/config.local.ini';
    const PHINX_CONFIG_FILE = 'application/configs/phinx.local.yml';
    const APP_DIR = 'application';
    const LIB_DIR = 'library';
    const TEST_SQL_FILES_TO_IMPORT = [
        'data/create-installation.sql',
        'data/create-project-de.sql',
        'tests/_data/create-test-admin.sql',
        'tests/_data/set_locale.sql',
    ];
    const TEST_TMP_SQL_FILE = '.tmp/dump.sql';

    public function test()
    {
        $this->stopOnFail(true);
        $this->lintPhp();
        $this->phpcs();
        $this->codecept();
    }

    public function codecept()
    {
        $this->prepareTestDb();
        $this->taskCodecept('vendor/bin/codecept')->suite('acceptance')->run();
    }

    public function update()
    {
        $this->stopOnFail(true);
        $this->clearCache();
        $this->build();
        $this->dbMigrateProd();
        $this->test();
    }

    public function install()
    {
        $this->say('Install method is not supported.');
    }

    /**
     * @param string $tag
     */
    public function release($tag)
    {
        $this->stopOnFail(true);
        $result = $this->addVersionToConfig($tag);
        if ($result !== null) {
            $this->say($result);
            return;
        }
        $this->taskGitStack()
            ->stopOnFail()
            ->add('-A')
            ->commit('insert version info into config.ini')
            ->run();
        $this->taskExecStack()
            ->stopOnFail()
            ->exec(sprintf('git tag %s', $tag))
            ->exec('git push')
            ->exec('git push --tags')
            ->run();

        $this->say(sprintf('Version %s released.', $tag));
    }

    public function phpcs()
    {
        $this
            ->taskExec('vendor/bin/phpcs')
            ->args('--standard=.php_cs_ruleset.xml')
            ->args('--encoding=utf-8')
            ->args(implode(' ', [self::APP_DIR, self::LIB_DIR]))
            ->run();
    }

    public function lintPhp()
    {
        $this
            ->taskExec(vsprintf('find %s -name "*.php" -print0 | xargs -0 -n1 -P8 php -l', [
                implode(' ', [self::APP_DIR, self::LIB_DIR]),
            ]))
            ->run();
    }

    public function createZip()
    {
        $this->stopOnFail(true);
        $this->build();
        $this->taskExec('cp install/images/consultation_thumb_micro_scholl.jpg www/media/consultations/1')->run();
        $this->taskExec('cp www/images/logo@2x.png www/media/folders/misc/logo.png')->run();
        $this->taskExec('zip')
            ->arg('--recurse-paths')
            ->arg('--quiet')
            ->arg('dbjr-tool.zip')
            ->arg('.')
            ->arg('--include .htaccess')
            ->arg('--include application/\*')
            ->arg('--include data/\*')
            ->arg('--include install/\*')
            ->arg('--include languages/\*')
            ->arg('--include library/\*')
            ->arg('--include runtime/\*')
            ->arg('--include vendor/\*')
            ->arg('--include www/css/\*')
            ->arg('--include www/fonts/\*')
            ->arg('--include www/images/\*')
            ->arg('--include www/js/\*')
            ->arg('--include www/vendor/\*')
            ->arg('--include www/index.php')
            ->arg('--include www/robots.txt')
            ->arg('--include www/.htaccess')
            ->arg('--include www/media/consultations/1/consultation_thumb_micro_scholl.jpg')
            ->arg('--include www/media/folders/misc/logo.png')
            ->arg('--exclude application/configs/config.local.ini')
            ->arg('--exclude runtime/cache/\*')
            ->arg('--exclude runtime/sessions/\*')
            ->arg('--exclude runtime/logs/\*')
            ->arg('--exclude \*.git*')
            ->arg('--exclude \*.keep')
            ->run();
        $this->taskExec('rm www/media/consultations/1/consultation_thumb_micro_scholl.jpg')->run();
        $this->taskExec('rm www/media/folders/misc/logo.png')->run();

    }

    /**
     * Zend_Config_* was not used because of ignoring comments in the ini file which were not writed back after editing
     * process.
     * @param string $tag
     * @return null|string error
     */
    private function addVersionToConfig($tag)
    {
        $configFileContent = file_get_contents(__DIR__ . '/' . self::CONFIG_FILE);
        if ($configFileContent === null) {
            return sprintf('Cannot load %s.', self::CONFIG_FILE);
        }
        $newConfigFileContent = preg_replace(
            "#\[production\]\n(\n;[^\n]*\n|)([\s]*version[\s]\=[\s]\"[a-z0-9\-\.]*\"|)#",
            "[production]\n$1version = \"" . $tag . "\"",
            $configFileContent
        );
        if (null === $newConfigFileContent) {
            return sprintf('Cannot add version into file %s.', self::CONFIG_FILE);
        }
        if (false === file_put_contents(__DIR__ . '/' . self::CONFIG_FILE, $newConfigFileContent)) {
            return sprintf('Can write into file %s.', self::CONFIG_FILE);
        }

        return null;
    }

    private function prepareTestDb()
    {
        $this->stopOnFail(true);
        $this->createTestDb();
        $this
            ->taskExec(sprintf('cat %s > %s', implode(' ', self::TEST_SQL_FILES_TO_IMPORT), self::TEST_TMP_SQL_FILE))
            ->run();
    }

    private function build()
    {
        $this->stopOnFail(true);
        $this->taskExec('bower install')->run();
        $this->taskComposerInstall()->run();
        $this->taskNpmInstall()->run();
        $this->taskExec('grunt')->run();
    }

    private function dbMigrateProd()
    {
        $this
            ->taskExec('vendor/bin/phinx migrate')
            ->args(sprintf('-c %s', self::PHINX_CONFIG_FILE))
            ->args('-e default')
            ->run();
    }

    private function clearCache()
    {
        $this->stopOnFail(true);
        $this->taskExec('rm -rf runtime/cache/*')->run();
    }

    private function createTestDb()
    {
        $configLocal = parse_ini_file(self::CONFIG_LOCAL_FILE, true);
        $testConfig = $configLocal['test : production'];
        $connection = new PDO(
            sprintf(
                'mysql:host=%s;',
                $testConfig['resources.db.params.host']
            ),
            $testConfig['resources.db.params.username'],
            $testConfig['resources.db.params.password']
        );
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->exec(sprintf(
            "CREATE DATABASE IF NOT EXISTS `%s` COLLATE 'utf8_general_ci';",
            $testConfig['resources.db.params.dbname']
        ));
    }
}
