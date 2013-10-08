<?php

error_reporting(E_ALL ^ E_NOTICE);

require_once 'connect.php';
require_once 'user.php';
require_once 'utility.php';

session_set_cookie_params(2 * 7 * 24 * 60 * 60); // 2 weeks
session_start();

function doLogin($dbh, $login, $password, $rememberMe = false) {
    if(strstr($login, '@')) {
        $curUser = User::getUserByEmail($dbh, $login, $password);
    }
    else{
        $curUser = User::getUser($dbh, $login, $password);
    }
    
    if($curUser) {
        loginMemo($curUser, $rememberMe);
        header("Location: showprofile.php");
    } 
    else {
        $_SESSION['msg']['login-err'] = 'Wrong Username / Email / Password!';
        header("Location: index.php?id=login");
    }
    exit;
}


function doRegister($dbh, $username, $name, $pwd, $email) {
    if(User::addUser($dbh, $username, $name, $pwd, $email, getUserTimezone())) {
        $_SESSION['msg']['reg-success'] = 'Account created. You can log in now.';
        header("Location: index.php?id=login");        
    }
    else {
        $_SESSION['msg']['reg-err'] = 'Account creation failed.';
        header("Location: index.php?id=create_account");
    }
    exit; 
}

function doLogout($dbh) {
    $username = $_SESSION['username'];
    setcookie("auth_key", "", time() - 3600);
    
    // delete auth key from database so cookie can no longer be used
    if(User::removeUserAuth($dbh, $username)) {
        unset($_SESSION['username']);
        unset($_SESSION['lastactive']);
        session_unset();
        session_destroy();
        header("Location: index.php");
    }
}

// Check if session is still active and if it keep it alive
function keepSession() {
    if(! isset($_COOKIE['auth_key'])) {
        $oldtime = $_SESSION['lastactive'];

        if(! empty($oldtime)) {
            $currenttime = time();
            $timeoutlength = 30 * 600;
            
            if($oldtime + $timeoutlength >= $currenttime) {
                $_SESSION['lastactive'] = $currenttime;
            }
            else {
                doLogout(); // If session has been inactive too long logout
            }
        }
    }
}

function doAutoLogin($dbh) {
    if(isset($_SESSION['username'])) {
        $username = safe_var($_SESSION['username']);
        $curUser = User::getUserByUsername($dbh, $username);
        if($curUser) {
            loginMemo($curUser, true);
        }
        return $curUser;
    }
    else if(isset($_COOKIE['auth_key'])) {
        $auth_key = safe_var($_COOKIE['auth_key']);
        echo $_COOKIE['auth_key'];
        $curUser = User::getUserByAuth($dbh, $auth_key);
        if($curUser) {
            loginMemo($curUser, true);
        }
        else {
            setcookie("auth_key", "", time() - 3600); // invalid auth
        }
        return $curUser;
    }
    else{
        return false;
    }
}


function loginMemo($curUser, $rememberMe) {
    if($rememberMe) {
        // Generate new auth key for each log in
        $cookie_auth = string_random() . $username;
        $auth_key = string_enscript($cookie_auth);
        $curUser->setAuth($auth_key);
        setcookie("auth_key", $auth_key);
    }

    // Assign variables to session
    session_regenerate_id(true);
    $_SESSION['username'] = $curUser->getUsername();
    $_SESSION['lastactive'] = time();
}

?>
