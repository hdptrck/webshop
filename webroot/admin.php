<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/adminChecker.php");

// Declare var
$orders = [];

// Get all orders from the database
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

$result->free();

// Include header
$siteName = "Bestellungen";
include("./includes/header.inc.php");

?>
<div class="row fadeIn">

    <?php
    // Create element for each order
    foreach ($orders as $order) {
    ?>

        <div class="col-12 mb-2">
            <div class="card" data-ripple-color="light">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $order['eventName']; ?> </h5>
                    <?php echo '<p class="card-text">Besteller: ' . $order['firstname'] . ' ' . $order['lastname'] . '<br>E-Mail: <a href="mailto:' . $order['email'] . '"> ' . $order['email'] . '</a></p>';
                    echo '<p>Abholdatum: ' . $order['pickUpDatetime'] . '<br>';
                        echo 'Zurückbringdatum: ' . $order['returnDatetime'] . '</p>';
                    echo '<button id="btn-' . $order['idOrder'] . '" class="btn btn-info mb-3" onClick="showDetail(this)">Details anzeigen</button>';
                    echo '<div class="mb-3" style="display: none">';

                        // Get items in the order
                        $items = [];
                        $stmt = "SELECT * FROM order_has_item
                        LEFT JOIN item ON order_has_item.item_idItem = item.idItem
                        WHERE order_has_item.order_idOrder = " . $order['idOrder'];
                        if (!$result = $mysqli->query($stmt)) {
                        echo "Oops! Something went wrong. Please try again later.";
                        return false;
                        }

                        // Fetch result
                        while ($row = $result->fetch_assoc()) {
                        $items[] = $row;
                        }


                        // Create li element for each item
                        $rowNumber = 1;
                        if (count($items)) {
                        echo '<table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Titel</th>
                                    <th scope="col">Anzahl</th>
                                </tr>
                            </thead>
                            <tbody>';

                                foreach ($items as $item) {
                                echo '<tr>
                                    <th scope="row">' . $rowNumber . '</th>
                                    <td><a href="detail.php?id=' . $item['idItem'] . '">' . $item['title'] . '</a></td>
                                    <td>' . $item['count'] . '</td>
                                </tr>';
                                $rowNumber++;
                                }

                                echo '</tbody>
                        </table>';
                        } else {
                        echo '<span class="text-warning">Keine Einträge vorhanden</span>';
                        }

                        // Create a button depending on the state of the order
                        echo '</div><button class="order-event btn btn-primary btn-block" data-order-id="' . $order['idOrder'] . '" data-order-action="';
                    if ($order['isReady'] == 0) {
                        echo 'isReady">Bestellung bereitgestellt</button>';
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
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>
<script>
    // Get all order-events
    const orderActionItemList = document.querySelectorAll('.order-event');

    // Shows the items in an order
    const showDetail = (e) => {
        let nextSibling = e.nextElementSibling;
        switch (nextSibling.style.display) {
            case "none":
                nextSibling.style.display = "block";
                break;
            case "block":
                nextSibling.style.display = "none";
                break;
        }
    };

    // Changes the state of an order
    const orderAction = event => {
        const clickedItem = event.target;
        // Create GET Request
        fetch('adminOrderHandler.php?orderId=' + clickedItem.dataset.orderId + '&orderAction=' + clickedItem.dataset.orderAction)
            .then(res => {
                return res.json();
            }).then(res => {
                if (res.code != 200) {
                    alert(res.description);
                } else {
                    // If successful reloads page
                    location.reload();
                }
            });
    };

    // Adds event listeners
    orderActionItemList.forEach(item => {
        item.addEventListener('click', orderAction);
    });
</script>
</body>

</html>