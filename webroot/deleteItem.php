<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/adminChecker.php");
require("includes/class/Response.php");

// Create response
$response = new Response();
$response->description = "Ein Fehler ist aufgetreten, das Produkt konnte nicht gelöscht werden.";

// Check GET parameters
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = preg_replace('#[^0-9]#i', "", $_GET['id']);
        // No delete because the item could be in existing orders
        $query = "UPDATE item SET isActive = 0 WHERE idItem = ?;";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Check if product update was successful
        if ($stmt->affected_rows) {
            $response->code = 200;
            $response->description = "Das Produkt wurde erfolgreich gelöscht.";
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);