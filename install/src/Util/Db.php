<?php

namespace Util;

use PDO;

class Db {

    const CRYPT_COST = 10;

    private $pdo;

    private $dbName;

    /**
     * Db constructor.
     * @param string $dbName
     * @param string $dbHost
     * @param string $dbUserName
     * @param string $dbPass
     */
    public function __construct($dbName, $dbHost, $dbUserName, $dbPass)
    {
        $this->dbName = $dbName;
        $this->pdo = new PDO(sprintf('mysql:dbname=%s;host=%s;charset=utf8mb4', $dbName, $dbHost), $dbUserName, $dbPass);
    }

    /**
     * @param string $sqlPath
     * @param string $adminName
     * @param string $adminEmail
     * @param string $adminPassword
     * @param string $locale
     * @param callback $phinxMigrate
     * @throws \Exception
     */
    public function initDb(
        $sqlPath,
        $adminName,
        $adminEmail,
        $adminPassword,
        $locale,
        $phinxMigrate
    ) {
        $this->execSql(sprintf(
            'ALTER DATABASE `%s` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci',
            $this->dbName
        ));

        $this->execSql(file_get_contents(realpath($sqlPath . '/create-installation.sql')));

        $phinxMigrate();

        $this->execSql(file_get_contents(realpath($sqlPath . '/create-project-de.sql')));
        $this->execSql(file_get_contents(realpath($sqlPath . '/create-sample-data-de.sql')));
        $this->execStatement(
            "INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES (:name, :email, :password, 'admin');",
            [':name' => $adminName, ':email' => $adminEmail, ':password' => $this->encryptPassword($adminPassword)]
        );
        $this->execStatement(
            "UPDATE `proj` SET `locale` = :locale;",
            [':locale' => $locale]
        );
    }

    /**
     * @param string $password
     * @return string
     */
    private function encryptPassword($password)
    {
        $salt = '$2y$' . self::CRYPT_COST . '$' . bin2hex(openssl_random_pseudo_bytes(22));

        return crypt($password, $salt);
    }

    /**
     * @param string $sql
     * @throws \Exception
     */
    private function execSql($sql)
    {
        if ($this->pdo->exec($sql) === false) {
            throw new \Exception(print_r($this->pdo->errorInfo(), true));
        }
    }

    /**
     * @param string $statementSql
     * @param array $params
     * @throws \Exception
     */
    private function execStatement($statementSql, array $params)
    {
        $statement = $this->pdo->prepare($statementSql);
        if ($statement->execute($params) === false) {
            throw new \Exception(print_r($statement->errorInfo(), true));
        }
    }
}
