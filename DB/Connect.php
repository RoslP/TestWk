<?php
Class Connect
{
    private static $pdo;
    static protected $dsnData = [
        'host' => 'localhost',
        'dbname' => 'DB',
        'charset' => 'utf8',
        'user' => 'root',
        'passwd' => 'mysql',
    ];
    public static function getConnection()
    {
        if (self::$pdo === null) {
            $dsn = "mysql:host=".self::$dsnData['host'].";dbname=".self::$dsnData['dbname'].";charset=".self::$dsnData['charset'];
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            try {
                self::$pdo = new PDO($dsn, self::$dsnData['user'], self::$dsnData['passwd'], $options);
            } catch (PDOException $e) {
                die("Ошибка подключения: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}