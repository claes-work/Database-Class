<?php

class Database {

    /**
     * Database class properties
     */
    protected $connection;
    protected $query;
    protected $showErrors  = TRUE;
    protected $queryClosed = TRUE;
    public    $queryCount  = 0;


    /**
     * Database constructor
     *
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @param string $dbName
     * @param string $charset
     */
    public function __construct(
        $dbHost  = 'localhost',
        $dbUser  = 'root',
        $dbPass  = '',
        $dbName  = '',
        $charset = 'utf8'
    ) {
        // create new database object
        $this->connection = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        // check if there is a connection error
        ($this->connection->connect_error) ?: $this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
        // set the msqli charset
        $this->connection->set_charset($charset);
    }


    public function query($query) {

        // close the query connection if the queryClosed flag is FALSE
        (!$this->queryClosed) ?: $this->query->close();

    }

}