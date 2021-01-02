<?php
// Check if user is an Administrator
if ($_SESSION['userRole'] != 2) {
    header('Location: index.php');
    die();
}
