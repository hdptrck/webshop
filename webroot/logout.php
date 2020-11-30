<?php
session_start();

// Unset all of the session variables.
$_SESSION = array();

if (isset($_COOKIE['rememberme'])) {
    unset($_COOKIE['rememberme']); 
    setcookie('rememberme', null, -1, '/');
}

// Finally, destroy the session.
session_destroy();

header('Location: login.php');
