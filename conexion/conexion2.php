<?php
class CConexion2 {
    private static $instancia = null;
    private $conexion;
    
    private function __construct() {
        $host = "mainline.proxy.rlwy.net";
        $dbname = "railway";
        $username = "postgres";
        $password = "PlODJaMiNTNSbCvuomGjZfLVdPVzwQzY";
        $port = "48148";
        
        try {
            $this->conexion = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            $this->conexion = null;
        }
    }
    
    public static function obtenerConexion() {
        if (self::$instancia === null) {
            self::$instancia = new CConexion2();
        }
        return self::$instancia->conexion;
    }
    
    public static function cerrarConexion() {
        if (self::$instancia !== null) {
            self::$instancia->conexion = null;
            self::$instancia = null;
        }
    }
}
?>