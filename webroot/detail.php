<?php
require("includes/autoLoad.php");
// Session temporarly deactivated for development
//require("includes/sessionChecker.php");
$error = '';

if (isset($_GET['id'])) {
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
}

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
    <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-white scrolling-navbar">
        <div class="container">

            <a class="navbar-brand" href="#" target="_blank">
                <strong>Webshop</strong>
            </a>

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
                </ul>

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
                <!--Grid column-->
                <div class="col-md-6 mb-4">

                    <?php echo '<img class="img-fluid" src="' . $item['picture'] . '" />' ?>

                </div>
                <!--Grid column-->

                <!--Grid column-->
                <div class="col-md-6 mb-4">

                    <!--Content-->
                    <div class="p-4">
                        <p class="lead">
                            <span>
                                <?php echo $item['count']; ?>
                                Stück an Lager
                            </span>
                        </p>

                        <p class="lead font-weight-bold">
                            <?php echo $item['title']; ?>
                        </p>

                        <p><?php echo $item['description']; ?></p>

                        <form class="d-flex justify-content-left">
                            <!-- Default input -->
                            <input type="number" min="1" max="<?php echo $item['count']; ?>" value="1" aria-label="Search" class="form-control mr-2" style="width: 100px">
                            <button class="btn btn-primary btn-md my-0 p" type="submit">Zur Bestellung hinzufügen
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