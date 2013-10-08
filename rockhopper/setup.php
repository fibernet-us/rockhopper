<?php

/**
 * ****************************************************************************
 *
 * setup.php
 *
 * Rockhopper set up script. Created database schema and add default data.
 *
 * @author   Wen Bian
 * @version  1.01
 * @history
 *   09/01/2013: added DB creation, table creation and table info display
 *   09/10/2013: changed DB access from mysqli to PDO
 *   09/12/2013: cleaned up (removed unused code)
 *   09/16/2013: added user admin creation code
 *
 */

require_once 'connect.php';
require_once 'user.php';

$dbfile = '../../inc/rockhopper.sql';

// Create database, remove it first if it exists
$sql = "DROP DATABASE IF EXISTS $database";
$dbh->query($sql);
//
$sql = "CREATE DATABASE $database";
if($dbh->query($sql)){
    echo "Database $database created successfully. <br><br>";
} 
else {
    exit('Error: could not create the database.');
}

// Select DB
$sql = "USE $database";
if(! $dbh->query($sql)){
    exit('Error: could not select the database');
}

// Create tables from the DB schema file
$fh = fopen($dbfile, 'rb');
if($fh){
    while(! feof($fh)){
        $buffer = stream_get_line($fh, 1000000, ";\n");
        if(! $dbh->query($buffer)){
            // echo $dbh->error . $buffer . "<br><br>";
        }
    }
} 
else {
    exit("can't open sql file.");
}

// Add a default user admin
if(User::addUser($dbh, 'admin', 'rockhopper admin', 'rockhopper', 'admin@gmail.com', 0, User::TYPE_RH_ADMIN)) {
    echo "<font color=blue>A default admin account is created (username: admin, password: rockhopper)</font><br>";
    echo "<font color=red>Click <a href=index.php target=_blank>here</a> to login and change the password and email.</font><br><br>";
}

// Check tables
echo "The following tables have been created: <br><br>";
$sql = "SHOW TABLES";
$stmt = $dbh->query($sql);
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
//var_dump($tables);

// Print out each table's schema
foreach($tables as $table){
    $sql = "DESCRIBE $table";
    if($stmt = $dbh->query($sql)){
        echo "Table $table:<br>";
        echo "<table>";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo "<tr><td width=200>" . $row ['Field'] . "</td><td>" . $row ['Type'] . "</td></tr>";
        }
        echo "</table><br>";
    }
}
		
// TODO: Print out each table's default/init data


// Close the connection
$dbh = null;

?> 
