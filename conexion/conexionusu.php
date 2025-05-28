<?php
class ConexionUsu {
    private static $instancia = null;
    private $conexion;
    
    private function __construct() {
        $host = "mainline.proxy.rlwy.net";
        $dbname = "railway";
        $username = "postgres";
        $password = "PlODJaMiNTNSbCvuomGjZfLVdPVzwQzY";
        $port = "48148";
        
        try {
            $this->conexion = pg_connect("host=$host port=$port dbname=$dbname user=$username password=$password");
            if (!$this->conexion) {
                throw new Exception("Error de conexión a PostgreSQL");
            }
        } catch (Exception $e) {
            error_log("Error de conexión: " . $e->getMessage());
            $this->conexion = null;
        }
    }
    
    public static function obtenerConexion() {
        if (self::$instancia === null) {
            self::$instancia = new ConexionUsu();
        }
        return self::$instancia->conexion;
    }
    
    public static function cerrarConexion() {
        if (self::$instancia !== null && self::$instancia->conexion) {
            pg_close(self::$instancia->conexion);
            self::$instancia = null;
        }
    }
}
?>