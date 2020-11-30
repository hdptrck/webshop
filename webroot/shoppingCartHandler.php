<?php
//require("./includes/autoLoad.php");
require("includes/sessionChecker.php");
?>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["remove"])) {
        if ((isset($_SESSION["shoppingCart"]) and is_array($_SESSION["shoppingCart"]) && isset($_POST["id"]))) {
            $shoppingCart = $_SESSION["shoppingCart"];
            foreach ($shoppingCart as $item) {
                if ($item["id"] == $_POST["id"]) {
                    if (($key = array_search($item, $shoppingCart)) !== false) {
                        unset($shoppingCart[$key]);
                    }
                    $_SESSION["shoppingCart"] = $shoppingCart;
                    break;
                }
            }
        }
    } else if (isset($_POST["changeCount"])) {
        if ((isset($_SESSION["shoppingCart"]) and is_array($_SESSION["shoppingCart"]) && isset($_POST["id"]))) {
            $shoppingCart = $_SESSION["shoppingCart"];
            foreach ($shoppingCart as &$item) {
                if ($item["id"] == $_POST["id"]) {
                    if (isset($_POST["count"])) {
                        $item["count"] = $_POST["count"];
                        
            echo var_dump($shoppingCart);
                    }
                    unset($_SESSION["shoppingCart"]);
                    $_SESSION["shoppingCart"] = $shoppingCart;
                    break;
                }
            }
        }
    } else if (isset($_POST["changeTime"])) {
        echo "tschauaa";
    }
}
