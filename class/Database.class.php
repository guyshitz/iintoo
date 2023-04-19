<?php

class Database
{
    private mysqli $conn;

    public function __construct(string $servername, string $username, string $password, $db_name)
    {
        $this->conn = new mysqli($servername, $username, $password, $db_name);

        if ($this->conn->connect_error)
            die("Connection failed: " . $this->conn->connect_error);
    }

    /**
     * @param string $query
     * @param array $params
     * @return mysqli_result|bool
     */
    public function exec(string $query, array $params = []): mysqli_result|bool
    {
        return $this->conn->execute_query($query, $params);
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->conn->close();
    }

    /**
     * @return mysqli
     */
    public function getConn(): mysqli {
        return $this->conn;
    }
}