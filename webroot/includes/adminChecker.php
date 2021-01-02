<?php
// Check if user is an Administrator
if ($_SESSION['userRole'] != 2 && $_SESSION['userRole'] != 3) {
    header('Location: index.php');
    die();
}
