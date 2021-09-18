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

}