<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/class/Response.php");

$response = new Response();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET["orderId"]) && !empty(trim($_GET["orderId"])) && isset($_GET["orderAction"]) && !empty(trim($_GET["orderAction"]))) {

        $id = preg_replace('#[^0-9]#i', "", $_GET['orderId']);

        if ($_GET["orderAction"] == "isReady") {
            $query = "UPDATE tbl_order SET isReady = 1 WHERE idOrder = ?;";
            $updatestmt = $mysqli->prepare($query);
            $updatestmt->bind_param("i", $id);
            $updatestmt->execute();

            if ($updatestmt->affected_rows) {
                $response->code = 200;
            }
        } else if ($_GET["orderAction"] == "isReturned") {
            $query = "UPDATE tbl_order SET isReturned = 1 WHERE idOrder = ?;";
            $updatestmt = $mysqli->prepare($query);
            $updatestmt->bind_param("i", $id);
            $updatestmt->execute();

            if ($updatestmt->affected_rows) {
                $response->code = 200;
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
