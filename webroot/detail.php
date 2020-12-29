<?php
require("./includes/autoLoad.php");
// Session temporarly deactivated for development
require("includes/sessionChecker.php");
$error = '';
if (!isset($_GET['id'])) {
    header('Location: 404.html');
    die();
}

$id = preg_replace('#[^0-9]#i', "", $_GET['id']);

$query = "SELECT * FROM item WHERE idItem = ?;";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    header('Location: /404.html');
    die();
}

$item = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["number"])) {
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
}

?>

<?php
$siteName = "Detail";
include("./includes/header.inc.php");

?>

<div class="row fadeIn">
    <!--Grid column-->
    <div class="col-md-6 mb-4">

        <?php
        echo '<img class="img-fluid" src="';
        echo ($item['thumb'] == null) ? './img/products/1.jpg' : $item['picture'];
        echo '" />';
        ?>

    </div>
    <!--Grid column-->

    <!--Grid column-->
    <div class="col-md-6 mb-4">

        <!--Content-->
        <div class="pl-md-4">
            <p class="lead clearfix">
                <span class="float-left">
                    <?php echo $item['count']; ?>
                    Stück an Lager
                </span>
                <span class="float-right">
                    <a href="#" class="text-primary">
                        <span class="material-icons-outlined">
                            create
                        </span>
                    </a>
                    <a href="#" class="text-danger">
                        <span class="material-icons-outlined">
                            delete
                        </span>
                    </a>
                </span>
            </p>

            <p class="lead font-weight-bold">
                <?php echo $item['title']; ?>
            </p>

            <p><?php echo $item['description']; ?></p>

            <form class="d-flex justify-content-left" method="post">
                <!-- Default input -->
                <input name="number" type="number" min="1" max="<?php echo $item['count']; ?>" value="1" aria-label="Search" class="form-control mr-2" style="width: 100px">
                <button class="btn btn-primary btn-md my-0 p" type="submit">Zur Bestellung hinzufügen</button>

            </form>

        </div>
        <!--Content-->

    </div>
    <!--Grid column-->

</div>

</div>


<?php
include("./includes/footer.inc.php");
?>