<?php

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
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar fixed-top navbar-expand-lg navbar-light red scrolling-navbar">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand" href="#" target="_blank">
                <strong>Webshop</strong>
            </a>

            <!-- Collapse -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
            <div class="row wow fadeIn">

                <!--Grid column-->
                <div class="col-md-6 mb-4">

                    <img src="https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Products/14.jpg"
                        class="img-fluid" alt="">

                </div>
                <!--Grid column-->

                <!--Grid column-->
                <div class="col-md-6 mb-4">

                    <!--Content-->
                    <div class="p-4">

                        <div class="mb-3">
                            <a href="">
                                <span class="badge purple mr-1">Category 2</span>
                            </a>
                            <a href="">
                                <span class="badge blue mr-1">New</span>
                            </a>
                            <a href="">
                                <span class="badge red mr-1">Bestseller</span>
                            </a>
                        </div>

                        <p class="lead">
                            <span>$100</span>
                        </p>

                        <p class="lead font-weight-bold">Description</p>

                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Et dolor suscipit libero eos atque
                            quia ipsa
                            sint voluptatibus!
                            Beatae sit assumenda asperiores iure at maxime atque repellendus maiores quia sapiente.</p>

                        <form class="d-flex justify-content-left">
                            <!-- Default input -->
                            <input type="number" value="1" aria-label="Search" class="form-control mr-1"
                                style="width: 100px">
                            <button class="btn btn-primary btn-md my-0 p" type="submit">Zum Warenkorb hinzufügen
                                <i class="fas fa-shopping-cart ml-1"></i>
                            </button>

                        </form>

                    </div>
                    <!--Content-->

                </div>
                <!--Grid column-->

            </div>
            <!--Grid row-->

            <hr>

        </div>
    </main>
    <!--Main layout-->

    <!--Footer-->
    <footer class="page-footer text-center font-small mt-4 wow fadeIn">

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