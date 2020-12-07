<?php
session_start();
$_SESSION = array();

if (isset($_COOKIE['rememberme'])) {
    unset($_COOKIE['rememberme']); 
    setcookie('rememberme', null, -1, '/');
}

session_destroy();
header('Location: login.php');
