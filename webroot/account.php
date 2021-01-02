<?php
require("includes/autoLoad.php");

// Declare var
$message = $error = "";
$login_success = true;

$password_isValid = true;
$password_error = "";
$newPassword_isValid = true;
$newPassword_error = "";
$newPasswordConfirm_isValid = true;
$newPasswordConfirm_error = "";

$user = [];

// Check Session
require("includes/sessionChecker.php");

// Select user from DB
$query = "SELECT * FROM webshopuser WHERE idWebShopUser = ?;";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION["userId"]);
$stmt->execute();
$result = $stmt->get_result();

// If user is selected
if ($result->num_rows) {
    // Set user
    $user = $result->fetch_assoc();
    $result->free();
} else {
    $error .= "Benutzer konnte nicht abgerufen werden.<br />";
}

// Request Method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check password
    if (!isset($_POST["password"]) || empty(trim($_POST["password"]))) {
        $password_isValid = false;
        $password_error = "Bitte gib das aktuelle Passwort ein";
    }

    // Check new password
    if (!isset($_POST["new-password"]) || empty(trim($_POST["new-password"])) || !preg_match("/(?=^.{8,255}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", trim($_POST["new-password"]))) {
        $newPassword_isValid = false;
        $newPassword_error = "Das Passwort muss aus mindestens acht Zeichen welche Gross-, Kleinbuchstaben Zahlen und Sonderzeichen sind bestehen";
    }

    // Check if passwords are equal
    if ($_POST["new-password"] != $_POST["new-password-confirm"]) {
        $newPassword_isValid = $newPasswordConfirm_isValid = false;
        $newPassword_error = $newPasswordConfirm_error = "Die Passwörter stimmen nicht überein";
    }

    // All correct until now?
    if ($password_isValid && $newPassword_isValid) {

        // Checks if passwords match
        if (isset($user['password']) and password_verify($_POST["password"], $user["password"])) {
            $password = password_hash($_POST["new-password"], PASSWORD_DEFAULT);

            // Set new password
            $query = "UPDATE webShopUser SET password = ? WHERE idWebShopUser = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("si", $password, $_SESSION["userId"]);
            $stmt->execute();

            // Check if update was successful
            if ($stmt->affected_rows) {
                $message = "Das Passwort wurde erfolgreich geändert";
                unset($_POST['password']);
                unset($_POST['new-password']);
                unset($_POST['new-password-confirm']);
                session_regenerate_id(true);
            } else {
                $error .= "Das Passwort konnte nicht geändert werden.";
            }
        } else {
            $login_success = false;
            $password_isValid = false;
            $password_error = "Das Passwort ist falsch";
        }
    }
}

// Include header
$siteName = "Konto";
include("./includes/header.inc.php");

?>
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-7 col-sm-10 col-12">
        <?php
        if ($error) {
            echo '<p class="note note-danger mb-4">' . $error . '</p>';
        } elseif ($message)
            echo '<p class="note note-success mb-4">' . $message . '</p>';
        ?>
        <form method="post">
            <div class="text-center mb-3">
                <!-- Name input -->
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input disabled name="register-firstname" type="text" id="register-firstname" class="form-control" value="<?php if (isset($user['firstname'])) { echo $user['firstname'];} ?>" />
                            <label class="form-label" for="register-firstname">Vorname</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input disabled name="register-lastname" type="text" id="register-lastname" class="form-control" value="<?php if (isset($user['lastname'])) { echo $user['lastname'];} ?>" />
                            <label class=" form-label" for="register-lastname">Nachname</label>
                        </div>
                    </div>
                </div>

                <!-- Email input -->
                <div class="form-outline mb-4">
                    <input disabled name="register-email" type="email" id="register-email" class="form-control" value="<?php if (isset($user['email'])) { echo $user['email'];} ?>" />
                    <label class="form-label" for="register-email">E-Mail</label>
                </div>

                <!-- Password input -->
                <div class="form-outline mb-5">
                    <input name="password" type="password" id="password" class="form-control 
                        <?php if (!$password_isValid) {
                            echo "is-invalid";
                        } ?>" value="<?php if (isset($_POST["password"])) {
                                            echo $_POST["password"];
                                        } ?>" required />
                    <label class="form-label" for="password">Altes Passwort</label>
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
                                    } ?>" required />
                    <label class="form-label" for="new-password">Neues Passwort</label>
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
                                    } ?>" required />
                    <label class="form-label" for="new-password-confirm">Neues Passwort wiederholen</label>
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
            </div>
        </form>
    </div>
</div>

<?php
include("./includes/footer.inc.php");
?>