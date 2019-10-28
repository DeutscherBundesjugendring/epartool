<?php

namespace Util;

use PDO;

class Db {

    const CRYPT_COST = 10;

    /** @var PDO */
    private $pdo;

    /** @var string */
    private $dbName;

    public function __construct(string $dbName, string $dbHost, string $dbUserName, string $dbPass)
    {
        $this->dbName = $dbName;
        $this->pdo = new PDO(sprintf('mysql:dbname=%s;host=%s;charset=utf8mb4', $dbName, $dbHost), $dbUserName, $dbPass);
    }

    public function initDb(
        string $sqlPath,
        string $adminName,
        string $adminEmail,
        string $adminPassword,
        string $locale,
        callable $phinxMigrate
    ): int {
        $this->execSql(sprintf(
            'ALTER DATABASE `%s` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci',
            $this->dbName
        ));

        $this->execSql(file_get_contents($sqlPath . '/create-installation.sql'));
        $phinxMigrate();
        $this->execSql(file_get_contents($sqlPath . '/create-project-de.sql'));
        $this->execSql(file_get_contents($sqlPath . '/create-sample-data-de.sql'));
        $this->execStatement(
            "INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES (:name, :email, :password, 'admin');",
            [':name' => $adminName, ':email' => $adminEmail, ':password' => $this->encryptPassword($adminPassword)]
        );
        $this->execStatement(
            "UPDATE `proj` SET `locale` = :locale;",
            [':locale' => $locale]
        );

        return $this->getConsultationId();
    }

    private function encryptPassword(string $password): string
    {
        $salt = '$2y$' . self::CRYPT_COST . '$' . bin2hex(openssl_random_pseudo_bytes(22));

        return crypt($password, $salt);
    }

    private function execSql(string $sql)
    {
        if ($this->pdo->exec($sql) === false) {
            throw new \Exception(print_r($this->pdo->errorInfo(), true));
        }
    }

    private function execStatement(string $statementSql, array $params)
    {
        $statement = $this->pdo->prepare($statementSql);
        if ($statement->execute($params) === false) {
            throw new \Exception(print_r($statement->errorInfo(), true));
        }
    }

    private function getConsultationId(): int {
        $rows = $this->pdo->query('SELECT kid FROM cnslt')->fetchAll();
        if (count($rows) !== 1) {
            throw new \Exception('Installer should create exactly one consultation, none or more then one were found.');
        }

        return $rows[0]['kid'];
    }
}
