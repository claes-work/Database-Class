<?php

class Database {

    /**
     * Database class properties
     */
    protected $connection;
    protected $query;
    protected $showErrors  = true;
    protected $queryClosed = true;
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
        if ($this->connection->connect_error) $this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);

        // set the mysqli charset
        $this->connection->set_charset($charset);
    }

    /**
     * Query function that automatically bind parameters
     *
     * @param $query
     *
     * @return $this
     */
    public function query($query) {

        // close the query connection if the value of the $queryClosed flag is false
        if (!$this->queryClosed) $this->query->close();

        // if the sql statement is getting prepared for execution and the number of passed arguments is greater than 1
        if ($this->query = $this->connection->prepare($query)) {
            if (func_num_args() > 1) {

                // all argument variables
                $functionArgs = func_get_args();
                $allArguments = array_slice($functionArgs, 1);
                $types        = '';
                $argumentsRef = [];
                
                foreach ($allArguments as $index => &$argument) {

                    // check if $argument is an array
                    if (is_array($allArguments[$index])) {

                        // if so add each of its value types to the types string and push the $argumentArray to the argumentRef array
                        foreach ($allArguments[$index] as $argIndex => &$argumentArray) {
                            $types .= $this->_gettype($allArguments[$index][$argIndex]);
                            $argumentsRef[] = &$argumentArray;
                        }
                    } else {

                        // otherwise, add the single value type and push the argument to the argumentRef array
                        $types .= $this->_gettype($allArguments[$index]);
                        $argumentsRef[] = &$argument;
                    }

                    // push the $types string in the beginning of the $argumentsRef array
                    array_unshift($argumentsRef, $types);

                    // bind the values of the $argumentsRef
                    call_user_func_array([$this->query, 'bind_param'], $argumentsRef);
                }
            }
            
            $this->query->execute();

            // if the query throws an error print it
            if ($this->query->errno) $this->error('Unable to process MySQL query (check your params) - ' . $this->query->error);

            // set the $queryClosed flag to false
            $this->queryClosed = false;

            // increment the $queryCount
            $this->queryCount++;
        } else {

            // print the error message if the sql statement can't be prepared
            $this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error);
        }

        // return the query results or the specific error message
        return $this;
    }

    /**
     * Fetch multiple records from a database
     *
     * @param null $callback
     *
     * @return array
     */
    public function fetchAll($callback = null) {

        // declare array variables
        $params  = [];
        $row     = [];
        $results = [];

        // get the metadata of the query
        $meta = $this->query->result_metadata();

        // push the values to the $params array while there are columns in a table's row
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        // bind the values of the $params array
        call_user_func_array(array($this->query, 'bind_result'), $params);

        // while there are entries to fetch
        while ($this->query->fetch()) {

            // associative array of all rows that are getting fetched
            $allRows = [];

            // push each row with its key to the $allRows array
            foreach ($row as $key => $val) {
                $allRows[$key] = $val;
            }

            // if there is a callback specified and the callback is callable
            if ($callback != null && is_callable($callback)) {

                // call the specified callback of
                $value = call_user_func($callback, $allRows);
                if ($value == 'break') break;
            } else {

                // push the $allRows array to the $results array
                $results[] = $allRows;
            }
        }

        // close the existing connection
        $this->query->close();

        // set the $queryClosed flag to true
        $this->queryClosed = TRUE;

        // return the query results
        return $results;
    }


    /**
     * Fetch a record from a database
     *
     * @return array
     */
    public function fetchArray() {

        // declare array variables
        $params = [];
        $row    = [];
        $result = [];

        // get the metadata of the query
        $meta = $this->query->result_metadata();

        // push the values to the $params array while there are columns in a table's row
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        // bind the values of the $params array
        call_user_func_array([$this->query, 'bind_result'], $params);

        // while there are entries to fetch push each of it with its key to the $result array
        while ($this->query->fetch()) {
            foreach ($row as $key => $val) {
                $result[$key] = $val;
            }
        }

        // close the existing connection
        $this->query->close();

        // set the $queryClosed flag to true
        $this->queryClosed = TRUE;

        // return the query result
        return $result;
    }


    /**
     * Close the existing connection
     *
     * @return bool
     */
    public function close() {
        return $this->connection->close();
    }


    /**
     * Get the number of rows from a query result
     *
     * @return mixed
     */
    public function numRows() {
        $this->query->store_result();
        return $this->query->num_rows;
    }


    /**
     * Get all affected rows
     *
     * @return mixed
     */
    public function affectedRows() {
        return $this->query->affected_rows;
    }


    /**
     * Get the last inserted id
     *
     * @return int|string
     */
    public function lastInsertID() {
        return $this->connection->insert_id;
    }


    /**
     * Exit the function and output the specific error message if there is an error
     *
     * @param $error
     */
    public function error($error) {
        if ($this->showErrors) exit($error);
    }


    /**
     * Get the type of a specific variable
     *
     * @param $var
     *
     * @return string
     */
    private function _gettype($var) {

        // return a character based on the datatype of a variable
        if (is_string($var)) return 's';
        if (is_float($var))  return 'd';
        if (is_int($var))    return 'i';

        // return 'b' if the variable is neither a string, float nor integer
        return 'b';
    }
}
