<?php

$server   = 'localhost';
$database = 'rockhopper';
$username = 'username';
$password = 'password';

$mysqli = new mysqli( $server, $username, $password);

// check connection 
if(mysqli_connect_errno()) {
    printf( "Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/* turn autocommit on */
$mysqli->autocommit(TRUE);

$query = "DROP DATABASE IF EXISTS $database";
$mysqli->query($query);

// Create database
$query = "CREATE DATABASE $database";
if($mysqli->query($query)) {
    echo "Database $database created successfully. <br><br>";
} 
else {
    exit('Error: could not create the database.');
}

if(!$mysqli->select_db($database)) {
    exit('Error: could not select the database');
}

// Create tables
$handle = fopen('rockhopper.sql', 'rb');
if($handle) {
    while(!feof($handle)) {
        $buffer = stream_get_line($handle, 1000000, ";\n");
        if(!$mysqli->query($buffer)) {
            //echo  $mysqli->error . $buffer . "<br><br>"; 
        }
    }
}
else {
    exit("can't opne sql file.");
}

// Check tables
echo "The following tables have been created: <br><br>";
$tables = array();
$query = "SHOW TABLES";
$result = $mysqli->query($query);
while($row = $result->fetch_row()){
    $tables[] = $row[0];
}

foreach($tables as $table) {

    $query = "DESCRIBE $table";
    if($result = $mysqli->query($query)) {
        echo "Table $table:<br>";
        echo "<table>";
    
        while($row = $result->fetch_assoc()) {
             echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>"; 
        }
    
        echo "</table><br>";
        $result->free();
    }
}

?> 
