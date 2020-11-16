<?php
require("autoLoad.php");

$products = array(); //DB Query


?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webshop</title>

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
    <!-- Navbar -->
    <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-white scrolling-navbar">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand" href="#" target="_blank">
                <strong>Webshop</strong>
            </a>

            <!-- Collapse -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Links -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <!-- Left -->
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link waves-effect" href="#">Home
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>
                </ul>

                <!-- Right -->
                <ul class="navbar-nav nav-flex-icons">
                    <li class="nav-item">
                        <a class="nav-link waves-effect">
                            <span class="badge red z-depth-1 mr-1">1</span>
                            <i class="fas fa-shopping-cart"></i>
                            <span class="clearfix d-none d-sm-inline-block">Warenkorb</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-primary">Anmelden</button>
                    </li>
                </ul>

            </div>

        </div>
    </nav>
    <!-- Navbar -->

    <!--Main layout-->
    <main class="mt-5 pt-4">
        <div class="container dark-grey-text mt-5">
            <!--Grid row-->
            <div class="row fadeIn">

                <?php
                foreach ($products as $product) {
                ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="card hover-overlay ripple" data-ripple-color="light">
                            <div class="bg-image">
                                <!-- <img src="https://picsum.photos/214/143" class="img-fluid" /> -->
                                <?php echo '<img class="img-fluid" src="' . $product->img . '" />' ?>
                            </div>
                            <div class="card-body">
                                <!-- <h5 class="card-title">Produkt</h5> -->
                                <?php echo '<h5 class="card-title">' . $product->title . '</h5>' ?>
                                <!-- <p class="card-text">
                                Some quick example text to build on the card title and make up the bulk of the
                                card's content.
                            </p> -->
                                <?php echo '<p class="card-text">' . $product->desc . '</p>' ?>
                                <!-- <p>4 auf Lager</p> -->
                                <!-- <p>4 auf Lager</p> -->
                                <?php echo '<p>' . $product->stock . '</p>' ?>
                            </div>
                            <?php echo '<a href="detail.php?id=' . $product->id . '">' ?>
                            <div class="mask" style="background-color: rgba(251, 251, 251, 0.15)"></div>
                            </a>
                        </div>
                    </div>
                <?php
                }
                ?>

            </div>
            <!--Grid row-->

            <hr>

        </div>
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