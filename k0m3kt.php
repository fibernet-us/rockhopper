<?php

/**
 * ****************************************************************************
 *
 * connect.php
 * k0m 3kt
 *
 * Connect to database via PDO
 *
 * @author   Wen Bian
 * @version  1.00
 * @history
 *   09/10/2013: created
 *
 */

$host = 'localhost';
$user = 'username';
$pass = 'password';
$database = 'rockhopper';
$dsn = 'mysql:host=' . $localhost;
$dsd = 'mysql:host=' . $localhost . 'dbname=' . $database;
        
try {
    // first try to connect to our DB
    $dbh = new PDO($dsd, $user, $pass);
} 
catch (PDOException $e) {
    try {
        // if our DB does not exist connect anyway 
        $dbh = new PDO($dsn, $user, $pass);
    }
    catch (PDOException $e) {
        echo 'DB Connection failed: ' . $e->getMessage();
        exit();
    }
}
