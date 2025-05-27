<?php
class CConexion {
    static function ConexionBD() {
        $host = "mainline.proxy.rlwy.net";
        $dbname = "railway"; // Reemplaza con el nombre de tu base de datos
        $username = "postgres"; // Reemplaza con tu usuario de base de datos
        $password = "PlODJaMiNTNSbCvuomGjZfLVdPVzwQzY"; // Reemplaza con tu contraseña de base de datos
        $port = "48148";
        try {
            $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);

            return $conn;
        } catch (PDOException $exp) {
        return $null;
        }

        
    }
}
