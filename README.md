# PHP-Database-Class

A lightweight an easy to use PHP database class that automatically uses prepared statements to secure your queries from SQL injection attacks.

## ⚙️ How it works

The database class uses the default **MySQLi extension**, which is built into PHP version ![>= 5.0.0.](https://img.shields.io/badge/->=%205.0.0.-777BB4?style=flat)\
If you're using lower PHP versions version you still can use this class but you have to install: [mysqlnd](https://www.php.net/manual/en/book.mysqlnd.php).

### Connect to your MySQL database:
```php
include 'Database.php';

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'example';

$db = new db($dbhost, $dbuser, $dbpass, $dbname);
```

### Fetch a single record from a database:
```php
$cars = $db->query('SELECT * FROM cars WHERE brand = ? AND vehicle_type = ?', 'mercedes', 'SUV')->fetchArray();
echo cars['name'];
```
Instead of passing each value individually to the query, you could also use an array of values.
```php
$cars = $db->query('SELECT * FROM cars WHERE brand = ? AND vehicle_type = ?', array('mercedes', 'SUV'))->fetchArray();
```

### Fetch multiple records from a database:
```php
$cars = $db->query('SELECT * FROM cars')->fetchAll();

foreach ($cars as $car) {
   echo $car['name'] . '<br>';
}
```
If you're using large amounts of data you might want to specify a callback so that the results won't be stored in an array.
```php
$db->query('SELECT * FROM $cars')->fetchAll(function($car) {
  echo $car['name'];
});
```
### Get the number of rows:
```php
$accounts = $db->query('SELECT * FROM cars');
echo $cars->numRows();
```
### Get the affected number of rows:
```php
$insert = $db->query('INSERT INTO cars (name, brand, vehicle_type) VALUES (?,?,?)', 'AMG GT R', 'Mercedes', 'Coupé');
echo $insert->affectedRows();
```
### Get the total number of queries:
```php
echo $db->query_count;
```
### Get the last insert ID:
```php
echo $db->lastInsertID();
```
### Close the database:
```php
$db->close();
```
