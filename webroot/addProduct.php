<?php
require("./includes/autoLoad.php");
require("includes/sessionChecker.php");
require("includes/adminChecker.php");


// Declare var
$error = $message = "";
$login_success = true;

$file_isValid = $title_isValid = $description_isValid = $count_isValid = true;
$file_error = $title_error = $descripion_error = $count_error = "";

// Max filesize > 3MB
$maxFileSize = 3048576;
// Upload directory > original file
$uploadDirectory = './img/orig/';
// Directory for resized image
$imageDirectory = './img/products/';
// Directory for thumbnail
$thumbDirectory = './img/thumb/';
// Max image width
$maxImageSize = 1000;
// Max thumbnail width
$maxTumbSize = 250;

$fileUpload = '';
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

// Check GET parameters for updating a product. If there is no parametres it's a normal adding of a product,  not an update.
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $isUpdateId = preg_replace('#[^0-9]#i', "", $_GET['id']);
    // Select requestet item
    $isUpdateQuery = "SELECT * FROM item WHERE idItem = ?;";
    $isUpdateStmt = $mysqli->prepare($isUpdateQuery);
    $isUpdateStmt->bind_param("i", $isUpdateId);
    $isUpdateStmt->execute();
    $result = $isUpdateStmt->get_result();

    // Check if item exists, if not the link is manipulated
    if ($result->num_rows) {
        $isUpdate = true;
    }

    // If item found
    if ($isUpdate) {
        $item = $result->fetch_assoc();

        if (empty($_POST)) {
            // Set initial values for updating an item
            $_POST["title"] = $item["title"];
            $_POST["description"] = $item["description"];
            $_POST["count"] = $item["count"];
        }

        $hasSamePicturePath = true;
        $samePicturePath = $item["picture"];
        $hasSameThumbPath = true;
        $sameThumbPath = $item["thumb"];
        $result->free();
    }
}

// Check POST Parameters
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

    // Description
    if (isset($_POST["description"]) && strlen(trim($_POST["description"])) > 512) {
        $description_isValid = false;
        $description_error = "Die Beschreibung darf nicht länger als 512 Zeichen sein.";
    }

    if (isset($_POST['submit'])) {
        // Are there errors
        if ($_FILES['fileUpload']['error'] != 0) {
            // Error numbers switch case
            switch ($_FILES['fileUpload']['error']) {
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
                    // If no image was uploaded use the default image
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
            // No error
        } elseif ($hasImage) {
            echo "has image";
            // Check filetype
            if (exif_imagetype($_FILES['fileUpload']['tmp_name']) == IMAGETYPE_GIF) {
                $imageType = 'gif';
            } elseif (exif_imagetype($_FILES['fileUpload']['tmp_name']) == IMAGETYPE_JPEG) {
                $imageType = 'jpg';
            } elseif (exif_imagetype($_FILES['fileUpload']['tmp_name']) == IMAGETYPE_PNG) {
                $imageType = 'png';
            } else {
                $error .= "Filetype muss GIF / JPEG / PNG sein.<br />";
            }

            // Check file weight
            if (filesize($_FILES['fileUpload']['tmp_name']) > $maxFileSize) {
                $error .= "Die Datei ist grösser als 1MB.<br />";
            }

            // Check file weight
            if (($_FILES['fileUpload']['size']) > $maxFileSize) {
                $error .= "Die Datei ist grösser als 3MB.<br />";
            }

            // Check file size
            list($width, $height, $type, $attr) = getimagesize($_FILES['fileUpload']['tmp_name']);
            if ($width < $maxImageSize) {
                if ($height < $maxImageSize) {
                    $error .= "Die Datei zu klein. Wählen Sie eine Datei mit mindestens 1000px in Höhe oder Breite.<br />";
                }
            }

            if (!empty($error)) {
                $file_isValid = false;
            }

            // Filetype und grösse stimmen
            if (empty($error)) {
                // IMPORTANT: The file will be saved in your directory here. If you clone this repo check the privileges of the directory

                // ID for the file
                $result = $mysqli->query("SHOW TABLE STATUS LIKE 'item'");
                $data = $result->fetch_assoc();
                $nextId = $data['Auto_increment'];

                // Original filename
                $fileName = pathinfo($_FILES['fileUpload']['name'])['extension'];

                // Create filename
                $uploadFile = $uploadDirectory . $nextId . '.' . $fileName;

                // Resized image
                $imageFile = $imageDirectory . $nextId . '.' . $fileName;

                // Thumbnail
                $thumbFile = $thumbDirectory . $nextId . '.' . $fileName;

                // Move the temporary file to the correct directory
                if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $uploadFile)) {

                    // create image object
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

                    // Widht and height
                    $origWidth = imagesx($original);
                    $origHeight = imagesy($original);

                    // portrait  / landscape / square?
                    //landscape
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

                        // portrait 
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

                        // square
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
                    // create resized image > $maxImageSize
                    $image = imagecreatetruecolor($imageWidth, $imageHeight);
                    imagecopyresized($image, $original, 0, 0, 0, 0, $imageWidth, $imageHeight, $origWidth, $origHeight);

                    // thumbnail
                    $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
                    imagecopyresized($thumbnail, $original, 0, 0, $sourceX, $sourceY, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);

                    // Write image
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

                    // Delete image
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

    // Check all inputs
    if ($file_isValid && $title_isValid && $description_isValid && $count_isValid) {
        // Set the values for sql statement
        $count = trim(preg_replace('#[^0-9]#i', "", $_POST["count"]));
        $title = htmlspecialchars(trim(($_POST["title"])));
        $description = htmlspecialchars(trim($_POST["description"]));
        if (!$hasImage && !$hasSamePicturePath) {
            $imagePath = "/img/products/1.jpg";
        } elseif ($hasSamePicturePath) {
            $imagePath = $samePicturePath;
        } else {
            $imagePath = $imageFile;
        }

        // Use default images when no image was uploaded
        if (!$hasImage && !$hasSameThumbPath) {
            echo "placeholder";
            $thumbPath = "/img/products/1.jpg";
        } elseif ($hasSameThumbPath) {
            echo "same thumb path";
            $thumbPath = $sameThumbPath;
        } else {
            echo "new file";
            $thumbPath = $thumbFile;
        }

        // Create new product
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

            // Check if query was successful
            if ($stmt->execute()) {
                $message = "Produkt wurde erfolgreich hinzugefügt";
                unset($_POST);
            } else {
                $error = "Fehler beim Einfügen in die Datenbank. Bitte versuche es erneut";
            }
        } else {
            // Update an existing product
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

            $stmt->execute();

            // Check if query was successful
            if ($stmt->affected_rows) {
                $message .= "Produkt wurde erfolgreich geändert";
            } else {
                $error .= "Das Produkt konnte nicht angepasst werden. Bitte versuche es erneut";
            }
        }
    }
}

// Include header
$siteName = ($isUpdate) ? "Produkt ändern" : "Produkt hinzufügen";;
include("./includes/header.inc.php");

?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-12">
        <?php
        if ($error) {
            echo '<p class="note note-danger mb-5">' . $error . '</p>';
        } elseif ($message)
            echo '<p class="note note-success mb-5">' . $message . '</p>';
        ?>
        <form enctype="multipart/form-data" method="post">
            <!-- File upload -->
            <div class="form-file mb-3">
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxFileSize ?>" />
                <input type="file" class="form-file-input" id="fileUpload" name="fileUpload" accept="image/*" />
                <label class="form-file-label" for="fileUpload">
                    <span id="fileUploadLabel" class="form-file-text">Bild hinzufügen...</span>
                    <span class="form-file-button">Browse</span>
                </label>
            </div>

            <!-- Title -->
            <div class="form-outline <?php echo (!$title_isValid) ? "mb-5" : "mb-3"; ?>">
                <input type="text" id="title" name="title" maxlength="45" class="form-control
                <?php echo (!$title_isValid) ? "is-invalid" : ""; ?>" value="<?php echo (isset($_POST["title"])) ? $_POST["title"] : ""; ?>" required />
                <label class="form-label" for="title">Titel</label>
                <?php
                if (!$title_isValid) {
                    echo '<div class="invalid-feedback">' . $title_error . '</div>';
                }
                ?>
            </div>

            <!-- Description -->
            <div class="form-outline <?php echo (!$description_isValid) ? "mb-5" : "mb-3"; ?>">
                <textarea id="description" name="description" rows="4" maxlength="512" class="form-control
                <?php echo (!$description_isValid) ? "is-invalid" : ""; ?>"><?php echo (isset($_POST["description"])) ? $_POST["description"] : ""; ?></textarea>
                <label class="form-label" for="description">Beschreibung</label>
                <?php
                if (!$description_isValid) {
                    echo '<div class="invalid-feedback">' . $description_error . '</div>';
                }
                ?>
            </div>

            <!-- Count -->
            <div class="form-outline mb-5">
                <input type="number" id="count" name="count" min="1" class="form-control
                <?php echo (!$count_isValid) ? "is-invalid" : ""; ?>" value="<?php echo (isset($_POST["count"])) ? $_POST["count"] : ""; ?>" required />
                <label class="form-label" for="count">Bestand</label>
                <?php
                if (!$count_isValid) {
                    echo '<div class="invalid-feedback">' . $count_error . '</div>';
                }
                ?>
            </div>

            <!-- Submit button -->
            <button type="submit" name="submit" id="submit" class="btn btn-primary btn-block">
                <?php
                echo ($isUpdate) ? "Produkt ändern" : "Produkt hinzufügen";
                ?>
            </button>
        </form>
    </div>
</div>

<script>
    // Declare file upload input and label
    const inputFileUpload = document.getElementById('fileUpload');
    const labelFileUpload = document.getElementById('fileUploadLabel');

    // Sets the filename to the label
    const showFileName = (event) => {
        let inputFileUpload = event.srcElement;
        let fileName = inputFileUpload.files[0].name;
        labelFileUpload.innerText = fileName;
    }

    // Add eventlistener to file upload input
    inputFileUpload.addEventListener('change', showFileName);
</script>

<?php
include("./includes/footer.inc.php");
?>