<?php
require("includes/autoLoad.php");

// Session temporarly deactivated for development
require("includes/sessionChecker.php");

$orders = [];

// Get all Items from Database
$stmt = "SELECT * FROM tbl_order;";
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

        <div class="col-12">
            <div class="card" data-ripple-color="light">
                <div class="card-body">
                    <?php echo '<a href="orderDetail.php?id=' . $order['idOrder'] . '"><h5 class="card-title">' . $order['eventName'] . '</h5></a>' ?>

                    <?php //echo '<p class="card-text">' . $item['description'] . '</p>' 
                    ?>
                    <?php echo '<p>Abholdatum: ' . $order['pickUpDatetime'] . '<br>' ?>
                    <?php echo 'Zurückbringdatum: ' . $order['returnDatetime'] . '</p>' ?>
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