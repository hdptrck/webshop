<?php
require("../../pw.inc.php");
// Variabeln deklarieren
$host = 'localhost'; // host
$username = 'webShopBackend'; // username
$password = $pw_database; // password is stored in pw.inc.php
$database = 'webshop'; // database

// mit der Datenbank verbinden
$mysqli = new mysqli($host, $username, $password, $database);

// Fehlermeldung, falls Verbindung fehl schlägt.
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
