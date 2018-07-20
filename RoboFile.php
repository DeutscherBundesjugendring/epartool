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
    const NODE_MODULES_DIR = 'node_modules';

    public function test()
    {
        $this->stopOnFail(true);
        $this->lintPhp();
        $this->phpcs();
        $this->codecept();
    }

    public function codecept()
    {
        $this->taskCodecept('vendor/bin/codecept')->run();
    }

    public function update()
    {
        $this->stopOnFail(true);
        $this->clearCache();
        // 23.01.2018 jiri@visionapps.cz: clean installation is needed due to error from phantomjs
        // second level dependency (in grunt-uncss package)
        if (file_exists(self::NODE_MODULES_DIR)) {
            $this->taskExec(sprintf('rm -r %s/*', self::NODE_MODULES_DIR))->run();
        }
        $this->build();
        $this->phinxMigrate('test');
        $this->phinxMigrate('production');
        $this->test();
    }

    public function testInstall()
    {
        $this->stopOnFail(true);
        $this->createConfigs();
        $this->build();
        $this->createDatabase('test');
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

    public function createInstallZip()
    {
        $fileName = sprintf('ePartool-install_%s.zip', $this->getVersion());

        $this->stopOnFail(true);
        $this->build();
        $this->taskExec('cp install/images/consultation_thumb_micro_scholl.jpg www/media/consultations/1')->run();
        $this->taskExec('mkdir www/media/folders/misc || exit 0')->run();
        $this->taskExec('cp www/images/logo@2x.png www/media/folders/misc/logo.png')->run();
        $this->taskExec('cp install/images/epartool_logo.png www/media/folders/misc')->run();
        $this->taskExec('cp install/images/Gruppenstunde_ImP-2013_Cover.jpg www/media/folders/misc')->run();
        $this->taskExec('cp install/images/Methodenkarten-250x310.jpg www/media/folders/misc')->run();
        $this->taskExec('zip')
            ->args('--recurse-paths')
            ->args('--quiet')
            ->args($fileName)
            ->args('.')
            ->option('--include', 'VERSION.txt')
            ->option('--include', 'README.md')
            ->option('--include', '.htaccess')
            ->option('--include', 'application/*')
            ->option('--include', 'data/*')
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
            ->option('--include', 'www/runMigrations.php')
            ->option('--include', 'www/.htaccess')
            ->option('--include', 'www/media/consultations/1/consultation_thumb_micro_scholl.jpg')
            ->option('--include', 'www/media/folders/misc/logo.png')
            ->option('--include', 'www/media/folders/misc/epartool_logo.png')
            ->option('--include', 'www/media/folders/misc/Gruppenstunde_ImP-2013_Cover.jpg')
            ->option('--include', 'www/media/folders/misc/Methodenkarten-250x310.jpg')
            ->option('--exclude', 'application/configs/config.local.ini')
            ->option('--exclude', 'runtime/cache/*')
            ->option('--exclude', 'runtime/sessions/*')
            ->option('--exclude', 'runtime/logs/*')
            ->option('--exclude', '*.git*')
            ->option('--exclude', '*.keep')
            ->run();
    }

    public function createUpdateZip()
    {
        $fileName = sprintf('ePartool-update_%s.zip', $this->getVersion());

        $this->stopOnFail(true);
        $this->build();
        $this->taskExec('zip')
            ->args('--recurse-paths')
            ->args('--quiet')
            ->args($fileName)
            ->args('.')
            ->option('--include', 'VERSION.txt')
            ->option('--include', 'README.md')
            ->option('--include', 'application/*')
            ->option('--include', 'data/phinx-migrations/*')
            ->option('--include', 'languages/*')
            ->option('--include', 'languages_zend/*')
            ->option('--include', 'library/*')
            ->option('--include', 'vendor/*')
            ->option('--include', 'www/css/*')
            ->option('--include', 'www/fonts/*')
            ->option('--include', 'www/images/*')
            ->option('--include', 'www/js/*')
            ->option('--include', 'www/vendor/*')
            ->option('--include', 'www/index.php')
            ->option('--include', 'www/robots.txt')
            ->option('--include', 'www/runMigrations.php')
            ->option('--exclude', 'application/configs/config.local.ini')
            ->option('--exclude', 'application/configs/phinx.local.yml')
            ->option('--exclude', '*.git*')
            ->option('--exclude', '*.keep')
            ->run();
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

    public function build()
    {
        $this->stopOnFail(true);
        $this->taskExec('bower install')->run();
        $this->taskNpmInstall()->run();
        $this->taskExec('grunt')->run();
        $this->taskExec('npm run build')->run();
    }

    private function clearCache()
    {
        $this->taskExec('rm -rf runtime/cache/*')->run();
    }

    private function createConfigs()
    {
        $file = self::APP_DIR . '/configs/config.local.ini';
        $fileTemplate = self::APP_DIR . '/configs/config.local-example.ini';
        if (!realpath($file)) {
            copy($fileTemplate, $file);
        }

        $file = self::APP_DIR . '/configs/phinx.local.yml';
        $fileTemplate = self::APP_DIR . '/configs/phinx.local-example.ini';
        if (!realpath($file)) {
            copy($fileTemplate, $file);
        }
    }

    /**
     * Cannot be called before the application is build
     * @param string $environment
     * @throws \Exception
     */
    private function createDatabase($environment)
    {
        $credentials = $this->loadDbCredentials('config.local', $environment);
        $this
            ->taskExec(sprintf(
            'mysql -u %s -h %s -p%s -e \'DROP DATABASE IF EXISTS %s\';',
            $credentials['username'],
            $credentials['host'],
            $credentials['password'],
            $credentials['dbname']
        ))
            ->run();
        $this
            ->taskExec(sprintf(
                'mysql -u %s -h %s -p%s -e \'CREATE DATABASE %s\';',
                $credentials['username'],
                $credentials['host'],
                $credentials['password'],
                $credentials['dbname']
                ))
            ->run();

        require_once 'install/src/Util/Db.php';
        $db = new \Util\Db(
            $credentials['dbname'],
            $credentials['host'],
            $credentials['username'],
            $credentials['password']
        );

        $db->initDb(
            realpath(dirname(__FILE__) . '/data'),
            'admin',
            'email@example.com',
            'pass',
            'de_DE',
            function () use ($environment) {
                $this->phinxMigrate($environment);
            }
        );
    }

    /**
     * Cannot be called before the application is build
     * @param string $iniConfigFileName
     * @param string $environment
     * @throws \Exception
     * @return string[]
     */
    private function loadDbCredentials($iniConfigFileName, $environment)
    {
        require_once 'vendor/autoload.php';
        $configLocal = new Zend_Config_Ini(
            sprintf('%s/configs/%s.ini', self::APP_DIR, $iniConfigFileName),
            $environment
        );
        if (!$configLocal) {
            throw new \Exception(
                sprintf('Cannot load section %s from config %s.ini', $environment, $iniConfigFileName)
            );
        }
        $dbCredentials = $configLocal;
        foreach (['resources', 'db', 'params'] as $section) {
            if (!$dbCredentials) {
                throw new \Exception(sprintf('No default DB credentials found in %s.ini', $iniConfigFileName));
            }
            $dbCredentials = $dbCredentials->{$section};
        }
        $dbCredentials = $dbCredentials->toArray();
        foreach (['host', 'dbname', 'username', 'password'] as $parameter) {
            if (!array_key_exists($parameter, $dbCredentials)) {
                throw new \Exception(
                    sprintf('%s parameter for db access is missing in %s.ini', $parameter, $iniConfigFileName)
                );
            }
        }

        return $dbCredentials;
    }

    /**
     * @return string
     */
    private function getVersion()
    {
        return trim(file_get_contents('VERSION.txt'));
    }

    public function deployFinalize()
    {
        $this->stopOnFail(true);
        $this->clearCache();
        $this->phinxMigrate('production');
    }
}
