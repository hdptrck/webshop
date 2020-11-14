<?php


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
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center h-100-vh">
            <div class="col-lg-5 col-md-7 col-sm-10 col-12">
                <h1 class="text-center mb-5">Passwort vergessen</h1>
                <p class="text-center mb-5">Trage deine E-Mail Adresse ein um das Passwort zurückzusetzen. Anschliessend wird dir ein Link zugeschickt, mit welchem du dein Passwort zurücksetzen kannst.</p>
                <form>
                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <input type="email" id="login-name" class="form-control" />
                        <label class="form-label" for="login-name">E-Mail</label>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" id="login-submit" class="btn btn-primary btn-block mt-5">
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