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
        $this->phinxMigrate('production');
        $this->phinxMigrate('test');
        $this->test();
    }

    public function install()
    {
        $this->say('Install method is not supported.');
    }

    public function phpcs()
    {
        $this
            ->taskExec('vendor/bin/phpcs')
            ->args('--standard=.php_cs_ruleset.xml')
            ->args('--encoding=utf-8')
            ->args(self::APP_DIR)
            ->args(self::LIB_DIR)
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
        $this->taskExec('mkdir www/media/folders/misc || exit 0')->run();
        $this->taskExec('cp www/images/logo@2x.png www/media/folders/misc/logo.png')->run();
        $this->taskExec('zip')
            ->args('--recurse-paths')
            ->args('--quiet')
            ->args('dbjr-tool.zip')
            ->args('.')
            ->option('--include', 'VERSION.txt')
            ->option('--include', '.htaccess')
            ->option('--include', 'application/*')
            ->option('--include', 'data/*')
            ->option('--exclude', 'data/db-migrations/*')
            ->option('--exclude', 'data/phinx-migrations/*')
            ->option('--include', 'install/*')
            ->option('--include', 'languages/*')
            ->option('--include', 'languages_zend/*')
            ->option('--include', 'library/*')
            ->option('--include', 'runtime/*')
            ->option('--include', 'vendor/*')
            ->option('--include', 'www/css/*')
            ->option('--include', 'www/fonts/*')
            ->option('--include', 'www/images/*')
            ->option('--include', 'www/js/*')
            ->option('--include', 'www/vendor/*')
            ->option('--include', 'www/index.php')
            ->option('--include', 'www/robots.txt')
            ->option('--include', 'www/.htaccess')
            ->option('--include', 'www/media/consultations/1/consultation_thumb_micro_scholl.jpg')
            ->option('--include', 'www/media/folders/misc/logo.png')
            ->option('--exclude', 'application/configs/config.local.ini')
            ->option('--exclude', 'runtime/cache/*')
            ->option('--exclude', 'runtime/sessions/*')
            ->option('--exclude', 'runtime/logs/*')
            ->option('--exclude', '*.git*')
            ->option('--exclude', '*.keep')
            ->run();
        $this->taskExec('rm www/media/consultations/1/consultation_thumb_micro_scholl.jpg')->run();
        $this->taskExec('rm www/media/folders/misc/logo.png')->run();
    }

    /**
     * @param string $environment
     */
    public function phinxMigrate($environment)
    {
        $this
            ->taskExec('vendor/bin/phinx migrate')
            ->option('-c', self::PHINX_CONFIG_FILE)
            ->option('-e', $environment)
            ->run();
    }

    /**
     * @param string $name
     */
    public function phinxCreate($name)
    {
        $this
            ->taskExec('bin/phinxCreate.sh')
            ->arg($name)
            ->run();
    }

    private function prepareTestDb()
    {
        $this->stopOnFail(true);
        $this->createTestDb();
        $this
            ->taskExec(sprintf('cat %s > %s', implode(' ', self::TEST_SQL_FILES_TO_IMPORT), self::TEST_TMP_SQL_FILE))
            ->run();
    }

    public function build()
    {
        $this->stopOnFail(true);
        $this->taskExec('bower install')->run();
        $this->taskComposerInstall()->run();
        $this->taskNpmInstall()->run();
        $this->taskExec('grunt')->run();
        $this->taskExec('NODE_ENV=production webpack -p')->run();
        $this->taskExec('webpack')->run();
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
