<?php 

class database {

    private $host = "localhost";
    private $db_name = "litehouse";
    private $username = "root";
    private $password = "";
    public $conn;
    protected $key;

    public function getConnection() {

        $this->conn = null;

        try {

            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        } catch (Exeception $exeception) {
            echo "Connection Error: " .$exeception->getMessage();
        }

        return $this->conn;


    }




}



?>