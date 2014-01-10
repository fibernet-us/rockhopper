<?php

/**
 * ****************************************************************************
 *
 * check_available.php 
 * 
 * Used by index.php to check whether a username or email has been taken.
 * 
 *                               
 */
 
 require_once 'connect.php';
 require_once 'user.php';
 
 $checkuser = $_GET["checkuser"];
 $nametype = $_GET["nametype"];
 
 if ($nametype == "isname") {
	 $checkresult = User::isUsernameRegistered($dbh, $checkuser);
	 if ($checkresult == false) echo 0;
	 else echo 1;
 }

 
 else if ($nametype == "isemail") {
	 $checkresult = User::isEmailRegistered($dbh, $checkuser);
	 if ($checkresult == false) echo 2;
	 else echo 3;
 }
 