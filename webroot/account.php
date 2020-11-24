<?php
require("includes/autoLoad.php");


$error = "";
$message = "";
$login_success = true;


$password_isValid = true;
$password_error = "";
$newPassword_isValid = true;
$newPassword_error = "";
$newPasswordConfirm_isValid = true;
$newPasswordConfirm_error = "";

// Session temporarly deactivated for development
require("includes/sessionChecker.php");
$user = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check password
    if (!isset($_POST["password"]) || empty(trim($_POST["password"]))) {
        $password_isValid = false;
        $password_error = "Bitte gib das aktuelle Passwort ein";
    }

    // Check new password
    if (!isset($_POST["new-password"]) || empty(trim($_POST["new-password"])) || !preg_match("/(?=^.{8,255}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", trim($_POST["new-password"]))) {
        $newPassword_isValid = false;
        $newPassword_error = "Das Passwort muss aus mindestens acht Zeichen welche Gross-, Kleinbuchstaben Zahlen und Sonderzeichen bestehen";
    }

    // Check new confirm password
    if (!isset($_POST["new-password-confirm"]) || empty(trim($_POST["new-password-confirm"])) || !preg_match("/(?=^.{8,255}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", trim($_POST["new-password"]))) {
        $newPasswordConfirm_isValid = false;
        $newPasswordConfirm_error = "Das Passwort muss aus mindestens acht Zeichen welche Gross-, Kleinbuchstaben Zahlen und Sonderzeichen bestehen";
    }

    if ($_POST["new-password"] != $_POST["new-password-confirm"]) {
        $newPassword_isValid = $newPasswordConfirm_isValid = false;
        $newPassword_error = $newPasswordConfirm_error = "Die Passwörter stimmen nicht überein";
    }

    if ($password_isValid && isset($_SESSION["userId"]) && $newPassword_isValid) {
        $query = "SELECT password FROM webshopuser WHERE idWebShopUser = ?;";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $_SESSION["userId"]);
        $stmt->execute();
        $result = $stmt->get_result();

        $result->num_rows;
        $user = $result->fetch_assoc();
        $result->free();

        if (password_verify($_POST["password"], $user["password"])) { //Checks if passwords match
            $query = "UPDATE webShopUser SET password = ? WHERE idWebShopUser = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("si", $_POST["new-password"], $_SESSION["userId"]);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($stmt->affected_rows) {
                $message = "Passwort erfolgreich geändert";
                unset($_POST['password']);
                unset($_POST['new-password']);
                unset($_POST['new-password-confirm']);
                session_regenerate_id(true);
            }
        } else {
            $login_success = false;
            $password_isValid = false;
            $password_error = "Das Passwort ist falsch";
        }
    }
}

$siteName = "Konto";

// TODO: Implement shopping cart
$numberOfItems = 2;
include("./includes/header.inc.php");

?>
<div class="row justify-content-center">
    <div class="col-lg-4 col-md-7 col-sm-10 col-12">
        <form method="post">
            <div class="text-center mb-3">
                <!-- Name input -->
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input disabled name="register-firstname" type="text" id="register-firstname" class="form-control" value="<?php echo "Max"; ?>" />
                            <label class="form-label" for="register-firstname">Vorname</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input disabled name="register-lastname" type="text" id="register-lastname" class="form-control" value="<?php echo "Muster"; ?>" />
                            <label class=" form-label" for="register-lastname">Nachname</label>
                        </div>
                    </div>
                </div>

                <!-- Email input -->
                <div class="form-outline mb-4">
                    <input disabled name="register-email" type="email" id="register-email" class="form-control" value="<?php echo "max.muster@webshop.com"; ?>" />
                    <label class="form-label" for="register-email">E-Mail</label>
                </div>

                <!-- Password input -->
                <div class="form-outline mb-5">
                    <input name="password" type="password" id="password" class="form-control 
                        <?php if (!$password_isValid) {
                            echo "is-invalid";
                        } ?>" value="<?php if (isset($_POST["password"])) {
                                            echo $_POST["password"];
                                        } ?>" />
                    <label class="form-label" for="password">Password</label>
                    <?php
                    if (!$password_isValid) {
                        echo '<div class="invalid-feedback">' . $password_error . '</div>';
                    }
                    ?>
                </div>

                <!-- New password input -->
                <div class="form-outline mb-5">
                    <input name="new-password" type="password" id="new-password" class="form-control 
                    <?php if (!$newPassword_isValid) {
                        echo "is-invalid";
                    } ?>" value="<?php if (isset($_POST["new-password"])) {
                                        echo $_POST["new-password"];
                                    } ?>" />
                    <label class="form-label" for="new-password">Passwort</label>
                    <?php
                    if (!$newPassword_isValid) {
                        echo '<div class="invalid-feedback">' .
                            $newPassword_error .
                            '</div>';
                    }
                    ?>
                </div>

                <!-- Confirm new password input -->
                <div class="form-outline mb-5">
                    <input name="new-password-confirm" type="password" id="new-password-confirm" class="form-control 
                    <?php if (!$newPasswordConfirm_isValid) {
                        echo "is-invalid";
                    } ?>" value="<?php if (isset($_POST["new-password-confirm"])) {
                                        echo $_POST["new-password-confirm"];
                                    } ?>" />
                    <label class="form-label" for="new-password-confirm">Passwort wiederholen</label>
                    <?php
                    if (!$newPasswordConfirm_isValid) {
                        echo '<div class="invalid-feedback">' .
                            $newPasswordConfirm_error .
                            '</div>';
                    }
                    ?>
                </div>


                <!-- Submit button -->
                <button type="submit" id="register-submit" class="btn btn-primary btn-block mt-5">
                    Passwort ändern
                </button>
        </form>
    </div>
</div>
</div>
<!-- MDB -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>
</body>

</html>