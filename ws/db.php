<?php
function getDB() {
    $host = '127.0.0.1';
    $dbname = 'pret_bancaire';
    $username = 'sarb';
    $password = 'root.R00t';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
