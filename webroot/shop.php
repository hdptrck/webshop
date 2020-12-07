<?php
require("includes/autoLoad.php");

// Session temporarly deactivated for development
require("includes/sessionChecker.php");

$items = [];

// Get all Items from Database
$stmt = "SELECT * FROM item;";
if (!$result = $mysqli->query($stmt)) {
    echo "Oops! Something went wrong. Please try again later.";
    return false;
}

// Fetch result
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$scriptName2 = $_SERVER['SCRIPT_NAME'];
?>


<?php
$siteName = "Shop";

// TODO: Implement shopping cart
$numberOfItems = 2;
include("./includes/header.inc.php");

?>

<div class="row fadeIn">

    <?php
    foreach ($items as $item) {
    ?>

        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card hover-overlay ripple" data-ripple-color="light">
                <div class="bg-image">
                    <?php echo '<img class="img-fluid" src="' . $item['thumb'] . '" />' ?>
                </div>
                <div class="card-body">
                    <?php echo '<h5 class="card-title">' . $item['title'] . '</h5>' ?>

                    <?php //echo '<p class="card-text">' . $item['description'] . '</p>' 
                    ?>
                    <?php echo '<p>' . $item['count'] . ' Sück an Lager</p>' ?>
                </div>
                <?php echo '<a href="detail.php?id=' . $item['idItem'] . '">' ?>
                <div class="mask" style="background-color: rgba(251, 251, 251, 0.15)"></div>
                </a>
            </div>
        </div>

    <?php
    }
    ?>

</div>

<?php
include("./includes/footer.inc.php");
?>