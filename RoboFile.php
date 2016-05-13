<?php

use Robo\Tasks;

/**
 * Class RoboFile
 */
class RoboFile extends Tasks
{
    const COMPOSER_FILE = 'composer.json';

    /**
     * @param string $tag
     */
    public function release($tag)
    {
        $this->stopOnFail(true);
        $result = $this->addVersionToComposerFile($tag);
        if ($result !== null) {
            $this->say($result);
            return;
        }
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
    private function addVersionToComposerFile($tag)
    {
        $composerInfo = json_decode(file_get_contents(__DIR__ . '/' . self::COMPOSER_FILE));
        if ($composerInfo === null) {
            return sprintf('Cannot load %s.', self::COMPOSER_FILE);
        }
        $composerInfo->version = $tag;
        $jsonIndexedBy4 = json_encode($composerInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (false === $jsonIndexedBy4) {
            return 'Cannot encode json configuration';
        }
        $jsonIndexedBy2 = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $jsonIndexedBy4);
        if (false === file_put_contents(__DIR__ . '/' . self::COMPOSER_FILE, $jsonIndexedBy2 . PHP_EOL)) {
            return sprintf('Cannot write json configuration to %s.', self::COMPOSER_FILE);
        }
        return null;
    }
}
