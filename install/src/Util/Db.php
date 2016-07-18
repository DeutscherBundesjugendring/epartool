<?php

namespace Util;

use PDO;

class Db {

    private $pdo;

    /**
     * Db constructor.
     * @param string $dbName
     * @param string $dbHost
     * @param string $dbUserName
     * @param string $dbPass
     */
    public function __construct($dbName, $dbHost, $dbUserName, $dbPass)
    {
        $this->pdo = new PDO(sprintf('mysql:dbname=%s;host=%s;charset=utf8', $dbName, $dbHost), $dbUserName, $dbPass);
    }

    /**
     * @param string $sqlPath
     * @param string $adminName
     * @param string $adminEmail
     */
    public function initDb($sqlPath, $adminName, $adminEmail, $locale)
    {
        $this->execSql(sprintf(
            'ALTER DATABASE `%s` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci',
            $_POST['dbName']
        ));
        $this->execSql(file_get_contents(realpath($sqlPath . '/create-installation.sql')));
        $this->execSql(file_get_contents(realpath($sqlPath . '/create-project-de.sql')));
        $this->execSql(file_get_contents(realpath($sqlPath . '/create-sample-data-de.sql')));
        $this->execStatement(
            "INSERT INTO `users` (`name`, `email`, `password`, `lvl`) VALUES (:name, :email, 1, 'adm');",
            [':name' => $adminName, ':email' => $adminEmail]
        );
        $this->execStatement(
            "UPDATE `proj` SET `locale` = :locale;",
            [':locale' => $locale]
        );
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
