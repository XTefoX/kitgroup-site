<?php
// includes/db.php
$host = 'localhost';     
$dbname = 'kitgroupdb';
$user   = 'root';
$pass   = '';

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        global $host, $dbname, $user, $pass;
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}
?>