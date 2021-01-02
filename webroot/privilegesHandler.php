<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/rootChecker.php");
require("includes/class/Response.php");

$response = new Response();
$response->description = "Die Rolle des Benutzers konnte nicht angepasst werden";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["userId"]) && !empty(trim($_GET["userId"])) && isset($_GET["role"]) && !empty(trim($_GET["role"]))) {
        $id = preg_replace('#[^0-9]#i', "", $_GET['userId']);
        $role = preg_replace('#[^0-9]#i', "", $_GET['role']);

        if ($id == $_SESSION['userId']) {
            $response->code = 501;
            $response->description = "Sie können nicht die Rolle des angemeldeten Benutzers ändern";
        } else {
            $query = "UPDATE webshopuser SET role_idRole = ? WHERE idWebShopUser = ?;";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ii", $id, $role);
            $stmt->execute();

            if ($stmt->affected_rows) {
                $response->code = 200;
                $response->description = "Die Rolle des Benutzer wurde erfolgreich angepasst";
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
