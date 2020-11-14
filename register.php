<?php
    require('db.inc'); // Create database connection



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
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center h-100-vh">
            <div class="col-lg-4 col-md-7 col-sm-10 col-12">
                <h1 class="text-center mb-5">Registrieren</h1>
                <form>
                    <div class="text-center mb-3">
                        <!-- Name input -->
                        <div class="row mb-4">
                            <div class="col">
                                <div class="form-outline">
                                    <input type="text" id="register-firstname" class="form-control" />
                                    <label class="form-label" for="register-firstname">Vorname</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-outline">
                                    <input type="text" id="register-lastname" class="form-control" />
                                    <label class="form-label" for="register-lastname">Nachname</label>
                                </div>
                            </div>
                        </div>

                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <input type="email" id="register-email" class="form-control" />
                            <label class="form-label" for="register-email">E-Mail</label>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-4">
                            <input type="password" id="register-password" class="form-control" />
                            <label class="form-label" for="register-password">Passwort</label>
                        </div>

                        <!-- Repeat Password input -->
                        <div class="form-outline mb-4">
                            <input type="password" id="register-password-repeat" class="form-control" />
                            <label class="form-label" for="register-password-repeat">Passwort wiederholen</label>
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