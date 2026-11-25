<?php
/**
 * Connexion à la base de données green_tunisia avec PDO.
 * Utilisation : $pdo = Database::getConnection();
 */
class Database
{
    private const HOST     = 'localhost';
    private const DBNAME   = 'green_tunisia';
    private const USER     = 'root';
    private const PASSWORD = '';          // XAMPP : mot de passe vide par défaut
    private const CHARSET  = 'utf8mb4';

    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . self::HOST
                 . ';dbname=' . self::DBNAME
                 . ';charset=' . self::CHARSET;

            try {
                self::$pdo = new PDO($dsn, self::USER, self::PASSWORD, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // On n'affiche pas le détail technique à l'utilisateur
                error_log('Erreur de connexion BD : ' . $e->getMessage());
                die('Erreur : impossible de se connecter à la base de données.');
            }
        }

        return self::$pdo;
    }
}
