<?php

class Database
{
    private ?PDO $pdo; // PDO connection
    private \PDOStatement $query;


    public function __construct(string $host, string $db_name, string $username, string $password)
    {
        $this->connect($host, $db_name, $username, $password); // Creates a database connection.
    }

    /**
     *
     * Gets the PDO connection instance
     *
     * @return PDO|null
     */
    public function getConn(): ?PDO
    {
        return $this->pdo;
    }

    /**
     *	Create Database Connection
     */
    private function connect(string $host, string $db_name, string $username, string $password)
    {
        $dsn = "mysql:dbname=$db_name;host=$host";

        // Tries to connect to the database.
        try
        {
            $this->pdo = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        }
        catch (PDOException $e)
        {
            // Kills the page and showing the error message.
            die($e->getMessage());
        }
    }

    public function closeConnection()
    {
        // http://www.php.net/manual/en/pdo.connections.php
        $this->pdo = null; // Unsets the PDO object in order to close the database connection
    }

    /**
     *
     * Init database and execute query
     *
     * @param string $query
     * @param array $parameters
     * @return void
     */
    private function init(string $query, array $parameters = []): void
    {
        try {
            // Prepares the query.
            if($q = $this->pdo->prepare($query))
                $this->query = $q;

//            // Parameterizes the query.
//            $this->bindParams($parameters);

            $this->query->execute($parameters);
        }
        catch(PDOException $e)
        {
            die($e->getMessage());
        }
    }

    /**
     *
     * Execute SQL query
     *
     * @param string $query
     * @param array $params
     * @param int $fetch_mode
     * @return null|int|array according to the query statement
     */
    public function query(string $query, array $params = [], int $fetch_mode = PDO::FETCH_ASSOC)
    {
        $this->init($query,$params);

        // Parsing the query statement type by first word
        $statement = explode(" ", trim(strtolower($query)))[0];

        $fetch_statements = ['select', 'show'];
        $row_count_statements = ['insert', 'update', 'delete'];

        // Returns a result according to the query statement
        return in_array($statement, $fetch_statements)
            ? $this->query->fetchAll($fetch_mode)
            : (in_array($statement, $row_count_statements)
                ? $this->query->rowCount()
                : null);
    }

    /**
     *
     * Returns a row from the result set as an array
     *
     * @param string $query
     * @param array $params
     * @param int $fetch_mode
     * @return mixed
     */
    public function fetchArr(string $query, array $params = [], int $fetch_mode = PDO::FETCH_ASSOC)
    {
        $this->init($query,$params);
        return $this->query->fetch($fetch_mode);
    }
    /**
     *	Returns the value of one single field/column
     *
     *	@param  string $query
     *	@param  array  $params
     *	@return string
     */
    public function read(string $query, array $params = []): string
    {
        $this->init($query,$params);
        return $this->query->fetchColumn();
    }
}