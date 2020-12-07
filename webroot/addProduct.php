<?php
require("./includes/autoLoad.php");
require("includes/sessionChecker.php");

$error = $message = "";
$login_success = true;

$file_isValid = $title_isValid = $description_isValid = $count_isValid = true;
$file_error = $title_error = $descripion_error = $count_error = "";

// maximale filegrösse > 3MB
$maxFileSize = 3048576;
// upload directory > originaldatei
$uploadDirectory = './img/orig/';
// directory für verkleinerstes bild
$imageDirectory = './img/products/';
// directory für thumbnail
$thumbDirectory = './img/thumb/';
// maximale Bildbreite
$maxImageSize = 1000;
// maximale Thumnailbreite
$maxTumbSize = 250;

$userfile = '';
$imageType = '';
$nextId;


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

    if (isset($_POST['submit'])) {
        // Ausgabe von Debugg-Informationen
        echo '<pre>';
        echo 'Debugg-Info $_FILE:';
        print_r($_FILES);
        echo 'Debugg-Info $_POST:';
        print_r($_POST);
        echo '</pre>';

        /*
         * $_Files['userfile']['name'] = ursprüngliche Dateiname beim Benutzer
         * $_Files['userfile']['type'] = MIME-Type des hochgeladenen Datei 
         * $_FILES['userfile']['tmp_name'] = temprärer Pfad und Dateiname auf Server
         * $_FILES['userfile']['error'] = Fehlercode -> http://php.net/manual/de/features.file-upload.errors.php
         * $_FILES['userfile']['size'] = Grösse der Datei in Bytes
        */

        // sind Fehler aufgetreten?
        if ($_FILES['userfile']['error'] != 0) {
            $error .= 'Ein Fehler ist aufgetreten<br />';
            // switch case über die Fehlernummer
            switch ($_FILES['userfile']['error']) {
                case 1:
                    $error .= "Die hochgeladene Datei überschreitet die in der Anweisung upload_max_filesize in php.ini festgelegte Größe.<br />";
                    break;
                case 2:
                    $error .= "Die hochgeladene Datei überschreitet die in dem HTML Formular mittels der Anweisung MAX_FILE_SIZE angegebene maximale Dateigröße.<br />";
                    break;
                case 3:
                    $error .= "Die Datei wurde nur teilweise hochgeladen.<br />";
                    break;
                case 4:
                    $error .= "Es wurde keine Datei hochgeladen.<br />";
                    break;
                case 6:
                    $error .= "Fehlender temporärer Ordner. Eingeführt in PHP 5.0.3.<br />";
                    break;
                case 7:
                    $error .= "Speichern der Datei auf die Festplatte ist fehlgeschlagen. Eingeführt in PHP 5.1.0.<br />";
                    break;
                case 8:
                    $error .= "Eine PHP Erweiterung hat den Upload der Datei gestoppt. PHP bietet keine Möglichkeit an, um festzustellen welche Erweiterung das Hochladen der Datei gestoppt hat. Überprüfung aller geladenen Erweiterungen mittels phpinfo() könnte helfen. Eingeführt in PHP 5.2.0.<br />";
                    break;
            }

            if (!empty($error)) {
                $file_isValid = false;
            }
            // kein fehler
        } else {
            //check filetype
            if (exif_imagetype($_FILES['userfile']['tmp_name']) == IMAGETYPE_GIF) {
                $imageType = 'gif';
            } elseif (exif_imagetype($_FILES['userfile']['tmp_name']) == IMAGETYPE_JPEG) {
                $imageType = 'jpg';
            } elseif (exif_imagetype($_FILES['userfile']['tmp_name']) == IMAGETYPE_PNG) {
                $imageType = 'png';
            } else {
                $error .= "Filetype muss GIF / JPEG / PNG sein.<br />";
            }

            // check file weight
            if (filesize($_FILES['userfile']['tmp_name']) > $maxFileSize) {
                $error .= "Die Datei ist grösser als 1MB.<br />";
            }

            // check file weight
            if (($_FILES['userfile']['size']) > $maxFileSize) {
                $error .= "Die Datei ist grösser als 3MB.<br />";
            }

            // check file size
            list($width, $height, $type, $attr) = getimagesize($_FILES['userfile']['tmp_name']);
            if ($width < $maxImageSize) {
                if ($height < $maxImageSize) {
                    $error .= "Die Datei zu klein. Wählen Sie eine Datei mit mindestens 1000px in Höhe oder Breite.<br />";
                }
            }

            if (!empty($error)) {
                $file_isValid = false;
            }

            // filetype und grösse stimmen
            if (empty($error)) {
                // hier wird das hochgeladene File gespeichert -> relativ zum aktuellen Verzeichnis > Berechtigungen beachten !!!

                // ID für File
                $result = $mysqli->query("SHOW TABLE STATUS LIKE 'item'");
                $data = $result->fetch_assoc();
                $nextId = $data['Auto_increment'];

                echo "<pre>" . $nextId . "</pre>";

                // original Filename
                $fileName = pathinfo($_FILES['userfile']['name'])['extension'];

                // zusammensetzten von Pfad und Filename
                $uploadFile = $uploadDirectory . $nextId . '.' . $fileName;
                echo $uploadFile;
                //verkleinertes bild
                $imageFile = $imageDirectory . $nextId . '.' . $fileName;

                //thumbnail
                $thumbFile = $thumbDirectory . $nextId . '.' . $fileName;

                //verschiebt die temporäre Datei an den richtigen Ort
                if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {

                    // gd image objekt erstellen
                    switch ($imageType) {
                        case 'gif':
                            $original = imagecreatefromgif($uploadFile);
                            break;
                        case 'jpg':
                            $original = imagecreatefromjpeg($uploadFile);
                            break;
                        case 'png':
                            $original = imagecreatefrompng($uploadFile);
                            break;
                    }

                    // breite und höhe
                    $origWidth = imagesx($original);
                    $origHeight = imagesy($original);

                    // hochformat / querformat / quadrat?
                    //querformat
                    if ($origWidth > $origHeight) {
                        // bild ist zu breit und muss heruntergerechnet werden
                        if ($origWidth > $maxImageSize) {
                            // bildbreite und bildhöhe für das verkleinerte Bild berechnen
                            $imageWidth = $maxImageSize;
                            // höhe rechnen: neue Breite / original Breite * original Höhe
                            $imageHeight = ceil($maxImageSize / $origWidth * $origHeight);
                        }
                        //thumbnail
                        $thumbWidth = $thumbHeight = $maxTumbSize;
                        $sourceX = ceil(($origWidth / 2) - ($origHeight / 2));
                        $sourceY = 0;
                        $sourceWidth = $sourceHeight = $origHeight;

                        //hochformat
                    } elseif ($origWidth < $origHeight) {
                        // bild ist zu hoch und muss heruntergerechnet werden
                        if ($origHeight > $maxImageSize) {
                            // breite rechnen: neue höhe / original Höhe * original breite
                            $imageWidth = ceil($maxImageSize / $origHeight * $origWidth);
                            $imageHeight = $maxImageSize;
                        }
                        //thumbnail
                        $thumbWidth = $thumbHeight = $maxTumbSize;
                        $sourceX = 0;
                        $sourceY = ceil(($origHeight / 2) - ($origWidth / 2));
                        $sourceWidth = $sourceHeight = $origWidth;

                        // quadrat
                    } elseif ($origWidth == $origHeight) {
                        if ($origWidth > $maxImageSize) {
                            $imageWidth = $maxImageSize;
                            $imageHeight = $maxImageSize;
                        }
                        //thumbnail
                        $thumbWidth = $thumbHeight = $maxTumbSize;
                        $sourceX = 0;
                        $sourceY = 0;
                        $sourceWidth = $sourceHeight = $origWidth;
                    }
                    // verkleinerstes Bild erstellen > $maxImageSize
                    $image = imagecreatetruecolor($imageWidth, $imageHeight);
                    imagecopyresized($image, $original, 0, 0, 0, 0, $imageWidth, $imageHeight, $origWidth, $origHeight);

                    // thumbnail
                    $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
                    imagecopyresized($thumbnail, $original, 0, 0, $sourceX, $sourceY, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);

                    // bild schreiben
                    switch ($imageType) {
                        case 'gif':
                            imagegif($image, $imageFile);
                            imagegif($thumbnail, $thumbFile);
                            break;
                        case 'jpg':
                            imagejpeg($image, $imageFile, 90);
                            imagejpeg($thumbnail, $thumbFile, 90);
                            break;
                        case 'png':
                            imagepng($image, $imageFile, 1);
                            imagepng($thumbnail, $thumbFile, 1);
                            break;
                    }

                    // bilder löschen
                    imagedestroy($image);
                    imagedestroy($thumbnail);
                    imagedestroy($original);
                    $message .= "Datei ist valide und wurde erfolgreich hochgeladen.<br />";
                } else {
                    $error .= "Datei konnte nicht gespeichert werden.<br />";
                    $file_isValid = false;
                }
            }
        }
    }

    if ($file_isValid && $title_isValid && $description_isValid && $count_isValid) {
        $count = trim(preg_replace('#[^0-9]#i', "", $_POST["count"]));
        $title = htmlspecialchars(trim(($_POST["title"])));
        $description = htmlspecialchars(trim($_POST["description"]));
        $imagePath = (empty($_FILES["userfile"]) || !isset($_FILES["userfile"])) ? "/img/products/1.jpg" : $imageFile;
        $thumbPath = (empty($_FILES["userfile"]) || !isset($_FILES["userfile"])) ? "/img/products/1.jpg" : $thumbFile;

        $query = "INSERT INTO item(`count`, `title`, `description`, `picture`, `thumb`) VALUES (?, ?, ?, ?,?);";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param(
            "issss",
            $count,
            $title,
            $description,
            $imagePath,
            $thumbPath
        );

        if ($stmt->execute()) {
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
        <form enctype="multipart/form-data" method="post">
            <div class="form-file mb-5">
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxFileSize ?>" />
                <input type="file" class="form-file-input" id="userfile" name="userfile" accept="image/*" />
                <label class="form-file-label" for="userfile">
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
            <button type="submit" name="submit" id="submit" class="btn btn-primary btn-block mt-5">
                Produkt hinzufügen
            </button>
        </form>
    </div>
</div>

<?php
include("./includes/footer.inc.php");
?>