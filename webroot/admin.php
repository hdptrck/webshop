<?php
require("includes/autoLoad.php");

// Session temporarly deactivated for development
require("includes/sessionChecker.php");

$orders = [];
// Get all Items from Database

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = preg_replace('#[^0-9]#i', "", $_GET['id']);
    
    if (isset($_GET["isReady"]) && !empty(trim($_GET["isReady"])) && $_GET["isReady"] == true) {
        $query = "UPDATE tbl_order SET isReady = 1 WHERE idOrder = ?;";
        $updatestmt = $mysqli->prepare($query);
        $updatestmt->bind_param("i", $id);
        $updatestmt->execute();

        if (!$updatestmt->affected_rows) {
            header('Location: /404.html');
            die();
        }
    } else if (isset($_GET["isReturned"]) && !empty(trim($_GET["isReturned"])) && $_GET["isReturned"] == true) {
        $query = "UPDATE tbl_order SET isReturned = 1 WHERE idOrder = ?;";
        $updatestmt = $mysqli->prepare($query);
        $updatestmt->bind_param("i", $id);
        $updatestmt->execute();

        if (!$updatestmt->affected_rows) {
            header('Location: /404.html');
            die();
        }
    }
}


$stmt = "SELECT * FROM tbl_order 
            LEFT JOIN webshopuser ON tbl_order.webShopUser_idWebShopUser = webShopUser.idWebshopUser
            LEFT JOIN orderlocation ON tbl_order.orderLocation_idOrderLocation = orderlocation.idOrderLocation
            WHERE tbl_order.isReturned <> 1;";
if (!$result = $mysqli->query($stmt)) {
    echo "Oops! Something went wrong. Please try again later.";
    return false;
}

// Fetch result
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}


$siteName = "Bestellungen";
include("./includes/header.inc.php");

?>
<div class="row fadeIn">

    <?php
    foreach ($orders as $order) {
    ?>

        <div class="col-12 mb-2">
            <div class="card" data-ripple-color="light">
                <div class="card-body">
                    <?php
                    echo '<a href="orderDetail.php?id=' . $order['idOrder'] . '"><h5 class="card-title">' . $order['eventName'] . '</h5></a>';
                    echo '<p class="card-text">' . $order['email'] . '</p>';
                    echo '<p>Abholdatum: ' . $order['pickUpDatetime'] . '<br>';
                    echo 'Zurückbringdatum: ' . $order['returnDatetime'] . '</p>';
                    echo '<a href="admin.php?id=' . $order['idOrder'] . '&';
                    if ($order['isReady'] == 0) {
                        echo 'isReady=true" class="btn btn-primary btn-block">Bestellung bereitgestellen</a>';
                    } else {
                        echo 'isReturned=true" class="btn btn-primary btn-block">Bestellung zurückgegeben</a>';
                    }
                    ?>
                </div>
            </div>
        </div>

    <?php
    }
    ?>

</div>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>

</body>

</html>