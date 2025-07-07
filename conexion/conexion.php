<?php
class CConexion {
    static function ConexionBD() {
        $host = getenv("DB_HOST");
        $dbname = getenv("DB_NAME");
        $username = getenv("DB_USER");
        $password = getenv("DB_PASSWORD");
        $port = getenv("DB_PORT");
        try {
            $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
            return $conn;
        } catch (PDOException $exp) {
            return null;
        }
    }
}