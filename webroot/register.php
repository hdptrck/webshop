<?php
require("autoLoad.php");

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



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Validation of firstname.
    if (isset($_POST['register-firstname'])) { //Did user enter a firstname?
        if (!empty(trim($_POST['register-firstname'])) || preg_match("#[\p{L}]+$#", trim($_POST['register-firstname']))) { //Firstname contains only unicode letters and no whitespaces?
            if (strlen(htmlspecialchars(trim($_POST['register-firstname']))) <= 45) { //max-length of fistname is 45 characters
                $firstname = htmlspecialchars(trim($_POST['register-firstname'])); //firstname is completly valid
            } else {
                $firstname_isValid = false;
                $firstname_error = "Der Vorname ist zu lang";
            }
        } else {
            $firstname_isValid = false;
            $firstname_error = "Der Vorname ist ungültig";
        }
    } else {
        $firstname_isValid = false;
        $firstname_error = "Bitte gib Deinen Vornamen ein";
    }

    //Validation of lastname. Same as firstname.
    if (isset($_POST['register-lastname'])) {
        if (!empty(trim($_POST['register-lastname'])) || preg_match("#[\p{L}]+$#", trim($_POST['register-lastname']))) {
            if (strlen(htmlspecialchars(trim($_POST['register-lastname']))) <= 45) {
                $lastname = htmlspecialchars(trim($_POST['register-lastname']));
            } else {
                $lastname_isValid = false;
                $lastname_error = "Der Nachname ist zu lang";
            }
        } else {
            $lastname_isValid = false;
            $lastname_error = "Der Nachname ist ungültig";
        }
    } else {
        $lastname_isValid = false;
        $lastname_error = "Bitte gib Deinen Nachnamen ein";
    }

    //Validation of email. Alomst the same as previous validation
    if (isset($_POST['register-email'])) {
        if (!empty(trim($_POST['register-email'])) || filter_var(trim($_POST['register-email']), FILTER_VALIDATE_EMAIL)) { //Is it a valid email?
            if (strlen(htmlspecialchars(trim($_POST['register-email']))) <= 100) {

                $email = htmlspecialchars(trim($_POST['register-email']));

                //Try finde email in DB
                $query = "SELECT * FROM webShopUser WHERE email=?;";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result=$stmt->get_result();
                if ($result->num_rows) { // Email is already in use
                    $email = "";
                    $email_isValid = false;
                    $email_error = "Diese E-Mail-Adresse ist bereits vergeben";
                }
                $result->free();
            } else {
                $email_isValid = false;
                $email_error = "Diese E-Mail-Adresse ist zu lang";
            }
        } else {
            $email_isValid = false;
            $email_error = "Diese E-Mail-Adresse ist ungültig";
        }
    } else {
        $email_isValid = false;
        $email_error = "Bitte gib Deine E-Mail-Adresse ein";
    }

    //Validation of password and repeated password.
    if (isset($_POST['register-password'])) {
        if (!empty(trim($_POST['register-password'])) || preg_match("#(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$#", trim($_POST['register-password']))) {
            if (strlen($_POST['register-password']) <= 256) {
                if (isset($_POST['register-password-repeat'])) {
                    if ($_POST['register-password'] == $_POST['register-password-repeat']) {
                        $password = $_POST['register-password'];
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

    if($firstname_isValid && $lastname_isValid && $email_isValid && $password_isValid && $password_repeat_isValid) { // Everything is valid
        $password = password_hash($password, PASSWORD_DEFAULT); // Generate PW hash

        do {
            $id = bin2hex(random_bytes(128)); //Create random id

            //Try to select id in DB
            $query = "SELECT * FROM webShopUser WHERE idWebShopUser=?;";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
        } while ($result->num_rows); //Generate new id if id already exists (pretty unlikely though)

        $query = "INSERT INTO webShopUser (idWebShopUser, firstname, lastname, email, password) VALUES (?, ?, ?, ?, ?);";
        if (!($stmt2 = $mysqli->prepare($query))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        } else {
            $stmt2->bind_param("sssss", $id, $firstname, $lastname, $email, $password);
            $stmt2->execute();
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
    <title>Registrieren</title>

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
                <h1 class="text-center mb-5">Registrieren</h1>
                <form method="post">
                    <div class="text-center mb-3">
                        <!-- Name input -->
                        <div class="row mb-4">
                            <div class="col">
                                <div class="form-outline">
                                    <input name ="register-firstname" type="text" id="register-firstname" class="form-control <?php if (!$firstname_isValid) {
                                                                                                        echo "is-invalid";
                                                                                                    } ?>" value="<?php if (isset($_POST["register-firstname"])) { echo $_POST["register-firstname"]; }?>" />
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
                                                                                                    } ?>" value="<?php if (isset($_POST["register-lastname"])) { echo $_POST["register-lastname"]; }?>"/>
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
                                                                                        } ?>" value="<?php if (isset($_POST["register-email"])) { echo $_POST["register-email"]; }?>"/>
                            <label class="form-label" for="register-email">E-Mail</label>
                            <?php
                            if (!$email_isValid) {
                                echo '<div class="invalid-feedback">' .
                                    $email_error .
                                    '</div>';
                            }
                            ?>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-4">
                            <input name="register-password" type="password" id="register-password" class="form-control <?php if (!$password_isValid) {
                                                                                                    echo "is-invalid";
                                                                                                } ?>" value="<?php if (isset($_POST["register-password"])) { echo $_POST["register-password"]; }?>"/>
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
                            <input name="register-password-repeat" type="password" id="register-password-repeat" class="form-control is-invalid<?php if (!$password_repeat_isValid) {
                                                                                                            echo "is-invalid";
                                                                                                        } ?>" value="<?php if (isset($_POST["register-password-repeat"])) { echo $_POST["register-password-repeat"]; }?>"/>
                            <label class="form-label" for="register-password-repeat">Passwort wiederholen</label>
                            <div class="invalid-feedback">test</div>
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
                            Registrieren
                        </button>
                        <button type="submit" id="login-submit" class="btn btn-outline-primary btn-block">
                            Anmelden
                        </button>
                </form>
            </div>
        </div>
    </div>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/1.0.0/mdb.min.js"></script>
</body>

</html>