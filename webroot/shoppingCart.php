<?php
require("./includes/autoLoad.php");
require("includes/sessionChecker.php");
?>

<?php
$siteName = "Warenkorb";

// TODO: Implement shopping cart
$numberOfItems = 2;
include("./includes/header.inc.php");

?>

<label>Anlassname</label><br />
<input type="text" name="" />
<label>Abholdatum und -zeit</label><br />
<input class="start" type="date" id="start_date" name="trip-start" value="2020-11-30" min="2020-11-30" />
<input class="start" type="time" id="start_time" name="appt" step="1800" /><br />
<label>Zurückbringdatum und -zeit</label><br />
<input class="end" type="date" id="end_date" name="trip-start" value="2020-11-30" min="2020-11-30" />
<input class="start" type="time" id="end_time" name="appt" step="1800" /><br />

<?php
if (isset($_SESSION["shoppingCart"]) and is_array($_SESSION["shoppingCart"])) {
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
            echo '<input id="' . $row["idItem"] . '" name="number" type="number" min="1" max="20" value="' . $item["count"] . '" aria-label="Search" class="form-control mr-2 number" style="width: 100px">'; //TODO: Max
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
            }).then(response => response.json())
            .then(data => {
                res = data;
                console.log(res)
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
        //var feedback = Array.from(JSON.parse(callHandler(formData)));
        feedback = await callHandler(formData);

        let elements = document.getElementsByClassName('number');

        //Hier weiter
        feedback.forEach(function(item) {
            console.log("hi");
            elements.forEach((element) => {
                if (element.id == item.id) {
                    let target = element;
                }
            });
            target.setAttribute('max', item.max);
        });


    }

    var btn = document.getElementsByClassName('remove')

    for (var i = 0; i < btn.length; i++) {
        btn[i].addEventListener('click', function(e) {
            e.currentTarget.parentNode.remove();

            var formData = new FormData();
            formData.append('remove', '');
            formData.append('id', e.currentTarget.id);
            callHandler(formData);
        }, false);
    }

    var ipt = document.getElementsByClassName('number')

    for (var i = 0; i < ipt.length; i++) {
        ipt[i].addEventListener('change', function(e) {
            var formData = new FormData();
            formData.append('changeCount', '');
            formData.append('id', e.currentTarget.id);
            formData.append('count', e.currentTarget.value);
            callHandler(formData);
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
            end_time.min = start_time.value;

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