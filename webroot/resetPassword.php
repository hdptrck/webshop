<?php
require("autoLoad.php");

$password_isValid = true;
$password_error = "";
$password_repeat_isValid = true;
$password_repeat_error = "";

if (isset($_GET["token"])) {
    $token = $_GET["token"];

    //Select row of used token
    $query = "SELECT * FROM passwordResetToken WHERE token=?;";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows) { //Token exists
        $row = $result->fetch_assoc();
        $result->free();

        $expDate = date("Y-m-d H:i:s", $row["expire"]); //Store expire-date from DB

        $Format = mktime(date("H"), date("i") - 15, date("s"), date("m"), date("d"), date("Y"));
        $Date = date("Y-m-d H:i:s", $Format);

        if ($expDate > $Date) { // Check if token is not expired
            if ($_SERVER["REQUEST_METHOD"] == "POST") { //Reset-Button was pressed
                //Check if password is valid. Same criterias as in register.php
                if (isset($_POST['reset-password'])) {
                    if (!empty(trim($_POST['reset-password'])) || preg_match("#(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$#", trim($_POST['reset-password']))) {
                        if (strlen($_POST['reset-password']) <= 256) {
                            if (isset($_POST['reset-password-repeat'])) {
                                if ($_POST['reset-password'] == $_POST['reset-password-repeat']) {
                                    $password = $_POST['reset-password'];
                                } else {
                                    $password_repeat_isValid = false;
                                    $password_repeat_error = "Passwörter stimmen nicht überein";
                                }
                            } else {
                                $password_repeat_isValid = false;
                                $password_repeat_error = "Bitte gib das Passwort erneut ein";
                            }
                        } else {
                            $password_isValid = false;
                            $password_error = "Das Passwort ist zu lang";
                        }
                    } else {
                        $password_isValid = false;
                        $password_error = "Das Passwort muss aus mindestens acht Zeichen welche Gross-, Kleinbuchstaben Zahlen und Sonderzeichen bestehen";
                    }
                } else {
                    $password_isValid = false;
                    $password_error = "Bitte gib ein Password ein";
                }

                if ($password_isValid && $password_repeat_isValid) { //Passwords are valid
                    $webShopUser = $row["webShopUser_idWebShopUser"]; //Get id of user which wants new password
                    $password = password_hash($password, PASSWORD_DEFAULT); //Generate hash

                    //Update PW in DB
                    $query = "UPDATE webShopUser SET password=? WHERE idWebShopUser=?;";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("si", $password, $webShopUser);
                    if ($stmt->execute()) { //Update to DB was succesful
                        header("Location: login.php?reson=resetsuccessful"); //Forward to login-page with custom message

                        //Token is one-time-use. Delete token after use
                        $query = "DELETE FROM passwordResetToken WHERE token=?;";
                        $stmt = $mysqli->prepare($query);
                        $stmt->bind_param("si", $token);
                        $stmt->execute();
                    } else {
                        $reset_successful = false;
                    }
                }
            }
        } else {
            header("Location: forgot_password.php?reason=tokenexpired"); //Forward to forgot-page with custom message
        }
    } else {
        header("Location: forgot_password.php?reason=tokeninvalid"); //Forward to forgot-page with custom message
    }
} else {
    header("Location: forgot_password.php"); //Forward to forgot-page because there is no token in link
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="Content">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=no">
    <title>Passwort zurücksetzen</title>

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
                <h1 class="text-center mb-5">Passwort zurücksetzen</h1>
                <p class="text-center mb-5">Setze nun ein neues Passwort. Anschliessend wirst du zur Anmelde-Seite weitergeleitet.</p>
                <?php
                if (!$reset_successful) {
                    echo '<div class="invalid-feedback"> Das Zurücksetzen war nicht erfolgreich, bitte versuche es später erneut. </div>';
                }
                ?>
                <form method="post">
                    <div class="form-outline mb-4">
                        <input name="reset-password" type="password" id="reset-password" class="form-control <?php if (!$password_isValid) {
                                                                                                                    echo "is-invalid";
                                                                                                                } ?>" value="<?php if (isset($_POST["reset-password"])) {
                                                                                                                                    echo $_POST["reset-password"];
                                                                                                                                } ?>" />
                        <label class="form-label" for="reset-password">Neues Passwort</label>
                        <?php
                        if (!$password_isValid) {
                            echo '<div class="invalid-feedback">' .
                                $password_error .
                                '</div>';
                        }
                        ?>
                    </div>

                    <!-- Repeat Password input -->
                    <div class="form-outline mb-4">
                        <input name="reset-password-repeat" type="password" id="reset-password-repeat" class="form-control <?php if (!$password_repeat_isValid) {
                                                                                                                                echo "is-invalid";
                                                                                                                            } ?>" value="<?php if (isset($_POST["reset-password"])) {
                                                                                                                                                echo $_POST["reset-password"];
                                                                                                                                            } ?>" />
                        <label class="form-label" for="reset-password-repeat">Passwort wiederholen</label>
                        <?php
                        if (!$password_repeat_isValid) {
                            echo '<div class="invalid-feedback">' .
                                $password_repeat_error .
                                '</div>';
                        }
                        ?>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" id="reset-submit" class="btn btn-primary btn-block mt-5">
                        Zurücksetzen
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>
</body>

</html>