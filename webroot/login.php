<?php


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
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center h-100-vh">
            <div class="col-lg-4 col-md-7 col-sm-10 col-12">
                <h1 class="text-center mb-5">Anmelden</h1>
                <form>
                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <input type="email" id="login-name" class="form-control" />
                        <label class="form-label" for="login-name">E-Mail</label>
                    </div>

                    <!-- Password input -->
                    <div class="form-outline mb-4">
                        <input type="password" id="login-password" class="form-control" />
                        <label class="form-label" for="login-password">Password</label>
                    </div>

                    <!-- 2 column grid layout -->
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex justify-content-center">
                            <!-- Checkbox -->
                            <div class="form-check mb-3 mb-md-0">
                                <input class="form-check-input" type="checkbox" value="" id="login-check" checked />
                                <label class="form-check-label" for="login-check">
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