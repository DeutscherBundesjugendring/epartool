<?php

abstract class Service_Cron
{
    const HASH_FILE_NAME = '.cronHash';
    const LOCK_FILE_NAME = '.cronLock';
    const HASH_FILE_EXPIRE_INTERVAL = 20; // seconds
    const E_CODE_HASH_IN_USE = 1;

    /**
     * @var resource
     */
    private static $lockFilePointer = null;

    public static function executeAll()
    {
        (new Service_Cron_ContributionsConfirmationReminder())->execute();
        (new Service_Cron_ReminderConfirmVoting())->execute();
        (new Service_Cron_Mail())->execute();
        (new Service_Cron_CleanMailArchive())->execute();
        (new Service_Cron_Logrotate())->execute();
    }

    abstract public function execute();

    /**
     * @return string
     * @throws Exception
     */
    public static function getHash(): string
    {
        if (self::hashExists()) {
            throw new Exception(
                'Hash was already created in other request and it will be in use probably.',
                self::E_CODE_HASH_IN_USE
            );
        }

        return self::createHash();
    }

    /**
     * @param string $hash
     * @return bool
     */
    public static function verifyHash(string $hash): bool
    {
        $readHash = self::readHash();
        if ($readHash !== null && $readHash === $hash) {
            self::destroyHash();

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function enterCriticalSection(): bool
    {
        return self::getLock();
    }

    /**
     * @return bool
     */
    public static function waitForEnterCriticalSection(): bool
    {
        return self::getLock(true);
    }

    /**
     * @param bool $blockingCall
     * @return bool
     */
    private static function getLock(bool $blockingCall = false): bool
    {
        self::$lockFilePointer = fopen(self::getLockFilePath(), 'w');
        if (!flock(self::$lockFilePointer, ($blockingCall ? LOCK_EX : (LOCK_EX | LOCK_NB)))) {
            fclose(self::$lockFilePointer);
            self::$lockFilePointer = null;

            return false;
        }

        return true;
    }

    public static function leaveCriticalSection()
    {
        if (self::$lockFilePointer === null) {
            return;
        }

        flock(self::$lockFilePointer, LOCK_UN);
        fclose(self::$lockFilePointer);
        self::$lockFilePointer = null;
    }

    /**
     * @return string
     */
    private static function getLockFilePath(): string
    {
        return sprintf('%s/%s', RUNTIME_PATH, self::LOCK_FILE_NAME);
    }

    /**
     * @return string
     */
    private static function getHashFilePath(): string
    {
        return sprintf('%s/%s', RUNTIME_PATH, self::HASH_FILE_NAME);
    }

    /**
     * @return string|null
     */
    private static function readHash()
    {
        if (!self::hashExists()) {
            return null;
        }

        return file_get_contents(self::getHashFilePath());
    }

    private static function destroyHash()
    {
        @unlink(self::getHashFilePath());
    }

    /**
     * @return bool
     */
    private static function hashExists(): bool
    {
        clearstatcache();

        return (file_exists(self::getHashFilePath())
            && filectime(self::getHashFilePath()) + self::HASH_FILE_EXPIRE_INTERVAL > time());
    }

    /**
     * @return string
     */
    private static function createHash(): string
    {
        $newHash = md5(microtime() . rand() . Zend_Registry::get('systemconfig')->security->token);
        file_put_contents(self::getHashFilePath(), $newHash);

        return $newHash;
    }
}
