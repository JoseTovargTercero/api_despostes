<?php
class DB {
    private $host = "localhost";
    private $user = "user_db";
    private $pass = "h51R8p,ORI4D";
    private $db = "desposte_membresia";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        $this->conn->set_charset("utf8");

        if ($this->conn->connect_error) {
            http_response_code(500);
            die(json_encode(["error" => "Error de conexión"]));
        }
    }
}
?>
