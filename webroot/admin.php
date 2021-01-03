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
                    <div class="row mb-3">
                        <div class="col-md-6 col-12">
                            <p class="card-text"><a href="#" data-mdb-toggle="tooltip" title="Abholdatum" class="a-nostyling"><span class="material-icons-outlined material-icons-extra-class">local_shipping</span></a> <?php echo $order['pickUpDatetime']; ?></p>
                            <p class="card-text"><a href="#" data-mdb-toggle="tooltip" title="Zurückbringdatum" class="a-nostyling"><span class="material-icons-outlined material-icons-extra-class">assignment_return</span></a> <?php echo $order['returnDatetime']; ?></p>
                            <p class="card-text mb-3"><a href="#" data-mdb-toggle="tooltip" title="Bereitstellungsort" class="a-nostyling"><span class="material-icons-outlined material-icons-extra-class">pin_drop</span></a> <?php echo $order['name']; ?></p>
                        </div>
                        <div class="col-md-6 col-12">
                            <p class="card-text"><a href="#" data-mdb-toggle="tooltip" title="Besteller" class="a-nostyling"><span class="material-icons-outlined material-icons-extra-class">assignment_ind</span></a> <?php echo $order['firstname'] . ' ' . $order['lastname']; ?></p>
                            <p class="card-text"><a href="#" data-mdb-toggle="tooltip" title="E-Mail" class="a-nostyling"><span class="material-icons-outlined material-icons-extra-class">email</span></a> <a href="mailto:<?php echo $order['email']; ?>"> <?php echo $order['email']; ?></a></p>
                        </div>
                    </div>
                    <button id="btn-<?php echo  $order['idOrder']; ?>" class="mr-3 mb-3 btn btn-outline-info" onClick="showDetail(this)">Details anzeigen</button>

                    <?php
                    // Create a button depending on the state of the order
                    echo '<button class="mb-3 order-event btn btn-primary" data-order-id="' . $order['idOrder'] . '" data-order-action="';
                    if ($order['isReady'] == 0) {
                        echo 'isReady">Bestellung bereitgestellt</button>';
                    } else {
                        echo 'isReturned">Bestellung zurückgegeben</button>';
                    }
                    ?>

                    <div class="mb-3 order-info" style="display: none">
                        <?php
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
                        ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Titel</th>
                                        <th scope="col">Anzahl</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($items as $item) {
                                    ?>
                                        <tr>
                                            <th scope="row"><?php echo  $rowNumber; ?></th>
                                            <td><a href="detail.php?id=<?php echo $item['idItem']; ?>"><?php echo $item['title']; ?></a></td>
                                            <td><?php echo  $item['count']; ?></td>
                                        </tr>
                                    <?php
                                        $rowNumber++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php
                        } else {
                            echo '<span class="text-warning">Keine Einträge vorhanden</span>';
                        }


                        ?>
                    </div>
                </div>
            </div>
        </div>

    <?php
    }
    ?>

</div>



<script>
    // Get all order-events
    const orderActionItemList = document.querySelectorAll('.order-event');

    // Shows the items in an order
    const showDetail = (e) => {
        // Get parent node and search for matching child
        const parentofSelected = e.parentNode;
        const children = parentofSelected.childNodes;
        for (var i = 0; i < children.length; i++) {
            if (children[i].tagName == 'DIV') {
                if (children[i].classList.contains('order-info')) {
                    switch (children[i].style.display) {
                        case "none":
                            e.innerText = "Details ausblenden";
                            children[i].style.display = "block";
                            break;
                        case "block":
                            e.innerText = "Details anzeigen";
                            children[i].style.display = "none";
                            break;
                    }
                    break;
                }
            }
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
                    //alert(res.description);
                    let div = document.createElement("div");
                    let text = document.createTextNode(res.description)
                    div.appendChild(text);
                    div.classList.add("note", "note-danger");
                    div.setAttribute("id", "message");
                    document.body.appendChild(div);
                    setInterval(() => {
                        document.getElementById("message").style.opacity = '0';
                    }, 1000);
                    setTimeout(function() {
                        document.getElementById("message").remove();
                    }, 7001);
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

<?php
include("./includes/footer.inc.php");
?>