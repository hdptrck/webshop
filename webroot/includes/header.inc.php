<?php

// Pages
$pages = [
    [
        'fileName' => 'shop.php',
        'displayText' => 'Shop',
    ]
];

// Add pages for the admin
if ($_SESSION['userRole'] >= 2 ) {
    $pages[] = [
        'fileName' => 'addProduct.php',
        'displayText' => 'Produkt hinzufügen',
    ];

    $pages[] = [
        'fileName' => 'admin.php',
        'displayText' => 'Bestellungen',
    ];
}

if ($_SESSION['userRole'] == 3) {
    $pages[] = [
        'fileName' => 'privileges.php',
        'displayText' => 'Berechtigungen',
    ];
}

// Create navigation li items
foreach ($pages as $index => $page) {
    $listItem = "<li";

    // Check if current script is an active element
    if ($_SERVER['SCRIPT_NAME'] == "/" . $page['fileName']) {
        $listItem .= "  class=\"nav-item active\" aria-current=\"page\"><a class=\"nav-link waves-effect\">";
    } else {
        $listItem .= "><a  class=\"nav-link waves-effect\" href=\"" . $page['fileName'] . "\">";
    }

    $listItem .= $page['displayText'] . "</a></li>";

    // Add element to pages array
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
    <!-- MDBootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar fixed-top navbar-expand-md navbar-light bg-white scrolling-navbar">
        <div class="container">

            <a href="shop.php"><strong class="navbar-brand">Webshop</strong></a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <!-- Navigation elements -->
                <ul class="navbar-nav mr-auto">

                    <?php
                    foreach ($pages as $page) {
                        echo $page['listItem'];
                    }
                    ?>

                </ul>

                <ul class="navbar-nav">
                    <!-- Shopping cart -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle hidden-arrow" href="shoppingCart.php" role="button">
                            <span class="material-icons-outlined">
                                shopping_bag
                            </span>

                            <?php
                            // Displays number of items in the shopping cart
                            if (isset($numberOfItems)) {
                                unset($numberOfItems);
                            }

                            if (isset($_SESSION['shoppingCart']) and is_array($_SESSION['shoppingCart'])) {
                                $numberOfItems = count($_SESSION['shoppingCart']);
                            }

                            if (!isset($numberOfItems) or $numberOfItems == 0) {
                                $numberOfItems = "";
                            }
                            echo  "<span class=\"badge rounded-pill badge-notification bg-danger\">"  . $numberOfItems . "</span>";
                            ?>

                        </a>
                    </li>

                    <!-- Account -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">
                            <span class="material-icons-outlined">
                                account_circle
                            </span>
                        </a>
                        <ul class="dropdown-menu " aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="account.php">Mein Konto</a></li>
                            <li><a class="dropdown-item" href="logout.php">Abmelden</a></li>
                        </ul>
                    </li>
                </ul>

            </div>

        </div>
    </nav>

    <!--Main layout-->
    <main class="mt-5 pt-5 main-min-height">
        <div class="container dark-grey-text">
            <div class="row fadeIn mb-4">
                <div class="col text-center">

                    <!-- Print site name -->
                    <h1><?php echo $siteName; ?></h1>

                </div>
            </div>