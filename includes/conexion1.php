<?php
class Conexion {
    private $conexion;

    public function __construct() {
        $host = "mainline.proxy.rlwy.net";
        $port = "48148";
        $dbname = "railway";
        $user = "postgres";
        $password = "PlODJaMiNTNSbCvuomGjZfLVdPVzwQzY";

        $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
        $this->conexion = pg_connect($conn_string);
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function cerrar() {
        if ($this->conexion) {
            pg_close($this->conexion);
        }
    }
}
?>
