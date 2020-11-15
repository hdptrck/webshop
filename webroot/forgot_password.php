<?php
require("../db.inc");
require("../../pw.inc");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('../PHPMailer/Exception.php');
require('../PHPMailer/PHPMailer.php');
require('../PHPMailer/SMTP.php');

$success = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        if (!empty(trim($_POST['email'])) || filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) { //Is it a valid email?
            if (strlen(htmlspecialchars(trim($_POST['email']))) <= 100) {

                $email = htmlspecialchars(trim($_POST['email']));

                //Check if email is known
                $query = "SELECT * FROM webShopUser WHERE email=?;";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows) { //E-Mail is known
                    $row = $result->fetch_assoc();
                    $result->free();

                    $idUser = $row['idWebShopUser']; //Get id of user which wants to reset password

                    do {
                        $token = bin2hex(random_bytes(128)); //Create random token

                        //Try to select token
                        $query = "SELECT * FROM passwordResetToken WHERE token=?;";
                        $stmt = $mysqli->prepare($query);
                        $stmt->bind_param("s", $token);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } while ($result->num_rows); //Generate new token if token already exists (pretty unlikely though)

                    //Token is only 15 minutes valid
                    $expFormat = mktime(date("H"), date("i") + 15, date("s"), date("m"), date("d"), date("Y"));
                    $expDate = date("Y-m-d H:i:s", $expFormat);

                    //Delete every previous token that user might has created
                    $query = "DELETE FROM passwordResetToken WHERE webShopUser_idWebShopUser=?;";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("i", $idUser);
                    $stmt->execute();

                    //Store Token in database
                    $query = "INSERT INTO passwordResetToken (token, webShopUser_idWebShopUser, expire) VALUES (?, ?, ?);";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("sis", $token, $idUser, $expDate);
                    $stmt->execute();

                    //Create mail content
                    $link = "localhost/reset_password.php?token=" . $token;
                    $output = "<p><a href=\"" . $link . "\">Zurücksetzen</a></p>";
                    $body = $output;
                    $subject = "Passwort zurücksetzen";

                    //Create Mail
                    $email_to = $email;
                    $fromserver = "test@kkb.ch";
                    $mail = new PHPMailer();
                    $mail->IsSMTP();
                    $mail->Host = "smtp.office365.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = "test@kkb.ch";
                    $mail->Password = $password; //password is stored in pw.inc
                    $mail->Port = 587;
                    $mail->IsHTML(true);
                    $mail->From = "test@kkb.ch";
                    $mail->FromName = "Reset";
                    $mail->Sender = $fromserver;
                    $mail->Subject = $subject;
                    $mail->Body = $body;
                    $mail->AddAddress($email_to);
                    if (!$mail->Send()) { //Try to send Mail
                        $error_message = "Sorry, es scheint ein Fehler aufgetreten zu sein. Bitte versuche es später nocheinmal.";
                    } else {
                        $success = true;
                    }
                } else {
                    $result->free();
                }
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
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="Content">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=no">
    <title>Passwort vergessen</title>

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
                <h1 class="text-center mb-5">Passwort vergessen</h1>
                <p class="text-center mb-5">Trage deine E-Mail Adresse ein um das Passwort zurückzusetzen. Anschliessend wird dir ein Link zugeschickt, mit welchem du dein Passwort zurücksetzen kannst.</p>
                <form method="post">
                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <input name="email" type="email" id="email" class="form-control" value="<?php if (isset($_POST["email"]) && !$success) {
                                                                                                    echo $_POST["email"];
                                                                                                } ?>" />
                        <label class="form-label" for="email">E-Mail</label>
                    </div>
                    <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if($success) {
                                echo "<div class=\"valid-feedback\">Wenn uns diese E-Mail-Adresse bekannt ist, so haben wir Dir soeben eine Nachricht mit weiteren Anweisungen geschickt.</div>";
                            } else {
                                echo "<div class=\"invalid-feedback\">" . $error_message . "</div>";
                            }
                        }
                    ?>

                    <!-- Submit button -->
                    <button type="submit" id="email-submit" class="btn btn-primary btn-block mt-5">
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