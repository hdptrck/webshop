<?php
session_start();
require("autoLoad.php");

$email_isset = true;
$password_isset = true;
$message = "";
$login_success = true;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo "hi";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"])) {
        if (empty(trim($_POST["email"]))) {
            $email_isset = false;
        }
    } else {
        $email_isset = false;
    }

    if (!isset($_POST["password"])) {
        if (empty(trim($_POST["password"]))) {
            $password_isset = false;
        }
    } else {
        $password_isset = false;
    }

    if ($email_isset && $password_isset) {
        $query = "SELECT * FROM webShopUser WHERE email=?;";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $_POST["email"]);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows) { //User exists
            $row = $result->fetch_assoc();
            $result->free();

            if (password_verify($_POST["password"], $row["password"])) { //Checks if passwords match
                $userId = $row["idWebShopUser"];
                $_SESSION["userId"] = $userId;

                if (isset($_POST["login-remember"])) { //Checks if remember me is set;
                    // Remember me;
                }
            } else {
                $login_success = false;
            }
        } else {
            $login_success = false;
        }

        if (!$login_success) {
            $message = "Die E-Mail-Adresse oder das Passwort ist falsch";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="Content">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=no">
    <title>Anmelden</title>

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
    <div class="container">
        <div class="row justify-content-center align-items-center h-100-vh">
            <div class="col-lg-4 col-md-7 col-sm-10 col-12">
                <h1 class="text-center mb-5">Anmelden</h1>
                <?php 
                echo '<p class="text-center';
                if (!$login_success) {
                    echo ' red-text';
                }
                echo '">' . $message . '</p>';

                ?>
                <form method="post">
                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <input name="email" type="email" id="email" class="form-control <?php if (!$email_isset) {
                                                                                            echo "is-invalid";
                                                                                        } ?>" value="<?php if (isset($_POST["email"])) {
                                                                                                            echo $_POST["email"];
                                                                                                        } ?>" />
                        <label class="form-label" for="email">E-Mail</label>
                        <?php
                        if (!$email_isset) {
                            echo '<div class="invalid-feedback">Bitte gib Deine E-Mail-Adresse ein</div>';
                        }
                        ?>
                    </div>

                    <!-- Password input -->
                    <div class="form-outline mb-4">
                        <input name="password" type="password" id="password" class="form-control <?php if (!$password_isset) {
                                                                                                        echo "is-invalid";
                                                                                                    } ?>" value="<?php if (isset($_POST["password"])) {
                                                                                                                        echo $_POST["password"];
                                                                                                                    } ?>" />
                        <label class="form-label" for="password">Password</label>
                        <?php
                        if (!$password_isset) {
                            echo '<div class="invalid-feedback">Bitte gib ein Passwort ein</div>';
                        }
                        ?>
                    </div>

                    <!-- 2 column grid layout -->
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex justify-content-center">
                            <!-- Checkbox -->
                            <div class="form-check mb-3 mb-md-0">
                                <input name="login-remember" class="form-check-input" type="checkbox" value="" id="login-remember" checked />
                                <label class="form-check-label" for="login-remember">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-center">
                            <!-- Simple link -->
                            <a href="#!">Passwort vergessen?</a>
                        </div>
                    </div>
                    <!-- Submit button -->
                    <button type="submit" id="login-submit" class="btn btn-primary btn-block mt-5">
                        Anmelden
                    </button>
                    <button type="submit" id="register-submit" class="btn btn-outline-primary btn-block">
                        Registrieren
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>
</body>

</html>