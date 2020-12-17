<?php
require("./includes/autoLoad.php");
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

                    header('Content-type: application/json');
                    echo json_encode($shoppingCart);

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

                        header('Content-type: application/json');
                        echo json_encode($shoppingCart);
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

            if (isset($_SESSION["shoppingCart"]) and is_array($_SESSION["shoppingCart"])) {
                $startStr = $startDateTime->format('Y-m-d H:i');
                $endStr = $endDateTime->format('Y-m-d H:i');
                //Create JOIN Statement: SELECT order_has_item.quantity, order_has_item.item_idItem FROM order_has_item INNER JOIN order ON order_has_item.order_idOrder=order.idOrder AND order.pickUpDatetime<=? AND order.returnDatetime>=?;
                $query = "SELECT order_has_item.quantity, item.idItem, item.count FROM order_has_item INNER JOIN tbl_order ON order_has_item.order_idOrder=tbl_order.idOrder RIGHT JOIN item ON order_has_item.item_idItem=item.idItem AND tbl_order.pickUpDatetime<=? AND tbl_order.returnDatetime>=?;";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ss", $endStr, $startStr);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                $shoppingCart = $_SESSION["shoppingCart"];
                $feedback = array();

                foreach ($shoppingCart as $item) {
                    $id = $item["id"];
                    foreach ($result as $order) {
                        if ($order["idItem"] == $id) {
                            if (!isset($max)) {
                                $max = $order["count"];
                            }
                            if (isset($order["quantity"])) {
                                $max -= $order["quantity"];
                            }
                        }
                    }
                    $feedback[] = array("id" => $id, "max" => $max);
                }

                header('Content-type: application/json');
                echo json_encode($feedback);
            }
        }
    }
}
