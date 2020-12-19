<?php
require("./includes/autoLoad.php");
require("./includes/sessionChecker.php");
require("./includes/itemChecker.inc.php");
?>

<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") { // Should only be called as post
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'changeOrderInfos':
                if (isset($_POST['eventName']) && isset($_POST['eventPlace']) && isset($_POST['orderLocation'])) {
                    // Store all basic informations in session
                    $orderInfos = array('eventName' => $_POST['eventName'], 'eventPlace' => $_POST['eventPlace'], 'orderLocation' => $_POST['orderLocation']);
                    if (isset($_SESSION['orderInfos'])) {
                        unset($_SESSION['orderInfos']);
                    }
                    $_SESSION['orderInfos'] = $orderInfos;
                }
            case 'remove':
                if ((isset($_SESSION['shoppingCart']) and is_array($_SESSION['shoppingCart']) && isset($_POST['id']))) {
                    $shoppingCart = $_SESSION['shoppingCart'];
                    foreach ($shoppingCart as $item) {
                        if ($item['id'] == $_POST['id']) { // Search item which shall be removed
                            if (($key = array_search($item, $shoppingCart)) !== false) {
                                unset($shoppingCart[$key]); // Remove item from session
                            }

                            header("Content-type: application/json");
                            echo json_encode($shoppingCart); // Send updated cart as feedback

                            $_SESSION['shoppingCart'] = $shoppingCart;
                            break;
                        }
                    }
                }
            case 'changeCount':
                if ((isset($_SESSION['shoppingCart']) and is_array($_SESSION['shoppingCart']) && isset($_POST['id']))) {
                    $shoppingCart = $_SESSION['shoppingCart'];
                    foreach ($shoppingCart as &$item) {
                        if ($item['id'] == $_POST['id']) { // Search for item which has a new count
                            if (isset($_POST['count'])) {
                                $item['count'] = $_POST['count']; // Update count
                            }
                            header("Content-type: application/json");
                            echo json_encode($shoppingCart); // Send updated cart as feedback 
                            unset($_SESSION['shoppingCart']);
                            $_SESSION['shoppingCart'] = $shoppingCart;
                            break;
                        }
                    }
                }
            case 'changeTime':
                if (isset($_POST['startDate']) && isset($_POST['startTime']) && isset($_POST['endDate']) && isset($_POST['startTime'])) {
                    $startDateTime = DateTime::createFromFormat("Y-m-d H:i", $_POST['startDate'] . " " . $_POST['startTime']);
                    $endDateTime = DateTime::createFromFormat("Y-m-d H:i", $_POST['endDate'] . " " . $_POST['endTime']);
                    $timeSpan = array('start' => $startDateTime->format("d.m.Y H:i"), 'end' => $endDateTime->format("d.m.Y H:i"));
                    if (isset($_SESSION['timeSpan'])) {
                        unset($_SESSION['timeSpan']);
                    }
                    $_SESSION['timeSpan'] = $timeSpan; // Store updated timespan

                    if (isset($_SESSION['shoppingCart']) and is_array($_SESSION['shoppingCart'])) {
                        $startStr = $startDateTime->format("Y-m-d H:i");
                        $endStr = $endDateTime->format("Y-m-d H:i");
                        $shoppingCart = $_SESSION['shoppingCart'];

                        $feedback = checkItemsInTimeSpan($mysqli, $startStr, $endStr, $shoppingCart); // Check items in new timespan. Function in itemChecker.inc.php

                        header("Content-type: application/json"); // Send feedback
                        echo json_encode($feedback);
                    }
                }
        }
    }
} else {
    header("Location: shoppingCart.php");
}
