<?php
require_once 'tracking.php';

doLogout($dbh);

header('Location: index.php');