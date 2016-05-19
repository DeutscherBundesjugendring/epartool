<?php

require('../../autoload.php');

use Robo\Tasks;

/**
 * Class RoboFile
 */
class RoboFile extends Tasks
{
    const CONFIG_FILE = '../../../project/configs/config.ini';

    /**
     * @param string $tag
     */
    public function release($tag)
    {
        $this->stopOnFail(true);
        $this->addVersionToConfig($tag);
        $this->taskExecStack()
            ->stopOnFail()
            ->exec(sprintf('git tag %s', $tag))
            ->exec('git push')
            ->exec('git push --tags')
            ->run();

        $this->say(sprintf('Version %s released.', $tag));
    }

    /**
     * @param string $tag
     * @return null|string error
     */
    private function addVersionToConfig($tag)
    {
        $config = new Zend_Config_Ini(self::CONFIG_FILE, null, array('skipExtends' => true, 'allowModifications' => true));
        $config->production->version = $tag;
        $writer = new Zend_Config_Writer_Ini(array('config' => $config, 'filename' => self::CONFIG_FILE));
        $writer->write();
    }
}
