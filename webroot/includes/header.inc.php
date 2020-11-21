<?php
/*
VAR to set:
$siteName
$numberOfItems
*/

$pages = [
    [
        'fileName' => 'shop.php',
        'displayText' => 'Shop',
    ],
    [
        'fileName' => 'admin.php',
        'displayText' => 'Bestellungen',
    ],
];

// Create Navigation Items
foreach ($pages as $index => $page) {
    $listItem = "<li";
    if ($_SERVER['SCRIPT_NAME'] == "/" . $page['fileName']) {
        $listItem .= "  class=\"nav-item active\" aria-current=\"page\"><a class=\"nav-link waves-effect\">";
    } else {
        $listItem .= "><a  class=\"nav-link waves-effect\" href=\"" . $page['fileName'] . "\">";
    }
    $listItem .= $page['displayText'] . "</a></li>";
    $pages[$index]['listItem'] = $listItem;
}
?>




<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $siteName ?></title>

    <!-- Google Material Design Icons https://material.io/resources/icons/ -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.css" rel="stylesheet" />
    <!--https://mdbootstrap.com/docs/-->

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>

    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
</head>

<body>
    <nav class="navbar fixed-top navbar-expand-md navbar-light bg-white scrolling-navbar">
        <div class="container">

            <!-- <a class="navbar-brand" href="#" target="_blank"> -->
            <strong class="navbar-brand">Webshop</strong>
            <!-- </a> -->

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">


                <ul class="navbar-nav mr-auto">

                    <?php
                    foreach ($pages as $page) {
                        echo $page['listItem'];
                    }
                    ?>

                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle hidden-arrow" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">
                            <span class="material-icons-outlined">
                                shopping_bag
                            </span>

                            <?php
                            if (isset($numberOfItems)) {
                                echo  "<span class=\"badge rounded-pill badge-notification bg-danger\">"  . $numberOfItems . "</span>";
                            }
                            ?>

                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">
                            <span class="material-icons-outlined">
                                account_circle
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="account.php">Mein Konto</a></li>
                            <li><a class="dropdown-item" href="#">Abmelden</a></li>
                        </ul>
                    </li>
                </ul>

            </div>

        </div>
    </nav>
    <!-- Navbar -->

    <!--Main layout-->
    <main class="mt-5 pt-5 main-min-height">
        <div class="container dark-grey-text">
            <!--Grid row-->
            <div class="row fadeIn mb-4">
                <div class="col text-center">
                    <h1><?php echo $siteName; ?></h1>
                </div>
            </div>