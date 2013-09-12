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
 *
 *
 */

include 'k0m3kt.php';

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
$fh = fopen('rockhopper.sql', 'rb');
if($fh){
    while(! feof($fh)){
        $buffer = stream_get_line($fh, 1000000, ";\n");
        if(! $dbh->query($buffer)){
            // echo $dbh->error . $buffer . "<br><br>";
        }
    }
} 
else {
    exit("can't opne sql file.");
}


// TODO: Insert default data into tables


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
            echo "<tr><td>" . $row ['Field'] . "</td><td>" . $row ['Type'] . "</td></tr>";
        }
        echo "</table><br>";
    }
}

// Print out each table's data
foreach($tables as $table){
    $sql = "SELECT * FROM $table";
    $result = $sth->fetchAll(PDO::FETCH_CLASS, "fruit");
    
    if($stmt = $dbh->query($sql)){
        echo "Table $table:<br>";
        echo "<table>";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo "<tr><td>" . $row ['Field'] . "</td><td>" . $row ['Type'] . "</td></tr>";
        }
        echo "</table><br>";
    }
}

// Close the connection
$dbh = null;

?> 
