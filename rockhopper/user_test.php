<?php

require_once 'connect.php';
require_once 'user.php';

echo '<html>';
echo '<body>';

/*
 * Class User
 * 
 * public functions
 * ================
 * 
 * getId()  
 * getUsername()        
 * getFullname() 
 * getPassword()  
 * getEmail()  
 * getType()      
 * getStatus()    
 * getTimezone()   
 * getLocation()    
 * getIconUrl()     
 * getEnabled()  
 * getTypeString() 
 * getStatusString()
 * 
 * setDbHandle($dbh)   
 * setUsername($u)      
 * setFullname($f)      
 * setPassword($p)       
 * setEmail($e)          
 * setType($t)           
 * setStatus($s)         
 * setTimezone($z)       
 * setLocation($l)       
 * setIconUrl($i)        
 * setEnabled($b)        
 * 
 * 
 * public static functions
 * =======================
 * isUsernameRegistered($dbh, $username)
 * isEmailRegistered($dbh, $email) 
 * getUser($dbh, $username, $password) 
 * getUserByEmail($dbh, $email, $password) 
 * getAllUsers($dbh)
 * addUser($dbh, $username, $fullname, $password, $email, $timezone, $type, $status, $location, $iconurl) 
 * removeUser($dbh, $username) 
 * removeAllUsers($dbh)
 * 
*/

function printUserTable() {
	global $dbh;
	$users = User::getAllUsers($dbh);
	//var_dump($users);
	foreach($users as $user) {
		echo "<p>" . $user . "</p>";
	}
}

echo "<p><b>User table in the begining:</b></p>";
printUserTable();

//echo "<p><b>User table after truncation (should be empty):</b></p>";
//User::removeAllUsers($dbh);
//printUserTable();

echo "<p><b>User table after adding user 1:</b></p>";
User::addUser($dbh, "user1", "user one", "pass1", "user1@mail.com", -6);
printUserTable();

echo "<p><b>User table after adding user 2 and 3:</b></p>";
User::addUser($dbh, "user2", "user two", "pass2", "user2@mail.com", 0);
User::addUser($dbh, "user3", "user san", "pass3", "user3@mail.com", 8, 0, 0, "Beijing", "img/user3.jpg");
printUserTable();

echo "<p><b>Get 'user1' by username:</b></p>";
$username = 'user1';
$password = 'pass1';
$user = User::getUser($dbh, $username, $password);
echo "<p>" . $user . "</p>";

echo "<p><b>Get 'user1' by email 'user1@mail.com':</b></p>";
$email = 'user1@mail.com';
$password = 'pass1';
$user = User::getUserByEmail($dbh, $email, $password);
echo "<p>" . $user . "</p>";

echo "<p><b>User 1 after full name and type change:</b></p>";
$user->setFullname("user uno");
$user->setType(User::TYPE_RH_ADMIN);
$user->setStatus(User::STATUS_WORKING);
echo "<p>" . $user;

echo "<p><b>User table after user2 removed:</b></p>";
User::removeUser($dbh, 'user2');
printUserTable();

echo "<p><b>Check if username \"user1\" is registered:</b></p>";
if(User::isUsernameRegistered($dbh, "user1"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";

echo "<p><b>Check if username \"user2\" is registered:</b></p>";
if(User::isUsernameRegistered($dbh, "user2"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";

echo "<p><b>Check if username \"user3\" is registered:</b></p>";
if(User::isUsernameRegistered($dbh, "user3"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";

echo "<p><b>Add new user with username \"user3\":</b></p>";
if(User::addUser($dbh, "user3", "user t", "pass9", "usert@mail.com", 0))
	echo "<p>Successful</p>";
else
	echo "<p>Failed</p>";

echo "<p><b>Check if email \"user1@mail.com\" is registered: </b></p>";
if(User::isEmailRegistered($dbh, "user1@mail.com"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";

echo "<p><b>Check if email \"user2@mail.com\" is registered: </b></p>";
if(User::isEmailRegistered($dbh, "user2@mail.com"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";


echo '</body>';
echo '</html>';


