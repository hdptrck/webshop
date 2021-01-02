<?php
require("../pw.inc.php");
// Set Var
$host = 'localhost'; // host
$username = 'webShopBackend'; // username
$password = $pw_database; // password is stored in pw.inc.php
$database = 'webshop'; // database

// Connect to the database
$mysqli = new mysqli($host, $username, $password, $database);

// Error if there is a problem with the connection
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
