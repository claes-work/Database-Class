<?php

class Database {

    /**
     * Database class properties
     */
    protected $connection;
    protected $query;
    protected $show_errors  = TRUE;
    protected $query_closed = TRUE;
    public    $query_count  = 0;


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

}