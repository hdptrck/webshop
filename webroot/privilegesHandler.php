<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/rootChecker.php");
require("includes/class/Response.php");

$response = new Response();

// Check request method
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["userId"]) && !empty(trim($_GET["userId"]))) {
        $id = preg_replace('#[^0-9]#i', "", $_GET['userId']);

        // Update role
        if (isset($_GET["role"]) && !empty(trim($_GET["role"]))) {
            $response->description = "Die Rolle des Benutzers konnte nicht angepasst werden";
            $role = preg_replace('#[^0-9]#i', "", $_GET['role']);

            // Disable changes on the logged in user
            if ($id == $_SESSION['userId']) {
                $response->code = 501;
                $response->description = "Sie können nicht die Rolle des angemeldeten Benutzers ändern";
            } else {
                // Update role
                $query = "UPDATE webshopuser SET `role_idRole` = ? WHERE `idWebShopUser` = ?;";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ii", $role, $id);
                $stmt->execute();

                if ($stmt->affected_rows) {
                    $response->code = 200;
                    $response->description = "Die Rolle des Benutzer wurde erfolgreich angepasst";
                }
            }

            // Update Active state
        } elseif (isset($_GET["active"]) && !empty(trim($_GET["active"]))) {
            $response->description = "Der Benutzer konnte nicht angepasst werden";
            $active = preg_replace('#[^0-9]#i', "", $_GET['active']);
            
            // Disable changes on the logged in user
            if ($id == $_SESSION['userId']) {
                $response->code = 501;
                $response->description = "Es können keine Änderungen am angemeldeten Benutzers vorgenommen werden";
            } else {
                // Update active 1 = active, 2 = disabled
                $query = "UPDATE webshopuser SET `active` = ? WHERE `idWebShopUser` = ?;";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ii", $active, $id);
                $stmt->execute();

                if ($stmt->affected_rows) {
                    $response->code = 200;
                    $response->description = "Der Benutzer wurde erfolgreich angepasst";
                }
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
