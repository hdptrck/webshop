<?php
require("includes/autoLoad.php");

$firstname_isValid = true;
$firstname_error = "";
$lastname_isValid = true;
$lastname_error = "";
$email_isValid = true;
$email_error = "";
$password_isValid = true;
$password_error = "";
$password_repeat_isValid = true;
$password_repeat_error = "";

// Session temporarly deactivated for development
//require("includes/sessionChecker.php");
$user = [];

if (isset($_SESSION["userId"])) {
    $id = $_SESSION["userId"];

    $query = "SELECT firstname, lastname, email FROM webshopuser WHERE idWebShopUser = ?;";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result->num_rows) {
        header('Location: /404.html');
        die();
    }

    $user = $result->fetch_assoc();
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
                            <input name="register-firstname" type="text" id="register-firstname" class="form-control <?php if (!$firstname_isValid) {
                                                                                                                            echo "is-invalid";
                                                                                                                        } ?>" value="<?php if (isset($_POST["register-firstname"])) {
                                                                                                                                            echo $_POST["register-firstname"];
                                                                                                                                        } ?>" />
                            <label class="form-label" for="register-firstname">Vorname</label>
                            <?php
                            if (!$firstname_isValid) {
                                echo '<div class="invalid-feedback">' .
                                    $firstname_error .
                                    '</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input name="register-lastname" type="text" id="register-lastname" class="form-control <?php if (!$lastname_isValid) {
                                                                                                                        echo "is-invalid";
                                                                                                                    } ?>" value="<?php if (isset($_POST["register-lastname"])) {
                                                                                                                                        echo $_POST["register-lastname"];
                                                                                                                                    } ?>" />
                            <label class="form-label" for="register-lastname">Nachname</label>
                            <?php
                            if (!$lastname_isValid) {
                                echo '<div class="invalid-feedback">' .
                                    $lastname_error .
                                    '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Email input -->
                <div class="form-outline mb-4">
                    <input name="register-email" type="email" id="register-email" class="form-control <?php if (!$email_isValid) {
                                                                                                            echo "is-invalid";
                                                                                                        } ?>" value="<?php if (isset($_POST["register-email"])) {
                                                                                                                            echo $_POST["register-email"];
                                                                                                                        } ?>" />
                    <label class="form-label" for="register-email">E-Mail</label>
                    <?php
                    echo 
                    ?>
                </div>

                <!-- Password input -->
                <div class="form-outline mb-4">
                    <input name="register-password" type="password" id="register-password" class="form-control <?php if (!$password_isValid) {
                                                                                                                    echo "is-invalid";
                                                                                                                } ?>" value="<?php if (isset($_POST["register-password"])) {
                                                                                                                                    echo $_POST["register-password"];
                                                                                                                                } ?>" />
                    <label class="form-label" for="register-password">Passwort</label>
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
                    <input name="register-password-repeat" type="password" id="register-password-repeat" class="form-control <?php if (!$password_repeat_isValid) {
                                                                                                                                    echo "is-invalid";
                                                                                                                                } ?>" value="<?php if (isset($_POST["register-password-repeat"])) {
                                                                                                                                                    echo $_POST["register-password-repeat"];
                                                                                                                                                } ?>" />
                    <label class="form-label" for="register-password-repeat">Passwort wiederholen</label>
                    <?php
                    if (!$password_repeat_isValid) {
                        echo '<div class="invalid-feedback">' .
                            $password_repeat_error .
                            '</div>';
                    }
                    ?>
                </div>

                <!-- Submit button -->
                <button type="submit" id="register-submit" class="btn btn-primary btn-block mt-5">
                    Speicher
                </button>
                <button type="submit" id="login-submit" class="btn btn-outline-danger btn-block">
                    Abbrechen
                </button>
        </form>
    </div>
</div>
</div>
<!-- MDB -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>
</body>

</html>