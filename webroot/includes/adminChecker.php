<?php
// Check if user is an Administrator
if ($_SESSION['userRole'] != 1) {
    header('Location: index.php');
    die();
}
