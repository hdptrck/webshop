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
        if (isset($_POST["startDate"]) && isset($_POST["startTime"]) && isset($_POST["endDate"]) && isset($_POST["startTime"])) {
            $startDateTime = DateTime::createFromFormat("Y-m-d H:i", $_POST["startDate"] . " " . $_POST["startTime"]);
            $endDateTime = DateTime::createFromFormat("Y-m-d H:i", $_POST["endDate"] . " " . $_POST["endTime"]);
            $timeSpan = array("start" => $startDateTime->format('d.m.Y H:i'), "end" => $endDateTime->format('d.m.Y H:i'));
            if (isset($_SESSION["timeSpan"])) {
                unset($_SESSION["timeSpan"]);
            }
            $_SESSION["timeSpan"] = $timeSpan;

            //Create JOIN Statement
            $query = "SELECT * FROM item WHERE idItem=?;";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $item["id"]);
            $stmt->execute();
            $result = $stmt->get_result();
        }
    }
}
