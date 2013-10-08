<?php

require_once 'connect.php';

function string_enscript($string) {
    $salt = sosdldsldsldsksdklds;
    $string = md5($salt, $string);
}

function string_random($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for($i = 0; $i < $length; $i ++) {
        $randomString .= $characters [rand(0, strlen($characters)- 1)];
    }
    return $randomString;
}

function safe_var($var) {
    global $dbh;
    $var = stripslashes($var);
    $var = trim($var);
    $var = strip_tags($var);
    //$var = $dbh->quote($var);
    $var = htmlspecialchars($var);
    $var = nl2br($var);

    return $var;
}

// TODO
function getUserTimezone() {
    return 0;
}

