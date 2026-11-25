<?php

declare(strict_types=1);

/**
 * Classe Database - Gestionnaire de connexion PDO (Pattern Singleton).
 * Respecte l'exigence technique obligatoire : utilisation exclusive de PDO.
 */
class Database
{
    private static ?PDO $instance = null;

    private string $host;
    private string $dbName;
    private string $username;
    private string $password;
    private string $charset;

    private function __construct()
    {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->dbName = getenv('DB_NAME') ?: 'green_tunisia';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') !== false ? (string) getenv('DB_PASS') : '';
        $this->charset = 'utf8mb4';
    }

    /**
     * Retourne l'instance unique de PDO connectée à MySQL.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = new self();
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config->host,
                $config->dbName,
                $config->charset
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $config->username, $config->password, $options);
            } catch (PDOException $e) {
                throw new RuntimeException(
                    "Erreur de connexion à la base de données '{$config->dbName}' : " . $e->getMessage(),
                    (int) $e->getCode(),
                    $e
                );
            }
        }

        return self::$instance;
    }

    /**
     * Permet d'injecter une connexion PDO (utile pour les tests automatisés).
     */
    public static function setConnection(?PDO $pdo): void
    {
        self::$instance = $pdo;
    }
}
