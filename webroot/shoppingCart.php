<?php
require("./includes/autoLoad.php");
require("./includes/sessionChecker.php");
require("./includes/itemChecker.inc.php");

//All triggers and messages for server-side validation
$eventName_isValid = true;
$eventName_error = "";
$eventPlace_isValid = true;
$eventPlace_error = "";
$startDate_isValid = true;
$startDate_error = "";
$startTime_isValid = true;
$startTime_error = "";
$endDate_isValid = true;
$endDate_error = "";
$endTime_isValid = true;
$endTime_error = "";
$orderLocation_isValid = true;
$orderLocation_error = "";
$items_exist = true;
$items_error = "";
$itemsMax_inLimit = true;

//Selects orderLocations whitch later get filled in dropdown
$query = "SELECT * FROM orderLocation;";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$orderLocations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['eventName'])) {
        if (!empty(trim($_POST['eventName']))) { // Not only whitespaces?
            if (strlen(htmlspecialchars(trim($_POST['eventName']))) >= 45) { // Max length which can get filled in db
                $eventName_isValid = false;
                $eventName_error = "Der Name deines Anlasses ist zu lang.";
            }
        } else {
            $eventName_isValid = false;
            $eventName_error = "Bitte gib deinem Anlass einen gültigen Namen.";
        }
    } else {
        $eventName_isValid = false;
        $eventName_error = "Bitte gib deinem Anlass einen Namen.";
    }

    // Same validation as eventName
    if (isset($_POST['eventPlace'])) {
        if (!empty(trim($_POST['eventPlace']))) {
            if (strlen(htmlspecialchars(trim($_POST['eventPlace']))) >= 45) {
                $eventPlace_isValid = false;
                $eventPlace_error = "Die Ortsangabe deines Anlasses ist zu lang.";
            }
        } else {
            $eventPlace_isValid = false;
            $eventPlace_error = "Bitte gib einen gültigen Ort ein.";
        }
    } else {
        $eventPlace_isValid = false;
        $eventPlace_error = "Bitte teile uns mit, wo der Anlass stattfindet.";
    }

    // Validation of startdate
    if (isset($_POST['start_date'])) {
        if (empty(trim($_POST['start_date'])) || !preg_match("#\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$#", htmlspecialchars(trim($_POST['start_date'])))) { // Valid date?
            $startDate_isValid = false;
            $startDate_error = "Bitte gib ein gültiges Abholdatum ein.";
        }
    } else {
        $startDate_isValid = false;
        $startDate_error = "Bitte teile uns mit, wann das Material abgeholt wird.";
    }

    // Same validation as startdate exepts it's time
    if (isset($_POST['start_time'])) {
        if (empty(trim($_POST['start_time'])) || !preg_match("#([0-1]?[0-9]|2[0-3]):[0-5][0-9]$#", htmlspecialchars(trim($_POST['start_time'])))) {
            $startTime_isValid = false;
            $startTime_error = "Bitte gib eine gültige Abholzeit ein.";
        }
    } else {
        $startTime_isValid = false;
        $startTime_error = "Bitte teile uns mit, wann das Material abgeholt wird.";
    }

    // Same validation as enddate
    if (isset($_POST['end_date'])) {
        if (empty(trim($_POST['end_date'])) || !preg_match("#\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$#", htmlspecialchars(trim($_POST['end_date'])))) {
            $endDate_isValid = false;
            $endDate_error = "Bitte gib ein gültiges Zurückbringdatum ein.";
        }
    } else {
        $endDate_isValid = false;
        $endDate_error = "Bitte teile uns mit, wann das Material zurückgebracht wird.";
    }

    // Same validation as starttime
    if (isset($_POST['end_time'])) {
        if (empty(trim($_POST['end_time'])) || !preg_match("#([0-1]?[0-9]|2[0-3]):[0-5][0-9]$#", htmlspecialchars(trim($_POST['end_time'])))) {
            $endTime_isValid = false;
            $endTime_error = "Bitte gib eine gültige Zurückbringzeit ein.";
        }
    } else {
        $endTime_isValid = false;
        $endTime_error = "Bitte teile uns mit, wann das Material zurückgebracht wird.";
    }

    // Validation of orderLocation
    if (isset($_POST['orderLocation'])) {
        // Select all orderLocations from DB
        $query = "SELECT idOrderLocation FROM orderLocation;";
        $stmt = $mysqli->prepare($query);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $betterresults = array();

        foreach ($results as $result) { // Puts only IDs in simple array
            $betterresults[] = $result['idOrderLocation'];
        }

        if (!in_array($_POST['orderLocation'], $betterresults)) { // Selected id is present in DB
            $orderLocation_isValid = false;
            $orderLocation_error = "Dieser Bereitstellungsort steht nicht zur Auswahl.";
        }
    }

    // All dates and times are possible dates
    if ($startDate_isValid && $startTime_isValid && $endDate_isValid && $endTime_isValid) {
        $startDateTime = DateTime::createFromFormat("Y-m-d H:i", $_POST['start_date'] . " " . $_POST['start_time']);
        $endDateTime = DateTime::createFromFormat("Y-m-d H:i", $_POST['end_date'] . " " . $_POST['end_time']);

        if ($startDateTime >= $endDateTime) { // enddatetime is before startdatetime
            $endDate_isValid = true;
            $endDate_error = "Die Dauer der Ausleihe ist ungültig.";
        }

        if (isset($_POST['id']) and isset($_POST['number']) and is_array($_POST['id']) and is_array($_POST['number']) and count($_POST['id']) > 0 and count($_POST['number']) > 0) { // Are items present in shopping cart?
            $ids = $_POST['id'];
            $numbers = $_POST['number'];
            if (count($ids) == count($numbers)) { // Theese values have to be the same, because there should be the same amount of html-elements. Under normal conditions there shouldn't be a difference
                $items = array();
                foreach ($ids as $key => $id) { // Creates one array out of two
                    $items[] = array('id' => $id, 'count' => $numbers[$key]);
                }
                $startStr = $startDateTime->format("Y-m-d H:i");
                $endStr = $endDateTime->format("Y-m-d H:i");
                $maxs = checkItemsInTimeSpan($mysqli, $startStr, $endStr, $items); // Function present in itemChecker.inc.php / returns maximum avaiable quantity for selected timespan

                foreach ($items as &$item) {
                    $limit = 0;
                    foreach ($maxs as $max) { // Searches maximum quanitity in checkItemsInTimeSpan's returned array for $item
                        if ($max['id'] == $item['id']) {
                            $limit = $max['max'];
                            break;
                        }
                    }
                    if ($item['count'] > $limit) { // If choosen quantity is bigger than maximum available amount
                        $itemsMax_inLimit = false;
                        $item['msg'] = "Die maximale Anzahl dieses Gegenstandes für den gewählten Zeitraum ist überschritten."; // Add message to $item
                    }
                }
            } else {
                $items_exist = false;
                $items_error = "Irgendetwas scheint hier nicht zu stimmen. Bitte lade die Seite neu und versuche es nochmal.";
            }
        } else {
            $items_exist = false;
            $items_error = "Es befinden sich keine Gegenstände im Warenkorb.";
        }

        if ($eventName_isValid && $eventPlace_isValid && $orderLocation_isValid && $items_exist && $itemsMax_inLimit) { // Is everything valid?
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enables error-throwing of mysqli
            $mysqli->begin_transaction();
            $orderSuccessful = false;
            try { // Try putting order into DB
                $query = "INSERT INTO tbl_order (webShopUser_idWebShopUser, eventName, eventPlace, pickUpDatetime, returnDatetime, orderLocation_idOrderLocation, isReady, isReturned) VALUES (?, ?, ?, ?, ?, ?, false, false);";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("issssi", $_SESSION['userId'], $_POST['eventName'], $_POST['eventPlace'], $startStr, $endStr, $_POST['orderLocation']);
                $stmt->execute();

                $orderId = $mysqli->insert_id; // Get id of inserted order

                $query = "INSERT INTO order_has_item VALUES (?, ?, ?);";
                $stmt = $mysqli->prepare($query);
                foreach ($items as $i) { // Put every ordered item into DB
                    $stmt->bind_param("iii", $orderId, $i['id'], $i['count']);
                    $stmt->execute();
                }

                $mysqli->commit();
                $orderSuccessful = true;
            } catch (mysqli_sql_exception $exception) { // Something failed while putting order into DB
                $mysqli->rollback();
                echo "<br><br><br>Abschicken der Bestellung fehlgeschlagen. Bitte versuche es erneut.";
                echo $exception;
            }
            if ($orderSuccessful) { // Was order put into DB?
                // Clear session values, because order was sent
                if (isset($_SESSION['shoppingCart'])) {
                    unset($_SESSION['shoppingCart']);
                }
                if (isset($_SESSION['timeSpan'])) {
                    unset($_SESSION['timeSpan']);
                }
                if (isset($_SESSION['orderInfos'])) {
                    unset($_SESSION['orderInfos']);
                }
                header("Location: shop.php");
            }
        }
    }
}

// Include header
$siteName = "Warenkorb";
include("./includes/header.inc.php");
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10 col-sm-10 col-12">
        <form method="post">
            <div class="form-outline mb-5">
                <input type="text" id="eventName" name="eventName" maxlength="45" class="orderInfo form-control" required <?php
                                                                                                                            if (isset($_SESSION['orderInfos']['eventName'])) { // Maybe there is a name already present in session?
                                                                                                                                echo 'value="' . $_SESSION['orderInfos']['eventName'] . '"';
                                                                                                                            }
                                                                                                                            ?> />
                <label class="form-label" for="eventName">Anlassname</label>
                <?php
                if (!$eventName_isValid) { // Possible server-side validation violation?
                    echo '<div class="invalid-feedback">'  . $eventName_error . '</div>';
                }
                ?>
            </div>

            <div class="form-outline mb-5">
                <input type="text" id="eventPlace" name="eventPlace" maxlength="45" class="orderInfo form-control" required <?php if (isset($_SESSION['orderInfos']['eventPlace'])) { // Maybe there is a place already present in session?
                                                                                                                                echo 'value="' . $_SESSION['orderInfos']['eventPlace'] . '"';
                                                                                                                            } ?> />
                <label class="form-label" for="eventPlace">Anlassort</label>
                <?php
                if (!$eventPlace_isValid) { // Possible server-side validation violation?
                    echo '<div class="invalid-feedback">'  . $eventPlace_error . '</div>';
                }
                ?>
            </div>

            <div class="row mb-5">
                <div class="col-md-6 col-12">
                    <label class="form-label" for="start_date">Abholdatum und -zeit</label>
                    <div class="row">
                        <div class="col mb-md-3">
                            <div class="form-outline">
                                <input class="start form-control" type="date" id="start_date" name="start_date" required value="<?php
                                                                                                                                if (isset($_SESSION['timeSpan']['start'])) { // Maybe there is a startdate already present in session?
                                                                                                                                    echo (DateTime::createFromFormat("d.m.Y H:i", $_SESSION['timeSpan']['start'])->format("Y-m-d"));
                                                                                                                                } else {
                                                                                                                                    echo date("Y-m-d", strtotime("next saturday"));
                                                                                                                                }
                                                                                                                                ?>" min="<?php echo date("Y-m-d", strtotime("now")) ?>" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-outline">
                                <input class="start form-control" type="time" id="start_time" name="start_time" value="<?php if (isset($_SESSION['timeSpan']['start'])) { // Maybe there is a starttime already present in session?
                                                                                                                            echo DateTime::createFromFormat("d.m.Y H:i", $_SESSION['timeSpan']['start'])->format("H:i");
                                                                                                                        } else {
                                                                                                                            echo "12:00";
                                                                                                                        } ?>" required />
                                <?php if (!$startDate_isValid) { // Possible server-side validation violation?
                                    echo '<div class="invalid-feedback">' . $startDate_error . '</div>';
                                }
                                if (!$startTime_isValid) { // Possible server-side validation violation?
                                    echo '<div class="invalid-feedback">' . $startTime_error . '</div>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-12 mt-md-0 mt-3">
                    <label class="form-label" for="end_date">Zurückbringdatum und -zeit</label>
                    <div class="row">
                        <div class="col mb-md-3">
                            <div class="form-outline">
                                <input class="end form-control" type="date" id="end_date" name="end_date" required value="<?php if (isset($_SESSION['timeSpan']['end'])) { // Maybe there is a enddate already present in session?
                                                                                                                                echo DateTime::createFromFormat("d.m.Y H:i", $_SESSION['timeSpan']['end'])->format("Y-m-d");
                                                                                                                            } else {
                                                                                                                                echo date("Y-m-d", strtotime("next saturday"));
                                                                                                                            } ?>" min="<?php echo date("Y-m-d", strtotime("now")) ?>" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-outline">
                                <input class="end form-control" type="time" id="end_time" name="end_time" required value="<?php if (isset($_SESSION['timeSpan']['end'])) { // Maybe there is a endtime already present in session?
                                                                                                                                echo DateTime::createFromFormat("d.m.Y H:i", $_SESSION['timeSpan']['end'])->format("H:i");
                                                                                                                            } else {
                                                                                                                                echo "19:00";
                                                                                                                            } ?>" min="12:00" />
                                <?php if (!$endDate_isValid) { // Possible server-side validation violation?
                                    echo '<div class="invalid-feedback">' . $endDate_error . '</div>';
                                }
                                if (!$endTime_isValid) { // Possible server-side validation violation?
                                    echo '<div class="invalid-feedback">' . $endTime_error . '</div>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <label class="form-label" for="orderLocation">Bereitstellungsort</label><br />
            <div class="form-outline mb-5">
                <select id="orderLocation" name="orderLocation" required class="orderInfo form-control">
                    <option value="">Auswählen...</option>
                    <?php
                    foreach ($orderLocations as $location) { // Foreach orderLocation which was selected in DB earlier there will be an option
                        $selected = "";
                        if (isset($_SESSION['orderInfos']['orderLocation']) and $_SESSION['orderInfos']['orderLocation'] == $location['idOrderLocation']) { // Is one option already selected in session? Will be selected again
                            $selected = "selected";
                        }
                        echo '<option value="' . $location['idOrderLocation'] . '" ' . $selected . '>' . $location['name'] . '</option>';
                    }
                    ?>
                </select>
                <?php if (!$orderLocation_isValid) { // Possible server-side validation violation?
                    echo '<div class="invalid-feedback">' . $orderLocation_error . '</div>';
                } ?>

                <?php if (!$items_exist) { // Possible server-side validation violation?
                    echo '<div class="invalid-feedback">' . $items_error . '</div>';
                } ?>
            </div>

            <?php
            if (isset($_SESSION['shoppingCart']) and is_array($_SESSION['shoppingCart']) and !empty($_SESSION['shoppingCart'])) { // Are there even items in shopping cart?
                $shoppingCart = $_SESSION['shoppingCart'];
                echo '<div class="row">';
                foreach ($shoppingCart as $item) { // Every items will be displayed as listitem
                    $query = "SELECT * FROM item WHERE idItem=?;";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("s", $item['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) { // Is ID valid?
                        $row = $result->fetch_assoc();
                        echo '<div class="col-12">';
                        //TODO:Add display of picture
                        echo '<a href="detail.php?id=' . $row['idItem'] . '"><h4>' . $row['title'] . '</h4></a>';
                        echo '<input name="id[]" type="hidden" value="' . $row['idItem'] . '" class="hidden"/>'; // Hidden input, so item-ID is present in post
                        echo '<input name="number" type="number" min="1" value="' . $item['count'] . '" aria-label="Search" class="number form-control float-left" style="width: 100px" required/>';
                        if (isset($items) and is_array($items)) {
                            foreach ($items as $item) {
                                if ($item['id'] == $row['idItem'] and isset($item['msg'])) { // Display possible error from server-side validation
                                    echo '<div class="invalid-feedback">' . $item['msg'] . '</div>';
                                }
                            }
                        }
                        echo '<button id="' . $row["idItem"] . '" class="remove float-right btn btn-danger btn-delete-padding"><span class="material-icons-outlined">delete</span></button>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            } else {
                //TODO: Nice view of no Items
                echo "Noch keine Dinge im Warenkorb";
            }
            ?>

            <!-- Submit button -->
            <button type="submit" id="shoppingcart-submit" class="btn btn-primary btn-block mt-5" <?php if (!isset($shoppingCart)) { // Disable button when there are no items
                                                                                                        echo "disabled";
                                                                                                    }  ?>>
                Bestellung abschicken
            </button>
        </form>
    </div>
</div>

<script>
    async function callHandler(formData) { // Function which calls php-handler
        const url = "shoppingCartHandler.php";
        let res;
        await fetch(url, {
            method: "POST",
            body: formData
        }).then(response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) { // Was a JSON returned?
                res = response.json();
            } else {
                res = response.text();
            }
        });
        return res;
    }

    async function updatedTimeSpan() { // Send timespan to backend and process feedback
        let start_date = document.getElementById("start_date");
        let start_time = document.getElementById("start_time");
        let end_date = document.getElementById("end_date");
        let end_time = document.getElementById("end_time");

        let formData = new FormData();
        formData.append('action', "changeTime");
        formData.append('startDate', start_date.value);
        formData.append('startTime', start_time.value);
        formData.append('endDate', end_date.value);
        formData.append('endTime', end_time.value);
        feedback = await callHandler(formData); // Tells server the new timespan and waits for feedback

        let warnings = document.getElementsByClassName("warning");

        Array.prototype.forEach.call(warnings, function(warning) { // Remove all warnings 
            warning.remove();
        });

        if (typeof feedback == "object") { // Is feedback JSON?
            let elements = document.getElementsByClassName("number");

            feedback.forEach(function(item) {
                Array.prototype.forEach.call(elements, function(element) {
                    if (element.id == item.id) { // Match item-ID to fitting element-ID
                        element.setAttribute("max", item.max); // Set new max
                        if (element.value > item.max) { // Is there too much count than actually possible?
                            // Create warning
                            let child = element.parentNode.appendChild(document.createElement("p"));
                            child.setAttribute('count', item.max);
                            child.classList.add("warning");
                            switch (item.max) {
                                case 0:
                                    child.innerHTML = "Achtung, zum gewählten Zeitraum sind keine Gegenstände verfügbar!";
                                    break;
                                case 1:
                                    child.innerHTML = "Achtung, zum gewählten Zeitraum ist nur ein Gegenstand verfügbar!";
                                    break;
                                default:
                                    child.innerHTML = "Achtung, zum gewählten Zeitraum sind nur ".concat(item.max).concat(" Gegenstände verfügbar!");
                            }

                            // Disable send-button, because there is a warning present
                            let btn = document.getElementById("shoppingcart-submit");
                            btn.setAttribute('disabled', "");
                        }
                    }
                });
            });
        }
    }

    let orderInfos = document.getElementsByClassName("orderInfo");
    Array.prototype.forEach.call(orderInfos, function(orderInfo) { // Some basic orderInfo got changed and everything will be sent to backend
        orderInfo.addEventListener("change", function(e) {
            var eventName = document.getElementById("eventName");
            var eventPlace = document.getElementById("eventPlace");
            var orderLocation = document.getElementById("orderLocation");

            var formData = new FormData();
            formData.append('action', "changeOrderInfos");
            formData.append('eventName', eventName.value);
            formData.append('eventPlace', eventPlace.value);
            formData.append('orderLocation', orderLocation.value);
            callHandler(formData);
        }, false);
    });

    let removes = document.getElementsByClassName("remove") // All remove-buttons
    Array.prototype.forEach.call(removes, function(remove) {
        remove.addEventListener("click", async function(e) { // Remove item
            e.currentTarget.parentNode.remove();

            var formData = new FormData();
            formData.append('action', "remove");
            formData.append('id', e.currentTarget.id);
            feedback = await callHandler(formData); // Inform backend and wait for feedback

            if (Object.keys(feedback).length == 0) { // Are there no items left? Disable send-button then
                let btn = document.getElementById("shoppingcart-submit");
                btn.setAttribute('disabled', "");
            }
        }, false);
    });

    let numbers = document.getElementsByClassName("number"); // All number input-fields
    Array.prototype.forEach.call(numbers, function(number) {
        number.addEventListener("change", async function(e) { // Count changed

            ids = e.currentTarget.parentNode.getElementsByClassName("hidden");

            let formData = new FormData();
            formData.append('action', "changeCount");
            formData.append('id', ids[0].value);
            formData.append('count', e.currentTarget.value);
            feedback = await callHandler(formData); // Inform backend and wait for feedback

            let numbers2 = document.getElementsByClassName("number");
            Array.prototype.forEach.call(numbers2, function(number2) { // Push feedback from backend to frontend. Under normal conditions there shouldn't be a change
                let itemExists = false;
                feedback.forEach(function(item) {
                    if (number2.id == item.id) {
                        number2.value = item.count;
                        itemExists = true;
                    }
                });

                let parent = number2.parentNode;
                if (!itemExists) { // Remove item if it doesn't exist
                    parent.remove();
                } else { // Else check if warning is present and still needed
                    let msgs = parent.getElementsByClassName("warning");
                    if (msgs.length == 1) { // Can only be 0 or 1
                        if (msgs[0].getAttribute('count') <= number2.value) {
                            msgs[0].remove();
                        }
                    }

                    msgs = document.getElementsByClassName("warning"); // Check if there are any warning on the whole page
                    if (msgs.length == 0) { // If not enable send-button again
                        let btn = document.getElementById("shoppingcart-submit");
                        btn.removeAttribute('disabled');
                    }
                }
            });

        }, false);
    });

    let starts = document.getElementsByClassName("start");
    Array.prototype.forEach.call(starts, function(start) { // Update enddate and endtime based one new startdate/-time
        start.addEventListener("change", function(e) {
            let start_date = document.getElementById("start_date");
            let start_time = document.getElementById("start_time");
            let end_date = document.getElementById("end_date");
            let end_time = document.getElementById("end_time");

            end_date.min = start_date.value; // Minimun enddate has to be startdate
            if (start_date.value == end_date.value) { // Same day?
                end_time.min = start_time.value; // So minimun endtime has to be starttime

                if (start_time.value > end_time.value) { // If starttime is more in future than endtime
                    end_time.value = start_time.value; // endtime gets set to starttime
                }

            } else {
                end_time.min = "";
            }

            if (start_date.value > end_date.value) { // If startdate is more in futire thand enddate
                end_date.value = start_date.value; // enddate gets set to startdate
            }

            updatedTimeSpan();

        }, false);
    });

    let ends = document.getElementsByClassName("end");
    Array.prototype.forEach.call(ends, function(end) { // Call updatedTimeSpan when enddate/-time changed
        end.addEventListener("change", function(e) {
            updatedTimeSpan();
        }, false);
    });

    updatedTimeSpan();
</script>

<?php
include("./includes/footer.inc.php");
?>