<?php
require("includes/autoLoad.php");

$password_isset = true;
$error = "";
$login_success = true;

// Session temporarly deactivated for development
require("includes/sessionChecker.php");
$user = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["password"]) || empty(trim($_POST["password"]))) {
        $password_isset = false;
        $error = "Bitte gib ein Passwort ein";
    }

    if ($password_isset && isset($_SESSION["userId"])) {
        $query = "SELECT password FROM webshopuser WHERE idWebShopUser = ?;";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $_SESSION["userId"]);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows) {
            $user = $result->fetch_assoc();
            $result->free();

            if (password_verify($_POST["password"], $user["password"])) { //Checks if passwords match
                header('Location: shop.php');








                
            } else {
                $login_success = false;
            }
        } else {
            $login_success = false;
        }

        if (!$login_success) {
            $password_isset = false;
            $error = "Das Passwort ist falsch";
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
                        <?php if (!$password_isset) {
                            echo "is-invalid";
                        } ?>" value="<?php if (isset($_POST["password"])) {
                                            echo $_POST["password"];
                                        } ?>" />
                    <label class="form-label" for="password">Password</label>
                    <?php
                    if (!$password_isset) {
                        echo '<div class="invalid-feedback">' . $error . '</div>';
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