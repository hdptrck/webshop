<?php
require("./includes/autoLoad.php");
require("includes/sessionChecker.php");


$error = "";
$message = "";
$login_success = true;


$file_isValid = true;
$file_error = "";
$title_isValid = true;
$title_error = "";
$description_isValid = true;
$descripion_error = "";
$count_isValid = true;
$count_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check title
    if (!isset($_POST["title"]) || empty(trim($_POST["title"]))) {
        $title_isValid = false;
        $title_error = "Bitte gib einen Titel ein";
    }

    // Check count
    if (!isset($_POST["count"]) || empty(trim(preg_replace('#[^0-9]#i', "", $_POST['count'])))) {
        $count_isValid = false;
        $count_error = "Bitte gib einen Bestand ein";
    }

    // Check length
    // Title
    if ($title_isValid && strlen(trim($_POST["title"])) > 45) {
        $title_isValid = false;
        $title_error = "Der Titel darf nicht länger als 45 Zeichen sein.";
    }

    //Description
    if (isset($_POST["description"]) && strlen(trim($_POST["description"])) > 512) {
        $description_isValid = false;
        $description_error = "Die Beschreibung darf nicht länger als 512 Zeichen sein.";
    }

    if ($file_isValid && $title_isValid && $description_isValid && $count_isValid) {
        $count = trim(preg_replace('#[^0-9]#i', "", $_POST["count"]));
        $title = htmlspecialchars(trim(($_POST["title"])));
        $description = htmlspecialchars(trim($_POST["description"]));
        $file = (empty($_POST["file"]) || !isset($_POST["file"])) ? "/img/products/1.jpg" : $_POST["file"];

        $query = "INSERT INTO item(`count`, `title`, `description`, `picture`) VALUES (?, ?, ?, ?);";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param(
            "isss",
            $count,
            $title,
            $description,
            $file
        );
        
        if($stmt->execute()) {
            $message = "Produkt wurde erfolgreich hinzugefügt";
        } else {
            $error = "Fehler beim einfügen in die Datenbank. Bitte versuche es erneut";
        }
    }
}

?>

<?php
$siteName = "Produkt hinzufügen";

// TODO: Implement shopping cart
$numberOfItems = 2;
include("./includes/header.inc.php");

?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-7 col-sm-10 col-12">
        <?php
        if ($error) {
            echo '<p class="note note-error mb-4">' . $error . '</p>';
        } elseif ($message)
            echo '<p class="note note-success mb-4">' . $message . '</p>';
        ?>
        <form method="post">
            <div class="form-file mb-5">
                <input type="file" class="form-file-input" id="file" name="file" accept="image/*" />
                <label class="form-file-label" for="file">
                    <span class="form-file-text">Bild hinzufügen...</span>
                    <span class="form-file-button">Browse</span>
                </label>
            </div>

            <div class="form-outline mb-5">
                <input type="text" id="title" name="title" class="form-control" maxlength="45" />
                <label class="form-label" for="title">Titel</label>
            </div>

            <div class="form-outline mb-5">
                <textarea type="text" id="description" name="description" class="form-control" rows="4" maxlength="512"></textarea>
                <label class="form-label" for="description">Beschreibung</label>
            </div>

            <div class="form-outline mb-5">
                <input type="number" id="count" name="count" class="form-control" min="1" value="1" />
                <label class="form-label" for="count">Bestand</label>
            </div>

            <!-- Submit button -->
            <button type="submit" id="submit" class="btn btn-primary btn-block mt-5">
                Produkt hinzufügen
            </button>
        </form>
    </div>
</div>

<?php
include("./includes/footer.inc.php");
?>