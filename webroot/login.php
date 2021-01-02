<?php
require("includes/autoLoad.php");
require("../pw.inc.php");

// Declare var
$email_isset = true;
$password_isset = true;
$message = "";
$login_success = true;

session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["reason"])) { //resetPassword.php redirects to this page, in this case a message will be shown
        switch ($_GET["reason"]) {
            case "resetsuccessful":
                $message = "Das Zurücksetzen des Passworts war erfolgreich. Bitte melde Dich an.";
        }
    }
    $cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : ''; // Is there a rememberMe Cookie
    if (isset($_SESSION["userId"])) { // Is Session already present?
        redirectToRequestedPage();
    } elseif ($cookie) { // RememberMe Cookie is present
        list($user, $token, $mac) = explode(':', $cookie);
        if (hash_equals(hash_hmac('sha256', $user . ':' . $token, $privateKey), $mac)) { // Cookie wasn't modified ($privateKey is from pw.inc.php)
            $query = "SELECT rememberMeToken.token, rememberMeToken.expire, webShopUser.* FROM rememberMeToken INNER JOIN webShopUser ON webShopUser.idWebShopUser=rememberMeToken.webShopUser_idWebShopUser AND webShopUser.userToken=?;"; //Select all rememberMe Tokens from user in DB
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows) { //User has tokens
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach ($rows as $row) { //Goes over all tokens which user has
                    if (hash_equals($row["token"], $token)) {
                        $expDate = date("Y-m-d H:i:s", strtotime($row["expire"])); //Read expire-date from DB

                        //Create current date
                        $Format = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
                        $Date = date("Y-m-d H:i:s", $Format);

                        if ($expDate > $Date) { // Check if Remember Me is not expired
                            createSession($row);
                            redirectToRequestedPage();
                            break;
                        }
                    }
                }
            }
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"])) {
        if (empty(trim($_POST["email"]))) {
            $email_isset = false;
        }
    } else {
        $email_isset = false;
    }

    if (isset($_POST["password"])) {
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
                createSession($row);

                if (isset($_POST["login-remember"])) { //Checks if remember me is set;

                    do {
                        $token = bin2hex(random_bytes(128)); //Create random token

                        //Try to select token
                        $query = "SELECT * FROM rememberMeToken WHERE token=?;";
                        $stmt = $mysqli->prepare($query);
                        $stmt->bind_param("s", $token);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } while ($result->num_rows); //Generate new token if token already exists (pretty unlikely though)

                    //Remember Me is only 30 days valid
                    $expFormat = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 30, date("Y"));
                    $expDate = date("Y-m-d H:i:s", $expFormat);

                    $query = "INSERT INTO rememberMeToken VALUES (?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("sis", $token, $row["idWebShopUser"], $expDate);
                    $stmt->execute();

                    $query = "SELECT * FROM webShopUser WHERE idWebShopUser=?;";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("s", $row["idWebShopUser"]);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $result->free();

                    $cookie = $row["userToken"] . ':' . $token;
                    $mac = hash_hmac('sha256', $cookie, $privateKey); //Key is stored in pw.inc.php
                    $cookie .= ':' . $mac;
                    setcookie('rememberme', $cookie, time() + (86400 * 30));
                }
                redirectToRequestedPage();
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

//Create a Session
function createSession($row)
{
    $_SESSION["userId"] = $row["idWebShopUser"];
    $_SESSION["userRole"] = $row["role_idRole"];
    session_regenerate_id(true);
}

// If possible send user back from where he came from
function redirectToRequestedPage()
{
    if (isset($_REQUEST["target"])) {
        header("Location: " . $_REQUEST["target"]);
    } else {
        header("Location: shop.php");
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="Content">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0">
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
            <div class="col-lg-5 col-md-7 col-sm-10 col-12">
                <h1 class="text-center mb-4">Anmelden</h1>
                <?php
                if (!empty($message)) {
                    echo '<p class="mb-4 note ';
                    if (!$login_success) {
                        echo 'note-danger';
                    } else {
                        echo 'note-success';
                    }
                    echo '">' . $message . '</p>';
                }
                ?>
                <form id="1" method="post">
                    <!-- Email input -->
                    <div class="form-outline mb-5">
                        <input name="email" type="email" id="email" class="form-control 
                        <?php if (!$email_isset) {
                            echo "is-invalid";
                        } ?>
                        " value="
                        <?php if (isset($_POST["email"])) {
                            echo $_POST["email"];
                        } ?>
                         " />
                        <label class="form-label" for="email">E-Mail</label>
                        <?php
                        if (!$email_isset) {
                            echo '<div class="invalid-feedback">Bitte gib Deine E-Mail-Adresse ein</div>';
                        }
                        ?>
                    </div>

                    <!-- Password input -->
                    <div class="form-outline mb-5">
                        <input name="password" type="password" id="password" class="form-control 
                        <?php if (!$password_isset) {
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
                            <a href="forgotPassword.php">Passwort vergessen?</a>
                        </div>
                    </div>
                    <!-- Submit button -->
                    <button type="submit" id="login-submit" class="btn btn-primary btn-block mt-5">
                        Anmelden
                    </button>
                    <a href="register.php" class="btn btn-outline-primary btn-block">
                        Registrieren
                    </a>
                </form>
            </div>
        </div>
    </div>
    <!-- MDB -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>
</body>

</html>