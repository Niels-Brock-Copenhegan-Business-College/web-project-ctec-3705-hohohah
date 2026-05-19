<?php
declare(strict_types=1);

namespace App\config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    private const HOST    = 'localhost';
    private const DBNAME  = 'coursehub';
    private const USER    = 'root';
    private const PASS    = '';
    private const CHARSET = 'utf8mb4';

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DBNAME . ';charset=' . self::CHARSET;
            try {
                self::$connection = new PDO($dsn, self::USER, self::PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                die('Database connection error. Please try again later.');
            }
        }
        return self::$connection;
    }
}
