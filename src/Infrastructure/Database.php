<?php

declare(strict_types=1);

namespace App\Infrastructure;

use PDO;

/**
 * Fabrique de connexion PDO — utilisée par les repositories (infrastructure).
 */
final class Database
{
    public static function createPdo(): PDO
    {
        $host = getenv('DB_HOST') !== false && getenv('DB_HOST') !== '' ? getenv('DB_HOST') : 'db';
        $name = getenv('DB_NAME') !== false && getenv('DB_NAME') !== '' ? getenv('DB_NAME') : 'library';
        $user = getenv('DB_USER') !== false && getenv('DB_USER') !== '' ? getenv('DB_USER') : 'libuser';
        $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'libpass';

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
