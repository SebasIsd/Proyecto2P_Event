<?php
class Conexion {
    private $conexion;

    public function __construct() {
        $host = "maglev.proxy.rlwy.net";
        $port = "10622";
        $dbname = "railway";
        $user = "postgres";
        $password = "knFFZcmuIhowgwGNQmnUMGuSMxkNTdqA";

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

<?php
/*
<?php
class Conexion {
    private $conexion;

    public function __construct() {
        // Leer desde variables de entorno (definidas en docker-compose.yml)
        $host = getenv("DB_HOST");
        $port = getenv("DB_PORT");
        $dbname = getenv("DB_NAME");
        $user = getenv("DB_USER");
        $password = getenv("DB_PASSWORD");

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

*/
