<?php
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

foreach ($pages as $index => $page) {
    $listItem = "<li";
    if ($_SERVER['SCRIPT_NAME'] == $page['fileName']) {
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
    <title><?= $site_name ?></title>

    <!-- Font Awesome -->
    <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.css" rel="stylesheet" />
    <!--https://mdbootstrap.com/docs/-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
</head>

<body>
    <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-white scrolling-navbar">
        <div class="container">

            <!-- <a class="navbar-brand" href="#" target="_blank"> -->
                <strong class="navbar-brand">Webshop</strong>
            <!-- </a> -->

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">


                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link waves-effect" href="#">Home
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>

                    <?php
                    foreach ($pages as $page) {
                        echo $page['listItem'];
                    }
                    ?>
                </ul>

                <ul class="navbar-nav nav-flex-icons">
                    <li class="nav-item">
                        <a class="nav-link waves-effect">
                            <span class="badge red z-depth-1 mr-1">1</span>
                            <i class="fas fa-shopping-cart"></i>
                            <span class="clearfix d-none d-sm-inline-block">Bestellungen</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-primary">Account</button>
                    </li>
                </ul>

            </div>

        </div>
    </nav>
    <!-- Navbar -->

    <!--Main layout-->
    <main class="mt-5 pt-4">

    </main>
    <!--Main layout-->

    <!--Footer-->
    <footer class="page-footer text-center font-small mt-4 fadeIn">

        <hr class="my-4">

        <!--Copyright-->
        <div class="footer-copyright py-3">
            © 2020 Copyright:
        </div>
        <!--/.Copyright-->

    </footer>
    <!--/.Footer-->

    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>
</body>

</html>