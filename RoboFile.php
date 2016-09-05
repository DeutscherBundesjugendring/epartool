<?php

require('vendor/autoload.php');

use Robo\Tasks;

/**
 * Class RoboFile
 */
class RoboFile extends Tasks
{
    const CONFIG_FILE = 'application/configs/config.ini';
    const APP_DIR = 'application';
    const LIB_DIR = 'library';

    public function test()
    {
        $this->stopOnFail(true);
        $this->phpcs();
    }

    public function build()
    {
        $this->taskExecStack()
            ->stopOnFail()
            ->exec('composer install')
            ->exec('bower update')
            ->exec('npm update')
            ->exec('grunt')
            ->run();
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
            ->args(implode(' ', [self::APP_DIR, self::LIB_DIR,]))
            ->run();
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
            "#\[production\]([\s]+version[\s]\=[\s]\"[a-z0-9\-\.]*\"|)#",
            "[production]\n\nversion = \"" . $tag . "\"",
            $configFileContent
        );
        if (null === $newConfigFileContent) {
            return sprintf('Cannot add version into file %s.', self::CONFIG_FILE);
        }
        if (false === file_put_contents(__DIR__ . '/' . self::CONFIG_FILE, $newConfigFileContent)) {
            return sprintf('Cannot write into file %s.', self::CONFIG_FILE);
        }

        return null;
    }
}
