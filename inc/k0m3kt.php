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
 *   09/12/2013: added a few echos
 */

$host = 'localhost';
$user = 'username';
$pass = 'password';
$database = 'rockhopper';
$dsn = 'mysql:host=' . $host;
$dsd = 'mysql:host=' . $host . ';dbname=' . $database;
        
try {
    // first try to connect to our DB
    $dbh = new PDO($dsd, $user, $pass);
    //echo '<p>connected to rockhopper.</p>';
} 
catch (PDOException $e) {
    try {
        // if our DB does not exist connect anyway 
        $dbh = new PDO($dsn, $user, $pass);
        //echo '<p>connected to localhost.<p>';
    }
    catch (PDOException $e) {
        echo 'DB Connection failed: ' . $e->getMessage();
        exit();
    }
}
