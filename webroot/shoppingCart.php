<?php
require("./includes/autoLoad.php");
require("includes/sessionChecker.php");

$name_isValid = true;
$name_error = "";
$startDate_isValid = true;
$startDate_error = "";
$startTime_isValid = true;
$startTime_error = "";
$endDate_isValid = true;
$endDate_error = "";
$endTime_isValid = true;
$endTime_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'])) {

    } else {
        $name_isValid = false;
        $name_error = "Bitte gib deinem Anlass einen Namen.";
    }
}
?>

<?php
$siteName = "Warenkorb";

// TODO: Implement shopping cart
$numberOfItems = 2;
include("./includes/header.inc.php");

?>
<form method="post"> <!--Anlassort & Bereitstellungsort noch hinzufügen.-->
    <label>Anlassname</label><br />
    <input type="text" name="name" maxlength="45" required />
    <label>Abholdatum und -zeit</label><br />
    <input class="start" type="date" id="start_date" name="trip-start" value="<?php echo date("Y-m-d", strtotime("next saturday")) ?>" min="<?php echo date("Y-m-d", strtotime("now")) ?>" required />
    <input class="start" type="time" id="start_time" name="appt" value="12:00" required /><br />
    <label>Zurückbringdatum und -zeit</label><br />
    <input class="end" type="date" id="end_date" name="trip-start" value="<?php echo date("Y-m-d", strtotime("next saturday")) ?>" min="<?php echo date("Y-m-d", strtotime("now")) ?>" required />
    <input class="start" type="time" id="end_time" name="appt" value="19:00" min="12:00" required /><br />

    <?php
    if (isset($_SESSION["shoppingCart"]) and is_array($_SESSION["shoppingCart"]) and !empty($_SESSION["shoppingCart"])) {
        $shoppingCart = $_SESSION["shoppingCart"];
        echo "<ul>";
        foreach ($shoppingCart as $item) {
            $query = "SELECT * FROM item WHERE idItem=?;";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $item["id"]);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo '<li>';
                //Add display of picture
                echo '<a href="detail.php?id=' . $row["idItem"] . '">' . $row["title"] . '</a>';
                echo '<input id="' . $row["idItem"] . '" name="number" type="number" min="1" value="' . $item["count"] . '" aria-label="Search" class="form-control mr-2 number" style="width: 100px" required/>';
                echo '<button id="' . $row["idItem"] . '" class="remove">Löschen</button>';
                echo '</li>';
            }
        }
        echo "</ul>";
    } else {
        //TODO: Nice view of no Items
        echo "Noch keine Dinge im Warenkorb";
    }
    ?>
    <button type="submit" id="shoppingcart-submit" class="btn btn-primary btn-block mt-5" <?php if (!isset($shoppingCart)) {
                                                                                                echo 'disabled';
                                                                                            }  ?>>
        Bestellung abschicken
    </button>

</form>

<script>
    //https://stackoverflow.com/questions/41946457/getting-text-from-fetch-response-object
    async function getTextFromStream(readableStream) {
        let reader = readableStream.getReader();
        let utf8Decoder = new TextDecoder();
        let nextChunk;

        let resultStr = '';

        while (!(nextChunk = await reader.read()).done) {
            let partialData = nextChunk.value;
            resultStr += utf8Decoder.decode(partialData);
        }

        return resultStr;
    }

    async function callHandler(formData) {
        const url = 'shoppingCartHandler.php';
        let res;
        await fetch(url, {
            method: 'POST',
            body: formData
        }).then(response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                res = response.json();
            } else {
                res = response.text();
            }
        });
        return res;
    }

    async function updatedTimeSpan() {
        var start_date = document.getElementById('start_date');
        var start_time = document.getElementById('start_time');
        var end_date = document.getElementById('end_date');
        var end_time = document.getElementById('end_time');

        var formData = new FormData();
        formData.append('changeTime', '');
        formData.append('startDate', start_date.value);
        formData.append('startTime', start_time.value);
        formData.append('endDate', end_date.value);
        formData.append('endTime', end_time.value);
        feedback = await callHandler(formData);

        let warnings = document.getElementsByClassName('warning');

        Array.prototype.forEach.call(warnings, function(warning) {
            warning.remove();
        });

        let elements = document.getElementsByClassName('number');

        feedback.forEach(function(item) {
            Array.prototype.forEach.call(elements, function(element) {
                if (element.id == item.id) {
                    element.setAttribute('max', item.max);
                    if (element.value > item.max) {
                        let child = element.parentNode.appendChild(document.createElement('p'));
                        child.classList.add('warning');
                        if (item.max == 0) {
                            child.innerHTML = 'Achtung, zum gewähltem Zeitraum sind keine Gegenstände verfügbar!';
                        } else if (item.max == 1) {
                            child.innerHTML = 'Achtung, zum gewähltem Zeitraum ist nur ein Gegenstand verfügbar!';
                        } else {
                            child.innerHTML = 'Achtung, zum gewähltem Zeitraum sind nur '.concat(item.max).concat(' Gegenstände verfügbar!');
                        }

                    }
                }
            });
        });


    }

    var btn = document.getElementsByClassName('remove')

    for (var i = 0; i < btn.length; i++) {
        btn[i].addEventListener('click', async function(e) {
            e.currentTarget.parentNode.remove();

            var formData = new FormData();
            formData.append('remove', '');
            formData.append('id', e.currentTarget.id);
            feedback = await callHandler(formData);

            if (Object.keys(feedback).length == 0) {
                let btn = document.getElementById('shoppingcart-submit');
                btn.setAttribute('disabled', '');
            }
        }, false);
    }

    var ipt = document.getElementsByClassName('number')

    for (var i = 0; i < ipt.length; i++) {
        ipt[i].addEventListener('change', async function(e) {
            var formData = new FormData();
            formData.append('changeCount', '');
            formData.append('id', e.currentTarget.id);
            formData.append('count', e.currentTarget.value);
            feedback = await callHandler(formData);

            let elements = document.getElementsByClassName('number');

            Array.prototype.forEach.call(elements, function(element) {
                let itemExists = false;
                feedback.forEach(function(item) {
                    if (element.id == item.id) {
                        element.value = item.count;
                        itemExists = true;
                    }
                });
                if (!itemExists) {
                    element.parentNode.remove();
                }
            });

        }, false);
    }

    var starts = document.getElementsByClassName('start');

    for (var i = 0; i < starts.length; i++) {
        starts[i].addEventListener('change', function(e) {
            var start_date = document.getElementById('start_date');
            var start_time = document.getElementById('start_time');
            var end_date = document.getElementById('end_date');
            var end_time = document.getElementById('end_time');

            end_date.min = start_date.value;
            if (start_date.value == end_date.value) {
                end_time.min = start_time.value;

                if (start_time.value > end_time.value) {
                    end_time.value = start_time.value;
                }

            } else {
                end_time.min = "";
            }

            if (start_date.value > end_date.value) {
                end_date.value = start_date.value;
            }

            updatedTimeSpan();

        }, false);
    }

    var ends = document.getElementsByClassName('end');

    for (var i = 0; i < ipt.length; i++) {
        ends[i].addEventListener('change', updatedTimeSpan(), false);
    }
</script>

<?php
include("./includes/footer.inc.php");
?>