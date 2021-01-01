<?php
require("./includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/adminChecker.php");

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

$imageFile = $thumbFile = '';

$isUpdate = false;
$isUpdateId = "";
$hasImage = true;
$hasSamePicturePath = false;
$samePicturePath = "";
$hasSameThumbPath = false;
$sameThumbPath = "";
$item = [];


if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $isUpdateId = preg_replace('#[^0-9]#i', "", $_GET['id']);
    $isUpdateQuery = "SELECT * FROM item WHERE idItem = ?;";
    $isUpdateStmt = $mysqli->prepare($isUpdateQuery);
    $isUpdateStmt->bind_param("i", $isUpdateId);
    $isUpdateStmt->execute();
    $result = $isUpdateStmt->get_result();

    if ($result->num_rows) {
        $isUpdate = true;
    }

    if ($isUpdate) {
        $item = $result->fetch_assoc();
        if (empty($_POST)) {
            $_POST["title"] = $item["title"];
            $_POST["description"] = $item["description"];
            $_POST["count"] = $item["count"];
            $hasSamePicturePath = true;
            $samePicturePath = $item["picture"];
            $hasSameThumbPath = true;
            $sameThumbPath = $item["thumb"];
            $result->free();
        }
    }
}

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
        // echo '<pre>';
        // echo 'Debugg-Info $_FILE:';
        // print_r($_FILES);
        // echo 'Debugg-Info $_POST:';
        // print_r($_POST);
        // echo '</pre>';

        /*
         * $_Files['userfile']['name'] = ursprüngliche Dateiname beim Benutzer
         * $_Files['userfile']['type'] = MIME-Type des hochgeladenen Datei 
         * $_FILES['userfile']['tmp_name'] = temprärer Pfad und Dateiname auf Server
         * $_FILES['userfile']['error'] = Fehlercode -> http://php.net/manual/de/features.file-upload.errors.php
         * $_FILES['userfile']['size'] = Grösse der Datei in Bytes
        */

        // sind Fehler aufgetreten?
        if ($_FILES['userfile']['error'] != 0) {
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
                    $hasImage = false;
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
        } elseif ($hasImage) {
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

                // original Filename
                $fileName = pathinfo($_FILES['userfile']['name'])['extension'];

                // zusammensetzten von Pfad und Filename
                $uploadFile = $uploadDirectory . $nextId . '.' . $fileName;

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
                    $hasSamePicturePath = false;
                    $hasSameThumbPath = false;
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
        if (empty($_FILES["userfile"]) || !isset($_FILES["userfile"]) && !$hasSamePicturePath) {
            $imagePath = "/img/products/1.jpg";
        } elseif ($hasSamePicturePath) {
            $imagePath = $samePicturePath;
        } else {
            $imagePath = $imageFile;
        }

        if (empty($_FILES["userfile"]) || !isset($_FILES["userfile"]) && !$hasSameThumbPath) {
            $thumbPath = "/img/products/1.jpg";
        } elseif ($hasSameThumbPath) {
            $thumbPath = $sameThumbPath;
        } else {
            $thumbPath = $thumbFile;
        }

        if (!$isUpdate) {
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
                $error = "Fehler beim Einfügen in die Datenbank. Bitte versuche es erneut";
            }
        } else {
            //echo "Update<br>Update ID: $isUpdateId<br>Titel neu: $title<br>Titel alt: " . $item['title'];
            $query = "UPDATE item SET `count` = ?, `title` = ?, `description` = ?, `picture` = ?, `thumb` = ? WHERE idItem = ?;";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param(
                "issssi",
                $count,
                $title,
                $description,
                $imagePath,
                $thumbPath,
                $isUpdateId
            );

            if ($stmt->execute()) {
                echo "execute successful";
                echo var_dump($stmt);
            } else {
                echo "exec problem";
            }
            if ($stmt->affected_rows) {
                $message .= "Produkt wurde erfolgreich geändert";
            } else {
                $error .= "Der Titel konnte nicht angepasst werden. Bitte versuche es erneut";
            }
        }
    }
}

?>

<?php
$siteName = ($isUpdate) ? "Produkt ändern" : "Produkt hinzufügen";;
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
                <input type="text" id="title" name="title" maxlength="45" class="form-control
                <?php if (!$title_isValid) {
                    echo "is-invalid";
                } ?>
                " value="<?php if (isset($_POST["title"])) {
                                echo $_POST["title"];
                            } ?>" />
                <label class="form-label" for="title">Titel</label>
                <?php
                if (!$title_isValid) {
                    echo '<div class="invalid-feedback">' .
                        $title_error .
                        '</div>';
                }
                ?>
            </div>

            <div class="form-outline mb-5">
                <textarea type="text" id="description" name="description" rows="4" maxlength="512" class="form-control
                <?php if (!$description_isValid) {
                    echo "is-invalid";
                } ?>
                "><?php if (isset($_POST["description"])) {
                    echo $_POST["description"];
                } ?></textarea>
                <label class="form-label" for="description">Beschreibung</label>
                <?php
                if (!$description_isValid) {
                    echo '<div class="invalid-feedback">' .
                        $description_error .
                        '</div>';
                }
                ?>
            </div>

            <div class="form-outline mb-5">
                <input type="number" id="count" name="count" min="1" class="form-control
                <?php if (!$count_isValid) {
                    echo "is-invalid";
                } ?>
                " value="<?php if (isset($_POST["count"])) {
                                echo $_POST["count"];
                            } ?>" />
                <label class="form-label" for="count">Bestand</label>
                <?php
                if (!$count_isValid) {
                    echo '<div class="invalid-feedback">' .
                        $count_error .
                        '</div>';
                }
                ?>
            </div>

            <!-- Submit button -->
            <button type="submit" name="submit" id="submit" class="btn btn-primary btn-block mt-5">
                <?php
                echo ($isUpdate) ? "Produkt ändern" : "Produkt hinzufügen";
                ?>
            </button>
        </form>
    </div>
</div>

<?php
include("./includes/footer.inc.php");
?>