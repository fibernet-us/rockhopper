<?php
error_reporting(E_ALL ^ E_NOTICE);



require_once '../../inc/k0m3kt.php';
require_once 'user.php';
//require_once 'test.php';

session_name('rhLogin');
// Starting the session

session_set_cookie_params(2*7*24*60*60);
// Making the cookie live for 2 weeks

session_start();

if($_SESSION['id'] && !isset($_COOKIE['rhRemember']) && !$_SESSION['rememberMe'])
{
	// If you are logged in, but you don't have the tzRemember cookie (browser restart)
	// and you have not checked the rememberMe checkbox:

	$_SESSION = array();
	session_destroy();
	
	// Destroy the session
}


if($_POST['submit']=='Login')
{

	$err = array();
	// Will hold our errors
	
	
	if(!$_POST['username'] || !$_POST['password'])
		$err[] = 'All the fields must be filled in!';
	
	if(!count($err))
	{
	$_POST['username'] = mysql_real_escape_string($_POST['username']);
	$_POST['password'] = mysql_real_escape_string($_POST['password']);
	$_POST['rememberMe'] = (int)$_POST['rememberMe'];
    
	$sql = "USE rockhopper; ";
    $dbh->query($sql);
    $sql2="SELECT username, passhash,salt FROM RH_USER WHERE username='{$_POST['username']}'";
    
     foreach ($dbh->query($sql2) as $row) {
     	$row['passhash'];
     	$row['salt'];
     }
    
    $verify=Passcrypt::verifyPassword($_POST['password'],$row['passhash'],$row['salt']);

		
		if($verify)
		{
			$_SESSION['rememberMe'] = $_POST['rememberMe'];
			
			// Store some data in the session
			
			setcookie('rhRemember',$_POST['rememberMe']);
		}
		else $err[]='Wrong username and/or password!';
	}
	
	
	if($err){
	$_SESSION['msg']['login-err'] = implode('<br />',$err);
	// Save the error messages in the session
	header("Location: index.php");
	}
	//proceed to the project main page
	else
	header("Location: project_main.html");
	exit;
}


else if($_POST['submit']=='Create Account')
{
	

	$err = array();
	
	if(!$_POST['username']||!$_POST['pwd']||!$_POST['cpwd']||!$_POST['email']||!$_POST['name']){
			$err[]='All the fields must be filled in!';		
	}
	
	if(strlen($_POST['username'])<4 || strlen($_POST['username'])>32)
	{
		$err[]='Your username must be between 3 and 32 characters!';
	}	
	
	if(preg_match('/[^a-zA-z<\br>]+/i',$_POST['name']))
	{
		$err[]='Your name contains invalid characters!';
	}
	
		
	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['username']))
	{
		$err[]='Your username contains invalid characters!';
	}
	
	if(strlen($_POST['pwd'])<6 || strlen($_POST['pwd'])>15)
	{
		$err[]='Your username must be between 6 and 15 characters!';
	}
	
	if($_POST['pwd']!=$_POST['cpwd'])
	{
		$err[]='Your password does not match!';
	};	
	
	if(user::isUsernameRegistered($dbh, $_POST['username']))
	{
		$err[]='Username has already been registered!';
	}
	
	if(user::isEmailRegistered($dbh, $_POST['email']))
	{
		$err[]='Email has already been registered!';
	}
	
	//add users if the input format is right
	if(!count($err)){
		$_POST['name'] = mysql_real_escape_string($_POST['name']);
		$_POST['username'] = mysql_real_escape_string($_POST['username']);
		$_POST['email'] = mysql_real_escape_string($_POST['email']);
		$_POST['pwd'] = mysql_real_escape_string($_POST['pwd']);
		
		User:: addUser($dbh,$_POST['name'],$_POST['username'],$_POST['pwd'],$_POST['email'],-6 );
		
	}
	
	

	if(count($err))
	{
		$_SESSION['msg']['reg-err'] = implode('<br />',$err);
	}	
	
	header("Location: index.php#create_account");
	exit;
}


?>
