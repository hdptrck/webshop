<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");

$orders = [];
// Get all Items from Database

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
                    echo '<h5 class="card-title">' . $order['eventName'] . '</h5>';
                    echo '<p class="card-text">' . $order['email'] . '</p>';
                    echo '<p>Abholdatum: ' . $order['pickUpDatetime'] . '<br>';
                    echo 'Zurückbringdatum: ' . $order['returnDatetime'] . '</p>';
                    echo '<button class="btn btn-info mb-3">Details anzeigen</button>';
                    echo '<div>
                     
                        </div>';

                    echo '<button class="order-event btn btn-primary btn-block" data-order-id="' . $order['idOrder'] . '" data-order-action="';
                    if ($order['isReady'] == 0) {
                        echo 'isReady">Bestellung bereitgestellen</button>';
                    } else {
                        echo 'isReturned">Bestellung zurückgegeben</button>';
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
<script>
    const orderActionItemList = document.querySelectorAll('.order-event');

    const orderAction = event => {
        const clickedItem = event.target;
        fetch('adminOrderHandler.php?orderId=' + clickedItem.dataset.orderId + '&orderAction=' + clickedItem.dataset.orderAction)
            .then(res => {
                return res.json();
            }).then(res => {
                if (res.code != 200) {
                    alert(res.description);
                } else {
                    location.reload();
                }
            });
    };

    orderActionItemList.forEach(item => {
        item.addEventListener('click', orderAction);
    });
</script>
</body>

</html>