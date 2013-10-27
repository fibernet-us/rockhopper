<?php

 //mysql_connect("localhost","rockhopper","");
 //mysql_select_db("rockhopper");
 
 require_once 'connect.php';
 
 $checkuser = $_GET["checkuser"];
 $nametype = $_GET["nametype"];
 
 if ($nametype == "isname") {
	 $sql = "SELECT * FROM  RH_USER where username='$checkuser'";
	 $stmt = $dbh->prepare($sql);
	 $stmt->execute();
	 if ($stmt->rowCount() == 0) echo 0;
	 else echo 1;
 }
 
 else if ($nametype == "isemail") {
	 $sql = "SELECT * from RH_USER where email='$checkuser'";
	 $stmt = $dbh->prepare($sql);
	 $stmt->execute();
	 if ($stmt->rowCount() == 0) echo 2;
	 else echo 3;
 }
 