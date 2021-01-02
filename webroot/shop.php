<?php
require("includes/autoLoad.php");
require("includes/sessionChecker.php");

// Declare var
$items = [];

// Get all Items from Database
$stmt = "SELECT * FROM item WHERE isActive = 1;";
if (!$result = $mysqli->query($stmt)) {
    echo "Oops! Something went wrong. Please try again later.";
    return false;
}

// Fetch result
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["message"])) {
        switch ($_GET["message"]) {
            case "ordersuccessful":
                $message = "Die Bestellung wurde erfolgreich abgeschickt.";
                break;
            case "deletesuccessful":
                $message = "Das Produkt wurde erfolgreich gelöscht";
                break;
        }
    }
}

// Include header
$siteName = "Shop";
include("./includes/header.inc.php");

// Display message
if (isset($message)) {
    echo '<div id="message" class="note note-success mb-4"><p>' . $message . '</p></div>';
}

?>

<div class="row fadeIn">
    <?php
    // Create element for each item
    foreach ($items as $item) {
    ?>

        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card hover-overlay ripple" data-ripple-color="light">
                <!-- Image -->
                <div class="bg-image">
                    <?php echo '<img class="img-fluid" alt="Produktbild" src="';
                    echo ($item['thumb'] == null) ? './img/products/1.jpg' : $item['thumb'];
                    echo '" />'; ?>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <?php echo '<h5 class="card-title">' . $item['title'] . '</h5>' ?>
                    <?php echo '<p>' . $item['count'] . ' Sück an Lager</p>' ?>
                </div>

                <a href="detail.php?id=<?php echo $item['idItem']; ?>">
                    <div class="mask" style="background-color: rgba(251, 251, 251, 0.15)"></div>
                </a>
            </div>
        </div>

    <?php
    }
    ?>

</div>

<script>
    //message fadeOut
    setTimeout(function() {
        document.getElementById("message").style.opacity = '0';
    }, 1);
    setTimeout(function() {
        document.getElementById("message").remove();
    }, 6001);
</script>

<?php
include("./includes/footer.inc.php");
?>