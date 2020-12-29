<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/adminChecker.php");
require("includes/class/Response.php");

$response = new Response();
$response->description = "Ein Fehler ist aufgetreten, das Produkt konnte nicht gelöscht werden.";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = preg_replace('#[^0-9]#i', "", $_GET['id']);
        $query = "UPDATE item SET isActive = 0 WHERE idItem = ?;";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows) {
            $response->code = 200;
            $response->description = "Das Produkt wurde erfolgreich gelöscht.";
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);