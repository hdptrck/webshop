<?php

if ($_SESSION['userRole'] != 1) {
    header('Location: index.php');
    die();
}