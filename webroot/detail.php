<?php
require("./includes/autoLoad.php");
require("includes/sessionChecker.php");

// Declare vars
$error = '';

// Check GET Parameters
if (!isset($_GET['id'])) {
    header('Location: 404.html');
    die();
}

$id = preg_replace('#[^0-9]#i', "", $_GET['id']);

// Get item from the database
$query = "SELECT * FROM item WHERE idItem = ?;";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if item was found, if not the link was manipulated
if (!$result->num_rows) {
    header('Location: /404.html');
    die();
}

$item = $result->fetch_assoc();

// Shopping cart handling
if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["number"])) {

    // Check if input was manipulated
    if (preg_replace('#[^0-9]#i', "", $_POST["number"]) > $item["count"]) {
        $_POST["number"] = $item["count"];
    } else {
        $_POST["number"] = preg_replace('#[^0-9]#i', "", $_POST["number"]);
    }

    if (!isset($_SESSION["shoppingCart"]) or !is_array($_SESSION["shoppingCart"])) {
        $shoppingCart = array();
    } else {
        $shoppingCart = $_SESSION["shoppingCart"];
    }

    $order = array("count" => $_POST["number"], "id" => $_GET["id"]);
    $affected_entry = null;
    foreach ($shoppingCart as &$entry) {
        if ($entry['id'] == $order['id']) {
            $affected_entry = &$entry;
            break;
        }
    }

    if ($affected_entry != null) {
        $affected_entry['count'] = $affected_entry['count'] + $order['count'];
    } else {
        $shoppingCart[] = $order;
    }

    $_SESSION["shoppingCart"] = $shoppingCart;
    $message = "Der Gegenstand wurde dem Warenkorb hinzugefügt.";
}

// Include header
$siteName = "Detail";
include("./includes/header.inc.php");
?>

<?php
// Display message
if (isset($message)) {
    echo '<div id="message" class="note note-success mb-4"><p>' . $message . '</p></div>';
}
?>

<div class="row fadeIn">
    <!-- Product image -->
    <div class="col-md-6 mb-4 d-flex justify-content-md-end">

        <?php
        echo '<img class="img-fluid" alt="Produktbild" src="';
        echo ($item['thumb'] == null) ? './img/products/1.jpg' : $item['picture'];
        echo '" />';
        ?>

    </div>
    <div class="col-md-6 mb-4">
        <div class="pl-md-4">
            <p class="lead clearfix">
                <!-- Count -->
                <span class="float-left">
                    <?php echo $item['count']; ?>
                    Stück an Lager
                </span>

                <!-- Edit and delete product -->
                <?php
                if ($_SESSION['userRole'] >= 2) {
                    echo '<span class="float-right">
                            <a href="addProduct.php?id=' . $item['idItem'] . '" class="text-primary">
                                <span class="material-icons-outlined">
                                    create
                                </span>
                            </a>
                            <span onClick="deleteProduct(' . $item['idItem'] . ')" class="clickable-icon text-danger material-icons-outline">
                                <span class="material-icons-outlined">
                                    delete
                                </span>
                            </span>
                        </span>';
                }
                ?>

            </p>

            <!-- Title -->
            <p class="lead font-weight-bold"><?php echo $item['title']; ?></p>

            <!-- Description -->
            <p><?php echo $item['description']; ?></p>

            <!-- Input field count -->
            <form class="d-flex justify-content-left" method="post">
                <input name="number" type="number" min="1" max="<?php echo $item['count']; ?>" value="1" aria-label="Search" class="form-control mr-2" style="width: 100px">
                <button class="btn btn-primary btn-md my-0 p" type="submit">Zur Bestellung hinzufügen</button>

            </form>
        </div>
    </div>
</div>

<script>
    // Delete Product
    const deleteProduct = (idItem) => {
        if (confirm('Sind Sie sicher, dass Sie dieses Produkt löschen wollen?')) {
            console.log("IdItem", idItem);
            fetch('deleteItem.php?id=' + idItem)
                .then(function(res) {
                    return res.json();
                }).then(res => {
                    if (res.code != 200) {
                        alert(res.description);
                    } else {
                        // Successful
                        alert(res.description);
                        window.location = "shop.php";
                    }
                });
        }
    };

    //message fadeOut
    setTimeout(function () {
        document.getElementById("message").style.opacity = '0';
    }, 1);
    setTimeout(function () {
        document.getElementById("message").remove();
    }, 6001);
</script>

<?php
include("./includes/footer.inc.php");
?>