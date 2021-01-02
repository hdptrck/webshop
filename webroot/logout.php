<?php
session_start();
$_SESSION = array();

// Destroy cookie
if (isset($_COOKIE['rememberme'])) {
    unset($_COOKIE['rememberme']); 
    setcookie('rememberme', null, -1, '/');
}

// Destroy session
session_destroy();
header("Location: login.php?reason=logoutsuccessful");
