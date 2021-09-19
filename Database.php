<?php

class Database {

    /**
     * Database class properties
     */
    protected $connection;
    protected $query;
    protected $showErrors = true;
    protected $queryClosed = true;
    public $queryCount = 0;


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
        $dbHost = 'localhost',
        $dbUser = 'root',
        $dbPass = '',
        $dbName = '',
        $charset = 'utf8'
    ) {

        // create new database object
        $this->connection = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // check if there is a connection error
        if ($this->connection->connect_error) $this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);

        // set the msqli charset
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

        // close the query connection if the $queryClosed flag is false
        if (!$this->queryClosed) $this->query->close();

        // if the sql statement is getting prepared for execution
        if ($this->query = $this->connection->prepare($query)) {

            // if the number of passed arguments is greater than one
            if (func_num_args() > 1) {

                // all argument variables
                $functionArgs = func_get_args();
                $allArguments = array_slice($functionArgs, 1);
                $types = '';
                $argumentsRef = [];

                // for each argument
                foreach ($allArguments as $index => &$argument) {

                    // check if the argument is an array
                    if (is_array($allArguments[$index])) {

                        //if so add each of its value types to the types string
                        foreach ($allArguments[$index] as $argIndex => &$functionArgumentArray) {

                            // add the type to the types sting
                            $types .= $this->_gettype($allArguments[$index][$argIndex]);

                            // push the specific array to the argumentRef array
                            $argumentsRef[] = &$functionArgumentArray;
                        }
                    } else {

                        // add the type to the types sting
                        $types .= $this->_gettype($allArguments[$index]);

                        // push the array to the argumentRef array
                        $argumentsRef[] = &$argument;
                    }

                    // push the $types string in the beginning of the $argumentsRef array
                    array_unshift($argumentsRef, $types);

                    // bind the values of the $argumentsRef
                    call_user_func_array([$this->query, 'bind_param'], $argumentsRef);
                }
            }

            // execute the query
            $this->query->execute();

            // if the query result is an error print it
            if ($this->query->errno) $this->error('Unable to process MySQL query (check your params) - ' . $this->query->error);

            // close the connection by setting the $queryClosed flag to false
            $this->queryClosed = false;

            // increment the $queryCount
            $this->queryCount++;
        } else {

            // print the error message if the sql statement can't be prepared
            $this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error);
        }

        //return the query results or the specific error message
        return $this;
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

        // return a character based on the data type of a variable
        if (is_string($var)) return 's';
        if (is_float($var))  return 'd';
        if (is_int($var))    return 'i';

        // return 'b' if the variable isn't a string, float or integer
        return 'b';
    }
}